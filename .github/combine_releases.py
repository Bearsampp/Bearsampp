#!/usr/bin/env python3
import requests
import json
import os
import re
import sys
import traceback
from concurrent.futures import ThreadPoolExecutor, as_completed
from packaging import version  # For proper semver comparison
import time
from datetime import datetime

output_path = 'core/resources/quickpick-releases.json'

# GitHub repositories to fetch releases from
repos = [
    'Bearsampp/module-apache',
    'Bearsampp/module-bruno',
    'Bearsampp/module-composer',
    'Bearsampp/module-ghostscript',
    'Bearsampp/module-git',
    'Bearsampp/module-mailpit',
    'Bearsampp/module-mariadb',
    'Bearsampp/module-memcached',
    'Bearsampp/module-mysql',
    'Bearsampp/module-ngrok',
    'Bearsampp/module-nodejs',
    'Bearsampp/module-perl',
    'Bearsampp/module-php',
    'Bearsampp/module-phpmyadmin',
    'Bearsampp/module-phppgadmin',
    'Bearsampp/module-postgresql',
    'Bearsampp/module-powershell',
    'Bearsampp/module-python',
    'Bearsampp/module-ruby',
    'Bearsampp/module-xlight'
]

# Track statistics
stats = {
    'total_repos': len(repos),
    'processed_repos': 0,
    'failed_repos': [],
    'total_versions': 0
}

combined_data = []

# GitHub API headers - add token if you have one to increase rate limits
headers = {}
# Use GitHub token if available in environment variables
if os.environ.get('GH_PAT'):
    headers = {"Authorization": f"token {os.environ.get('GH_PAT')}"}
    print("Using GitHub PAT for authentication")
else:
    print("No GitHub PAT found, using unauthenticated requests")

# Shared session so HTTP connections (TLS/TCP) are reused across requests
http_session = requests.Session()

# Per-repo caches, populated once and reused by both the main loop and the
# validation pass so nothing is fetched more than once per run
releases_props_cache = {}  # repo -> [(version, url)] (successful fetches only)
release_list_cache = {}    # repo -> [release dicts] (successful fetches only)

MAX_WORKERS = 10


# Rate limiting helper
def make_api_request(url, headers):
    try:
        response = http_session.get(url, headers=headers, timeout=30)
        if response.status_code == 429:  # Rate limit exceeded
            reset_time = int(response.headers.get('X-RateLimit-Reset', 0))
            current_time = int(time.time())
            sleep_time = max(reset_time - current_time + 1, 1)
            print(f"Rate limit exceeded. Waiting for {sleep_time} seconds...")
            time.sleep(sleep_time)
            return make_api_request(url, headers)  # Retry after waiting
        return response
    except Exception as e:
        # Avoid flooding logs with traceback for common connection issues
        print(f"Error making API request to {url}: {e}")
        return None


# Helper function to normalize version strings for comparison
def normalize_version(version_str):
    try:
        # Handle versions like "13.2" vs "13.30" by padding with zeros
        parts = version_str.split('.')
        return '.'.join(part.zfill(3) for part in parts)
    except Exception as e:
        print(f"Error normalizing version {version_str}: {e}")
        return version_str


# Helper function to create a tuple for version comparison
def version_tuple(v):
    try:
        # Split the version string and convert components to integers for proper comparison
        # This handles cases where packaging.version might fail
        components = []
        for component in v.split('.'):
            try:
                components.append(int(component))
            except ValueError:
                # If component has non-numeric parts, keep it as is
                components.append(component)
        return tuple(components)
    except Exception as e:
        print(f"Error creating version tuple for {v}: {e}")
        return (0,)


