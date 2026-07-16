#!/usr/bin/env python3
import requests
import json
import os
import re
import sys
import traceback
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

# Rate limiting helper
def make_api_request(url, headers):
    try:
        response = requests.get(url, headers=headers, timeout=30)
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
        print(f"Extracting version from asset: {asset_name} for module: {module_short_name}")

        # Special case for Ngrok version 3 - only for the specific older asset
        if module_short_name == 'ngrok' and asset_name == 'bearsampp-ngrok-3-2022.07.14.7z':
            print(f"Applying special case for specific Ngrok asset: setting version to 3.0")
            return '3.0'

        # Special case for assets with "neard-" prefix (legacy naming convention)
        if asset_name.startswith('neard-'):
            # Pattern: neard-{module}-{version}-r{release}.7z
            neard_pattern = f"neard-{module_short_name}-(\\d+(?:\\.\\d+)+)-r\\d+"
            neard_match = re.search(neard_pattern, asset_name)
            if neard_match:
                version_number = neard_match.group(1)
                print(f"Extracted version {version_number} from neard-prefixed asset {asset_name}")
                return version_number

        # General pattern for all modules: bearsampp-{module}-{version}-{date}.7z
        # The optional (?:-\d+)? captures a revision integer (e.g. the "1" in 4.0.2-1)
        # when present. The required -(\d{4}[\.-].*) suffix anchors to the date field
        # which usually starts with a 4-digit year followed by . or -
        standard_pattern = f"bearsampp-{module_short_name}-(\\d+(?:\\.\\d+)+(?:-\\d+)?)-(\\d{{4}}[\\.-].*)\\.7z"
        print(f"Trying standard pattern: {standard_pattern}")
        standard_match = re.search(standard_pattern, asset_name)
        if standard_match:
            version_number = standard_match.group(1)
            print(f"Extracted version {version_number} from asset {asset_name} using standard pattern")
            return version_number

        # Try alternative pattern: bearsampp-{module}-{version}.7z (no date)
        alt_pattern = f"bearsampp-{module_short_name}-(\\d+(?:\\.\\d+)+)\\.7z"
        print(f"Trying alternative pattern: {alt_pattern}")
        alt_match = re.search(alt_pattern, asset_name)
        if alt_match:
            version_number = alt_match.group(1)
            print(f"Extracted version {version_number} from asset {asset_name} using alternative pattern")
            return version_number

        # Handle non-standard prefixes (like phppgadmin7.13.0-2022.08.28.7z)
        # Try to match the module name directly at the start of the asset name
        nonstandard_pattern = f"{module_short_name}(\\d+(?:\\.\\d+)+)-"
        print(f"Trying non-standard pattern: {nonstandard_pattern}")
        nonstandard_match = re.search(nonstandard_pattern, asset_name, re.IGNORECASE)
        if nonstandard_match:
            version_number = nonstandard_match.group(1)
            print(f"Extracted version {version_number} from non-standard asset {asset_name}")
            return version_number

        # For more complex patterns, extract everything between module name and .7z
        base_pattern = f"bearsampp-{module_short_name}-(.+?)\\.7z"
        print(f"Trying base pattern: {base_pattern}")
        base_match = re.search(base_pattern, asset_name)

        if base_match:
            # Get everything between module name and .7z
            version_with_possible_suffix = base_match.group(1)
            print(f"Found content between module name and .7z: {version_with_possible_suffix}")

            # Special case for Ngrok version 3 - only for specific patterns
            if module_short_name == 'ngrok' and version_with_possible_suffix == '3':
                print(f"Applying special case for Ngrok version 3: setting version to 3.0")
                return '3.0'

            # Extract the version number from the string
            # First try to match X.Y.Z pattern
            version_match = re.search(r'(\d+\.\d+\.\d+)', version_with_possible_suffix)
            if version_match:
                version_number = version_match.group(1)
                print(f"Extracted version {version_number} from asset {asset_name} using X.Y.Z pattern")
                return version_number

            # Try to match X.Y pattern
            version_match = re.search(r'(\d+\.\d+)', version_with_possible_suffix)
            if version_match:
                version_number = version_match.group(1)
                print(f"Extracted version {version_number} from asset {asset_name} using X.Y pattern")
                return version_number

            # If no version pattern found, use the whole string before the first hyphen
            if '-' in version_with_possible_suffix:
                version_number = version_with_possible_suffix.split('-')[0]
                print(f"Extracted version {version_number} from asset {asset_name} by splitting at hyphen")
                return version_number
            else:
                version_number = version_with_possible_suffix
                print(f"Using entire string {version_number} as version from asset {asset_name}")
                return version_number

        # Try to extract version directly from the asset name if it contains a version pattern
        # First try X.Y.Z pattern
        print("Trying direct version extraction from asset name")
        version_match = re.search(r'(\d+\.\d+\.\d+)', asset_name)
        if version_match:
            version_number = version_match.group(1)
            print(f"Extracted version {version_number} directly from asset name {asset_name} using X.Y.Z pattern")
            return version_number

        # Then try to find any version-like pattern in the asset name
        # This will catch cases like phppgadmin7.13.0-2022.08.28.7z
        version_match = re.search(r'(\d+(?:\.\d+)+)', asset_name)
        if version_match:
            version_number = version_match.group(1)
            print(f"Extracted version {version_number} directly from asset name {asset_name} using generic version pattern")
            return version_number

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
        print(f"Extracting date from asset: {asset_name}")

        # Try to extract date from asset name (format: YYYY.MM.DD)
        date_match = re.search(r'(\d{4}\.\d{1,2}\.\d{1,2})', asset_name)
        if date_match:
            try:
                date_str = date_match.group(1)
                # Convert dots to dashes for datetime parsing
                date_str = date_str.replace('.', '-')
                date_obj = datetime.strptime(date_str, '%Y-%m-%d')
                print(f"Extracted date {date_obj} from asset name using YYYY.MM.DD pattern")
                return date_obj
            except ValueError as e:
                print(f"Failed to parse date from {date_str}: {e}")

        # Try to extract date from asset name (format: YYYY.M.D)
        date_match = re.search(r'(\d{4})\.(\d{1,2})\.(\d{1,2})', asset_name)
        if date_match:
            try:
                year, month, day = date_match.groups()
                date_obj = datetime(int(year), int(month), int(day))
                print(f"Extracted date {date_obj} from asset name using YYYY.M.D pattern")
                return date_obj
            except ValueError as e:
                print(f"Failed to parse date from {year}.{month}.{day}: {e}")

        # Try to extract date from asset name (format: YYYY-MM-DD)
        date_match = re.search(r'(\d{4}-\d{1,2}-\d{1,2})', asset_name)
        if date_match:
            try:
                date_str = date_match.group(1)
                date_obj = datetime.strptime(date_str, '%Y-%m-%d')
                print(f"Extracted date {date_obj} from asset name using YYYY-MM-DD pattern")
                return date_obj
            except ValueError as e:
                print(f"Failed to parse date from {date_str}: {e}")

        # Try to extract date from URL
        date_match = re.search(r'/(\d{4}\.\d{1,2}\.\d{1,2})/', asset_url)
        if date_match:
            try:
                date_str = date_match.group(1)
                # Convert dots to dashes for datetime parsing
                date_str = date_str.replace('.', '-')
                date_obj = datetime.strptime(date_str, '%Y-%m-%d')
                print(f"Extracted date {date_obj} from URL")
                return date_obj
            except ValueError as e:
                print(f"Failed to parse date from URL {date_str}: {e}")

        # If no date in asset name or URL, use the release created_at date
        try:
            date_obj = datetime.strptime(created_at, '%Y-%m-%dT%H:%M:%SZ')
            print(f"Using release date {date_obj} from created_at")
            return date_obj
        except (ValueError, TypeError) as e:
            print(f"Failed to parse created_at date {created_at}: {e}")
            # If all else fails, use current time (least preferred)
            date_obj = datetime.now()
            print(f"Using current time {date_obj} as fallback")
            return date_obj
    except Exception as e:
        print(f"Error extracting date from asset {asset_name}: {e}")
        traceback.print_exc()
        return datetime.now()

