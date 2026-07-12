#!/usr/bin/env python3

import requests
import json
import os
import sys
import traceback
from packaging import version

output_path = 'core/resources/quickpick-releases.json'

repos = [
    'Bearsampp/module-adminer',
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
    'Bearsampp/module-svn',
    'Bearsampp/module-xlight'
]

headers = {}
if os.environ.get('GH_PAT'):
    headers = {"Authorization": f"token {os.environ.get('GH_PAT')}"}
    print("✓ Using GitHub PAT for authentication")
else:
    print("⚠ No GitHub PAT found, using unauthenticated requests")

combined_data = []

def sanitize_string(s):
    """Remove null bytes and problematic characters."""
    if s is None:
        return None
    if isinstance(s, str):
        s = s.replace('\0', '').replace('\\0', '').replace('\\u0000', '')
        return "".join(ch for ch in s if ord(ch) >= 32 or ch in '\n\r')
    return s

def fetch_releases_from_api(owner, repo):
    """Fetch releases from GitHub API and extract version, URL, and prerelease status.

    Returns a list of dicts: [{version, url, prerelease}]
    """
    try:
        url = f"https://api.github.com/repos/{owner}/{repo}/releases?per_page=100"
        print(f"  📥 Fetching releases from GitHub API for {repo}")
        response = requests.get(url, headers=headers, timeout=30)

        if response.status_code == 200:
            releases = response.json()
            releases_data = []

            for release in releases:
                # Skip draft releases
                if release.get('draft', False):
                    continue

                version = release.get('tag_name', '').lstrip('v')
                url_value = None
                prerelease = release.get('prerelease', False)
                release_date = release.get('published_at', '')

                # Find the asset URL (usually a .7z or .zip file)
                if release.get('assets'):
                    for asset in release['assets']:
                        if asset['name'].endswith(('.7z', '.zip')):
                            url_value = asset['browser_download_url']
                            break

                if version and url_value:
                    releases_data.append({
                        'version': version,
                        'url': url_value,
                        'prerelease': prerelease,
                        'release_date': release_date
                    })

            print(f"  ✓ Found {len(releases_data)} versions from GitHub API")
            return releases_data
        else:
            print(f"  ⚠ GitHub API request failed for {repo} (HTTP {response.status_code})")
            return []
    except Exception as e:
        print(f"  ❌ Error fetching from GitHub API for {repo}: {e}")
        return []

def version_sort_key(v):
    """Sort key for semantic versioning."""
    try:
        return version.parse(v)
    except Exception:
        return version.parse("0.0.0")

try:
    print("\n" + "="*80)
    print("BEARSAMPP COMBINE RELEASES - Processing Module Repositories")
    print("="*80 + "\n")

    for repo_path in repos:
        try:
            parts = repo_path.split('/')
            if len(parts) != 2:
                print(f"⚠ Skipping invalid repo path: {repo_path}")
                continue

            owner, repo = parts
            module_name = repo.replace('module-', '')

            print(f"\n📦 Processing: {module_name}")
            print("-" * 80)

            # Fetch releases from GitHub API (includes prerelease status)
            releases_data = fetch_releases_from_api(owner, repo)

            if not releases_data:
                print(f"  ⚠ No versions found from GitHub API, skipping {module_name}")
                continue

            # Deduplicate: keep only the newest release for each version
            version_map = {}
            for release in releases_data:
                ver = release['version']
                if ver not in version_map:
                    version_map[ver] = release
                else:
                    # Compare release dates and keep the newer one
                    current_date = release.get('release_date', '')
                    existing_date = version_map[ver].get('release_date', '')
                    if current_date > existing_date:
                        version_map[ver] = release

            # Sort by version (newest first)
            sorted_versions = sorted(version_map.values(),
                                    key=lambda x: version_sort_key(x['version']),
                                    reverse=True)

            versions_data = []
            for release in sorted_versions:
                # Don't include release_date in final output
                clean_release = {
                    'version': release['version'],
                    'url': release['url'],
                    'prerelease': release['prerelease']
                }
                versions_data.append(clean_release)
                prerelease_label = " (prerelease)" if release['prerelease'] else ""
                print(f"  ✓ {release['version']}: {release['url']}{prerelease_label}")

            combined_data.append({
                'module': module_name,
                'versions': versions_data
            })

            print(f"  📊 Total versions: {len(versions_data)}")

        except Exception as e:
            print(f"  ❌ Error processing {repo_path}: {e}")
            traceback.print_exc()
            continue

    print("\n" + "="*80)
    print("WRITING OUTPUT")
    print("="*80 + "\n")

    # Write the combined data to JSON
    try:
        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(combined_data, f, indent=2)

        file_size = os.path.getsize(output_path)
        print(f"✓ Successfully wrote {output_path}")
        print(f"  File size: {file_size} bytes")
        print(f"  Modules: {len(combined_data)}")

        total_versions = sum(len(m['versions']) for m in combined_data)
        print(f"  Total versions: {total_versions}")

    except Exception as e:
        print(f"❌ Error writing {output_path}: {e}")
        traceback.print_exc()
        sys.exit(1)

except Exception as e:
    print(f"\n❌ FATAL ERROR: {e}")
    traceback.print_exc()
    sys.exit(1)

print("\n" + "="*80)
print("✓ SCRIPT COMPLETED SUCCESSFULLY")
print("="*80 + "\n")