# Helper function to extract version from asset name
def extract_version_from_asset(asset_name, module_short_name, tag_name):
    try:
        # Special case for Ngrok version 3 - only for the specific older asset
        if module_short_name == 'ngrok' and asset_name == 'bearsampp-ngrok-3-2022.07.14.7z':
            return '3.0'

        # Special case for assets with "neard-" prefix (legacy naming convention)
        if asset_name.startswith('neard-'):
            # Pattern: neard-{module}-{version}-r{release}.7z
            neard_pattern = f"neard-{module_short_name}-(\\d+(?:\\.\\d+)+)-r\\d+"
            neard_match = re.search(neard_pattern, asset_name)
            if neard_match:
                return neard_match.group(1)

        # General pattern for all modules: bearsampp-{module}-{version}-{date}.7z
        # The optional (?:-\d+)? captures a revision integer (e.g. the "1" in 4.0.2-1)
        # when present. The required -(\d{4}[\.-].*) suffix anchors to the date field
        # which usually starts with a 4-digit year followed by . or -
        standard_pattern = f"bearsampp-{module_short_name}-(\\d+(?:\\.\\d+)+(?:-\\d+)?)-(\\d{{4}}[\\.-].*)\\.7z"
        standard_match = re.search(standard_pattern, asset_name)
        if standard_match:
            return standard_match.group(1)

        # Try alternative pattern: bearsampp-{module}-{version}.7z (no date)
        alt_pattern = f"bearsampp-{module_short_name}-(\\d+(?:\\.\\d+)+)\\.7z"
        alt_match = re.search(alt_pattern, asset_name)
        if alt_match:
            return alt_match.group(1)

        # Handle non-standard prefixes (like phppgadmin7.13.0-2022.08.28.7z)
        # Try to match the module name directly at the start of the asset name
        nonstandard_pattern = f"{module_short_name}(\\d+(?:\\.\\d+)+)-"
        nonstandard_match = re.search(nonstandard_pattern, asset_name, re.IGNORECASE)
        if nonstandard_match:
            return nonstandard_match.group(1)

        # For more complex patterns, extract everything between module name and .7z
        base_pattern = f"bearsampp-{module_short_name}-(.+?)\\.7z"
        base_match = re.search(base_pattern, asset_name)

        if base_match:
            # Get everything between module name and .7z
            version_with_possible_suffix = base_match.group(1)

            # Special case for Ngrok version 3 - only for specific patterns
            if module_short_name == 'ngrok' and version_with_possible_suffix == '3':
                return '3.0'

            # Extract the version number from the string
            # First try to match X.Y.Z pattern
            version_match = re.search(r'(\d+\.\d+\.\d+)', version_with_possible_suffix)
            if version_match:
                return version_match.group(1)

            # Try to match X.Y pattern
            version_match = re.search(r'(\d+\.\d+)', version_with_possible_suffix)
            if version_match:
                return version_match.group(1)

            # If no version pattern found, use the whole string before the first hyphen
            if '-' in version_with_possible_suffix:
                return version_with_possible_suffix.split('-')[0]
            else:
                return version_with_possible_suffix

        # Try to extract version directly from the asset name if it contains a version pattern
        # First try X.Y.Z pattern
        version_match = re.search(r'(\d+\.\d+\.\d+)', asset_name)
        if version_match:
            return version_match.group(1)

        # Then try to find any version-like pattern in the asset name
        # This will catch cases like phppgadmin7.13.0-2022.08.28.7z
        version_match = re.search(r'(\d+(?:\.\d+)+)', asset_name)
        if version_match:
            return version_match.group(1)

        # If we get here, we couldn't extract a version using any pattern
        print(f"WARNING: Could not extract version from asset name: {asset_name}")
        return f"unknown-{module_short_name}"
    except Exception as e:
        print(f"Error extracting version from asset {asset_name}: {e}")
        traceback.print_exc()
        return f"unknown-{module_short_name}"


# Helper function to extract date from asset name or URL
def extract_date_from_asset(asset_name, asset_url, created_at):
    try:
        # Try to extract date from asset name (format: YYYY.MM.DD)
        date_match = re.search(r'(\d{4}\.\d{1,2}\.\d{1,2})', asset_name)
        if date_match:
            try:
                return datetime.strptime(date_match.group(1).replace('.', '-'), '%Y-%m-%d')
            except ValueError:
                pass

        # Try to extract date from asset name (format: YYYY-MM-DD)
        date_match = re.search(r'(\d{4}-\d{1,2}-\d{1,2})', asset_name)
        if date_match:
            try:
                return datetime.strptime(date_match.group(1), '%Y-%m-%d')
            except ValueError:
                pass

        # Try to extract date from URL
        date_match = re.search(r'/(\d{4}\.\d{1,2}\.\d{1,2})/', asset_url)
        if date_match:
            try:
                return datetime.strptime(date_match.group(1).replace('.', '-'), '%Y-%m-%d')
            except ValueError:
                pass

        # If no date in asset name or URL, use the release created_at date
        try:
            return datetime.strptime(created_at, '%Y-%m-%dT%H:%M:%SZ')
        except (ValueError, TypeError):
            # If all else fails, use current time (least preferred)
            return datetime.now()
    except Exception as e:
        print(f"Error extracting date from asset {asset_name}: {e}")
        return datetime.now()