try:
    print("Starting release processing...")
    for repo_path in repos:
        try:
            # Split the repo path into owner and repo
            parts = repo_path.split('/')
            if len(parts) != 2:
                print(f"Skipping invalid repo path: {repo_path}")
                continue

            owner, repo = parts
            module_name = repo
            module_short_name = repo.replace('module-', '')

            # First, try to use releases.properties as authoritative source of versions
            releases_props_url = f"https://raw.githubusercontent.com/{owner}/{repo}/main/releases.properties"
            print(f"Attempting to fetch releases.properties for {repo}: {releases_props_url}")
            rp_response = make_api_request(releases_props_url, headers)

            if rp_response and rp_response.status_code == 200:
                properties_content = rp_response.text
                # Parse releases.properties robustly: support both [version] = url and version = url
                matches = []
                for line in properties_content.splitlines():
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
                print(f"  Parsed {len(matches)} version entries from releases.properties for {repo}")

                version_list = []
                for version_str, url_value in matches:
                    vstr = version_str.strip()
                    urlv = url_value.strip()

                    # Determine prerelease status by querying the release tag (if possible)
                    prerelease_status = None
                    created_dt = None
                    tag_match = re.search(r'/releases/download/([^/]+)/', urlv)
                    if tag_match:
                        tag = tag_match.group(1)
                        api_url = f"https://api.github.com/repos/{owner}/{repo}/releases/tags/{tag}"
                        api_resp = make_api_request(api_url, headers)
                        if api_resp and api_resp.status_code == 200:
                            rd = api_resp.json()
                            prerelease_status = rd.get('prerelease', None)
                            created_at = rd.get('created_at', None)
                            if created_at:
                                try:
                                    created_dt = datetime.strptime(created_at, '%Y-%m-%dT%H:%M:%SZ')
                                except Exception:
                                    created_dt = None

                    # Default prerelease to False if unknown
                    if prerelease_status is None:
                        prerelease_status = False

                    version_list.append({'version': vstr, 'url': urlv, 'prerelease': prerelease_status})

                # Use releases.properties list directly (it acts as the authoritative list and drops old versions)
                combined_data.append({'module': module_name, 'versions': version_list})
                stats['processed_repos'] += 1
                stats['total_versions'] += len(version_list)
                continue

            # Fallback: releases.properties not found — fall back to GitHub Releases API scanning
            print(f"releases.properties not found for {repo}, falling back to GitHub Releases API")
            api_url = f"https://api.github.com/repos/{owner}/{repo}/releases"
            response = make_api_request(api_url, headers)

            if response is None:
                print(f"Failed to fetch releases for {repo_path}: No response")
                stats['failed_repos'].append(f"{repo_path} (No response)")
                continue

            if response.status_code == 200:
                releases = response.json()

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

                combined_data.append({'module': module_name, 'versions': version_data})
                stats['processed_repos'] += 1
                stats['total_versions'] += len(version_data)
            else:
                print(f"Failed to fetch releases for {repo_path}: {response.status_code}")
                stats['failed_repos'].append(f"{repo_path} (HTTP {response.status_code})")
        except Exception as e:
            print(f"Error processing repo {repo_path}: {e}")
            traceback.print_exc()
            stats['failed_repos'].append(f"{repo_path} (Error: {str(e)})")
            continue

    print("Release processing completed")
    print(f"Summary: Processed {stats['processed_repos']}/{stats['total_repos']} repositories")
    print(f"Total versions found: {stats['total_versions']}")
    if stats['failed_repos']:
        print(f"Failed repositories: {', '.join(stats['failed_repos'])}")

