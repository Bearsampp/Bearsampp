#!/usr/bin/env python3

import requests
import json
import os
import sys
import traceback
import re
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

def fetch_releases_properties(owner, repo):
    """Fetch releases.properties from the module repo and parse versions.

    Returns a list of dicts: [{version, url}]
    """
    try:
        url = f"https://raw.githubusercontent.com/{owner}/{repo}/main/releases.properties"
        print(f"  📥 Fetching releases.properties from {repo}")
        response = requests.get(url, headers=headers, timeout=30)

        if response.status_code == 200:
            properties_content = response.text
            releases_data = []

            # Parse releases.properties file
            # Format: [version] = [URL]
            pattern = r'\[\s*([^\]]+)\s*\]\s*=\s*([^\n]+)'
            matches = re.findall(pattern, properties_content)

            for version_str, url_value in matches:
                version_str = version_str.strip()
                url_value = url_value.strip()

                if version_str and url_value:
                    releases_data.append({
                        'version': version_str,
                        'url': url_value
                    })

            print(f"  ✓ Found {len(releases_data)} versions in releases.properties")
            return releases_data
        elif response.status_code == 404:
            print(f"  ⚠ releases.properties not found for {repo}")
            return []
        else:
            print(f"  ⚠ Request failed for {repo} (HTTP {response.status_code})")
            return []
    except Exception as e:
        print(f"  ❌ Error fetching releases.properties for {repo}: {e}")
        return []

def check_if_prerelease(owner, repo, url):
    """Check if a release is a prerelease by querying GitHub API.

    Args:
        owner: GitHub owner (e.g., 'Bearsampp')
        repo: GitHub repo (e.g., 'module-mysql')
        url: The download URL to find the corresponding release

    Returns:
        bool: True if prerelease, False if stable release
    """
    try:
        # Extract release tag from URL
        # Format: https://github.com/owner/repo/releases/download/TAG/filename
        match = re.search(r'/releases/download/([^/]+)/', url)
        if not match:
            print(f"    ⚠ Could not extract tag from URL: {url}")
            return False

        tag = match.group(1)

        # Query GitHub API for this release
        api_url = f"https://api.github.com/repos/{owner}/{repo}/releases/tags/{tag}"
        response = requests.get(api_url, headers=headers, timeout=30)

        if response.status_code == 200:
            release_data = response.json()
            return release_data.get('prerelease', False)
        else:
            print(f"    ⚠ Could not find release info for tag: {tag}")
            return False
    except Exception as e:
        print(f"    ⚠ Error checking prerelease status: {e}")
        return False

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

            # Fetch releases from releases.properties file
            releases_data = fetch_releases_properties(owner, repo)

            if not releases_data:
                print(f"  ⚠ No versions found in releases.properties, skipping {module_name}")
                continue

            # Check prerelease status for each version and sort
            versions_data = []
            for release in releases_data:
                prerelease = check_if_prerelease(owner, repo, release['url'])
                clean_release = {
                    'version': release['version'],
                    'url': release['url'],
                    'prerelease': prerelease
                }
                versions_data.append(clean_release)
                prerelease_label = " (prerelease)" if prerelease else ""
                print(f"  ✓ {release['version']}{prerelease_label}")

            # Sort by version (newest first)
            versions_data = sorted(versions_data,
                                   key=lambda x: version_sort_key(x['version']),
                                   reverse=True)

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