# Fetch the full (paginated) list of releases for a repo, cached per run.
# Returns None if the first request fails, otherwise the list of releases.
def fetch_all_releases(owner, repo, headers, max_pages=50):
    cache_key = f"{owner}/{repo}"
    if cache_key in release_list_cache:
        return release_list_cache[cache_key]

    all_releases = []
    page = 1
    while page <= max_pages:
        url = f"https://api.github.com/repos/{owner}/{repo}/releases?per_page=100&page={page}"
        response = make_api_request(url, headers)
        if response is None or response.status_code != 200:
            if page == 1:
                return None
            break
        page_releases = response.json()
        if not page_releases:
            break
        all_releases.extend(page_releases)
        if len(page_releases) < 100:
            break
        page += 1

    release_list_cache[cache_key] = all_releases
    return all_releases


# Build a tag -> {prerelease, created_at} map from the release list
def build_tag_map(owner, repo, headers):
    releases = fetch_all_releases(owner, repo, headers)
    if not releases:
        return {}
    return {
        r.get('tag_name'): {'prerelease': r.get('prerelease', False), 'created_at': r.get('created_at')}
        for r in releases
        if r.get('tag_name')
    }


# Fetch and parse releases.properties, cached per run (successful fetches only)
def fetch_releases_properties(owner, repo, headers):
    cache_key = f"{owner}/{repo}"
    if cache_key in releases_props_cache:
        return releases_props_cache[cache_key]

    releases_props_url = f"https://raw.githubusercontent.com/{owner}/{repo}/main/releases.properties"
    response = make_api_request(releases_props_url, headers)
    if response is None or response.status_code != 200:
        return None

    # Parse releases.properties robustly: support both [version] = url and version = url
    matches = []
    for line in response.text.splitlines():
        line = line.strip()
        if not line or line.startswith('#') or line.startswith(';'):
            continue
        # Strip inline comments
        if '#' in line:
            line = line.split('#', 1)[0].strip()
        if ';' in line:
            line = line.split(';', 1)[0].strip()
        if '=' not in line:
            continue
        left, right = line.split('=', 1)
        version_key = left.strip().strip('[]').strip()
        url_value = right.strip()
        matches.append((version_key, url_value))

    releases_props_cache[cache_key] = matches
    return matches