except Exception as e:
    print(f"Error during release processing: {e}")
    traceback.print_exc()

def get_prerelease_status_from_url(owner, repo, url):
    """Extract release tag from URL and check if it's a prerelease on GitHub."""
    try:
        # Extract release tag from URL: /releases/download/TAG/filename
        match = re.search(r'/releases/download/([^/]+)/', url)
        if not match:
            print(f"    Could not extract tag from URL: {url}")
            return None

        tag = match.group(1)
        print(f"    Checking prerelease status for tag: {tag}")

        # Query GitHub API for this release
        api_url = f"https://api.github.com/repos/{owner}/{repo}/releases/tags/{tag}"
        response = make_api_request(api_url, headers)

        if response and response.status_code == 200:
            release_data = response.json()
            prerelease_status = release_data.get('prerelease', False)
            print(f"    Release {tag} prerelease status: {prerelease_status}")
            return prerelease_status
        else:
            print(f"    Could not find release info for tag: {tag}")
            return None
    except Exception as e:
        print(f"    Error checking prerelease status: {e}")
        return None

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

    # Try to fetch releases.properties for this module
    releases_props_url = f"https://raw.githubusercontent.com/{owner}/{repo}/main/releases.properties"
    print(f"  Fetching releases.properties from: {releases_props_url}")
    try:
        print(f"  Attempting to fetch: {releases_props_url}")
        response = requests.get(releases_props_url, headers=headers, timeout=30)
        print(f"  Response status: {response.status_code}")
        if response.status_code == 200:
            properties_content = response.text

            # Parse releases.properties file: support both [version] = [URL] and version = URL formats
            matches = []
            for line in properties_content.splitlines():
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

            print(f"  Parsed {len(matches)} version entries from releases.properties")

            if matches:
                print(f"Successfully parsed {len(matches)} versions from releases.properties")
                print(f"Validating {module_name} against releases.properties ({len(matches)} versions found)")

                # Build a map of version -> URL from releases.properties
                releases_props_map = {}
                for version_str, url_value in matches:
                    version_str = version_str.strip()
                    url_value = url_value.strip()
                    releases_props_map[version_str] = url_value
                    print(f"  releases.properties: {version_str} = {url_value[:80]}...")

                print(f"JSON has {len(module_entry['versions'])} versions")

                # Check each version in our combined_data
                for version_entry in module_entry['versions']:
                    version_num = version_entry['version']
                    current_url = version_entry['url']

                    print(f"  Checking version: {version_num}")

                    # If releases.properties has a different URL for this version, use it
                    if version_num in releases_props_map:
                        print(f"    Found in releases.properties ✓")
                        releases_props_url_for_version = releases_props_map[version_num]
                        print(f"    Current JSON URL:     {current_url}")
                        print(f"    releases.properties:  {releases_props_url_for_version}")
                        if current_url != releases_props_url_for_version:
                            print(f"  {version_num}: Updating URL from GitHub API to releases.properties version")
                            print(f"    Old: {current_url}")
                            print(f"    New: {releases_props_url_for_version}")
                            version_entry['url'] = releases_props_url_for_version

                            # Get the correct prerelease status for the new URL
                            prerelease_status = get_prerelease_status_from_url(owner, repo, releases_props_url_for_version)
                            if prerelease_status is not None:
                                old_prerelease = version_entry['prerelease']
                                version_entry['prerelease'] = prerelease_status
                                if old_prerelease != prerelease_status:
                                    print(f"    Prerelease status: {old_prerelease} → {prerelease_status}")
                                else:
                                    print(f"    Prerelease status: {prerelease_status} (unchanged)")
                            else:
                                print(f"    Could not determine prerelease status, keeping: {version_entry['prerelease']}")
                        else:
                            print(f"    URLs match ✓")
                    else:
                        print(f"    NOT in releases.properties (only in GitHub API)")
        elif response.status_code == 404:
            pass  # releases.properties doesn't exist for this module, skip silently
        else:
            print(f"Warning: Could not fetch releases.properties for {module_name} (HTTP {response.status_code})")
    except Exception as e:
        print(f"Warning: Error validating {module_name} against releases.properties: {e}")

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
