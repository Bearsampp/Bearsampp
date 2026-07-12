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

def fetch_releases_properties(owner, repo):
    """Fetch and parse releases.properties from module repo.

    Returns a dict: {version: url}
    """
    try:
        url = f"https://raw.githubusercontent.com/{owner}/{repo}/main/releases.properties"
        print(f"  📥 Fetching releases.properties from {repo}")
        response = requests.get(url, headers=headers, timeout=30)

        if response.status_code == 200:
            content = sanitize_string(response.text)
            properties = {}

            for line in content.splitlines():
                line = line.strip()
                if line and '=' in line and not line.startswith('#'):
                    parts = line.split('=', 1)
                    if len(parts) == 2:
                        version_key = parts[0].strip()
                        url_value = parts[1].strip()
                        properties[version_key] = url_value

            print(f"  ✓ Found {len(properties)} versions in releases.properties")
            return properties
        else:
            print(f"  ⚠ releases.properties not found in {repo} (HTTP {response.status_code})")
            return {}
    except Exception as e:
        print(f"  ❌ Error fetching releases.properties for {repo}: {e}")
        return {}

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

            # PRIMARY SOURCE: Load releases.properties (version -> URL mapping)
            properties = fetch_releases_properties(owner, repo)

            if not properties:
                print(f"  ⚠ No versions found in releases.properties, skipping {module_name}")
                continue

            # Convert to the required JSON format
            # Sort by version (newest first)
            sorted_versions = sorted(properties.items(),
                                    key=lambda x: version_sort_key(x[0]),
                                    reverse=True)

            versions_data = []
            for ver, url in sorted_versions:
                versions_data.append({
                    'version': ver,
                    'url': url,
                    'prerelease': False
                })
                print(f"  ✓ {ver}: {url}")

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