# Process a single repo: returns (entry, num_versions, error)
def process_repo(repo_path):
    parts = repo_path.split('/')
    if len(parts) != 2:
        print(f"Skipping invalid repo path: {repo_path}")
        return None, 0, f"{repo_path} (Invalid path)"

    owner, repo = parts
    module_name = repo
    module_short_name = repo.replace('module-', '')

    # First, try to use releases.properties as authoritative source of versions
    matches = fetch_releases_properties(owner, repo, headers)
    if matches:
        print(f"Parsed {len(matches)} version entries from releases.properties for {repo}")

        # Single API call provides prerelease status for every tag (no per-version calls)
        tag_map = build_tag_map(owner, repo, headers)

        version_list = []
        for version_str, url_value in matches:
            vstr = version_str.strip()
            urlv = url_value.strip()

            # Determine prerelease status from the cached release list
            prerelease_status = False
            tag_match = re.search(r'/releases/download/([^/]+)/', urlv)
            if tag_match:
                tag_info = tag_map.get(tag_match.group(1))
                if tag_info:
                    prerelease_status = tag_info.get('prerelease', False)

            version_list.append({'version': vstr, 'url': urlv, 'prerelease': prerelease_status})

        # releases.properties acts as the authoritative list (drops old versions), but its entries
        # are not guaranteed to be ordered newest-first. Sort by version the same way the
        # GitHub Releases fallback path does, so the menu always shows newest versions on top.
        try:
            version_list.sort(key=lambda x: version.parse(x['version']), reverse=True)
        except Exception:
            try:
                version_list.sort(key=lambda x: version_tuple(x['version']), reverse=True)
            except Exception:
                version_list.sort(key=lambda x: normalize_version(x['version']), reverse=True)

        return {'module': module_name, 'versions': version_list}, len(version_list), None

    # Fallback: releases.properties not found — fall back to GitHub Releases API scanning
    print(f"releases.properties not found for {repo}, falling back to GitHub Releases API")
    releases = fetch_all_releases(owner, repo, headers)

    if releases is None:
        print(f"Failed to fetch releases for {repo_path}: No response")
        return None, 0, f"{repo_path} (No response)"

    # Dictionary to store the newest asset for each version
    version_assets = {}  # {version: (asset_data, date)}

    for release in releases:
        try:
            # Find .7z assets
            seven_z_assets = [asset for asset in release['assets'] if asset['name'].lower().endswith('.7z')]
            if not seven_z_assets:
                continue

            is_prerelease = release['prerelease']
            created_at = release.get('created_at')

            found_valid_asset = False
            for asset in seven_z_assets:
                try:
                    asset_url = asset['browser_download_url']
                    asset_name = asset['name']
                    version_number = extract_version_from_asset(asset_name, module_short_name, release['tag_name'])
                    if version_number.startswith('unknown-'):
                        continue
                    found_valid_asset = True
                    asset_date = extract_date_from_asset(asset_name, asset_url, created_at)

                    # Preference: Newer date wins. If dates equal, prefer stable over prerelease.
                    if version_number in version_assets:
                        existing_data, existing_date = version_assets[version_number]
                        new_is_prerelease = is_prerelease
                        existing_is_prerelease = existing_data['prerelease']

                        should_replace = False
                        if asset_date > existing_date:
                            should_replace = True
                        elif asset_date == existing_date and existing_is_prerelease and not new_is_prerelease:
                            should_replace = True

                        if should_replace:
                            version_assets[version_number] = ({'version': version_number, 'url': asset_url, 'prerelease': new_is_prerelease}, asset_date)
                    else:
                        version_assets[version_number] = ({'version': version_number, 'url': asset_url, 'prerelease': is_prerelease}, asset_date)
                except Exception as e:
                    print(f"Error processing asset {asset.get('name', 'unknown')}: {e}")
                    traceback.print_exc()
                    continue

            if not found_valid_asset:
                print(f"No valid .7z assets with version patterns found in release {release['tag_name']}")
        except Exception as e:
            print(f"Error processing release {release.get('tag_name', 'unknown')}: {e}")
            traceback.print_exc()
            continue

    # Extract just the asset data (without dates) for the final output
    version_data = [asset_data for asset_data, _ in version_assets.values()]
    version_data = [item for item in version_data if not item['version'].startswith('unknown-')]

    # Sort versions using packaging.version primarily
    try:
        version_data.sort(key=lambda x: version.parse(x['version']), reverse=True)
    except Exception:
        try:
            version_data.sort(key=lambda x: version_tuple(x['version']), reverse=True)
        except Exception:
            version_data.sort(key=lambda x: normalize_version(x['version']), reverse=True)

    return {'module': module_name, 'versions': version_data}, len(version_data), None


try:
    print("Starting release processing...")

    # Fetch and process all repositories in parallel (well within rate limits)
    results = {}
    with ThreadPoolExecutor(max_workers=MAX_WORKERS) as executor:
        futures = {executor.submit(process_repo, repo_path): repo_path for repo_path in repos}
        for future in as_completed(futures):
            repo_path = futures[future]
            try:
                results[repo_path] = future.result()
            except Exception as e:
                print(f"Error processing repo {repo_path}: {e}")
                traceback.print_exc()
                results[repo_path] = (None, 0, f"{repo_path} (Error: {str(e)})")

    # Aggregate results in repo order
    for repo_path in repos:
        entry, num_versions, error = results[repo_path]
        if error:
            stats['failed_repos'].append(error)
            continue
        combined_data.append(entry)
        stats['processed_repos'] += 1
        stats['total_versions'] += num_versions

    print("Release processing completed")
    print(f"Summary: Processed {stats['processed_repos']}/{stats['total_repos']} repositories")
    print(f"Total versions found: {stats['total_versions']}")
    if stats['failed_repos']:
        print(f"Failed repositories: {', '.join(stats['failed_repos'])}")

except Exception as e:
    print(f"Error during release processing: {e}")
    traceback.print_exc()


# Validation step: Override with releases.properties if it has different URLs
print("\n" + "="*80)
print("VALIDATING AGAINST releases.properties")
print("="*80 + "\n")

for module_entry in combined_data:
    module_name = module_entry['module']
    # module_name is already in format "module-{shortname}", extract the shortname
    module_shortname = module_name.replace('module-', '')
    repo_path = f"Bearsampp/module-{module_shortname}"
    parts = repo_path.split('/')
    owner, repo = parts

    print(f"\nProcessing module: {module_name} (repo: {repo})")
    print(f"  Current versions in JSON: {[v['version'] for v in module_entry['versions']]}")

    # releases.properties is already cached from the main loop when it exists
    matches = fetch_releases_properties(owner, repo, headers)
    if not matches:
        # releases.properties doesn't exist for this module, skip silently
        continue

    print(f"  Parsed {len(matches)} version entries from releases.properties")

    # Build a map of version -> URL from releases.properties
    releases_props_map = {}
    for version_str, url_value in matches:
        releases_props_map[version_str.strip()] = url_value.strip()

    # Reuse the cached release list for any prerelease lookups below
    tag_map = build_tag_map(owner, repo, headers)

    print(f"JSON has {len(module_entry['versions'])} versions")

    # Check each version in our combined_data
    for version_entry in module_entry['versions']:
        version_num = version_entry['version']
        current_url = version_entry['url']

        # If releases.properties has a different URL for this version, use it
        if version_num in releases_props_map:
            releases_props_url_for_version = releases_props_map[version_num]
            if current_url != releases_props_url_for_version:
                print(f"  {version_num}: Updating URL from GitHub API to releases.properties version")
                print(f"    Old: {current_url}")
                print(f"    New: {releases_props_url_for_version}")
                version_entry['url'] = releases_props_url_for_version

                # Get the correct prerelease status for the new URL
                tag_match = re.search(r'/releases/download/([^/]+)/', releases_props_url_for_version)
                if tag_match:
                    tag_info = tag_map.get(tag_match.group(1))
                    if tag_info is not None:
                        version_entry['prerelease'] = tag_info.get('prerelease', False)
                    else:
                        print(f"    Could not determine prerelease status, keeping: {version_entry['prerelease']}")
        else:
            print(f"    NOT in releases.properties (only in GitHub API)")

print("\n" + "="*80)
print("VALIDATION COMPLETED - About to write JSON file")
print("="*80 + "\n")

# Show what's about to be written for mysql module
for entry in combined_data:
    if entry['module'] == 'module-mysql':
        print("MySQL versions before writing:")
        for v in entry['versions'][:5]:  # Show first 5
            print(f"  {v['version']}: {v['url'][:70]}... (prerelease: {v['prerelease']})")
        break

# Write the file
print(f"\nWriting output to {output_path}")
try:
    # Ensure output directory exists
    outdir = os.path.dirname(output_path)
    if outdir:
        os.makedirs(outdir, exist_ok=True)
    with open(output_path, 'w') as f:
        json.dump(combined_data, f, indent=2)
    print(f"Successfully saved combined release data to {output_path}")
    print(f"File size: {os.path.getsize(output_path)} bytes")
except Exception as e:
    print(f"Error writing to {output_path}: {e}")
    traceback.print_exc()
    # Create an empty file if writing fails
    try:
        outdir = os.path.dirname(output_path)
        if outdir:
            os.makedirs(outdir, exist_ok=True)
        with open(output_path, 'w') as f:
            json.dump([], f, indent=2)
        print(f"Created empty {output_path} due to error")
    except Exception as e2:
        print(f"Failed to create {output_path}: {e2}")
        traceback.print_exc()
        sys.exit(1)

print("Script completed successfully")
