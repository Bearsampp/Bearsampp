import requests
import json
import os
import re
from packaging import version  # For proper semver comparison
import time

# GitHub repositories to fetch releases from
repos = [
    'Bearsampp/module-adminer',
    'Bearsampp/module-apache',
    'Bearsampp/module-bruno',
    'Bearsampp/module-composer',
    'Bearsampp/module-consolez',
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
    'Bearsampp/module-python',
    'Bearsampp/module-ruby',
    'Bearsampp/module-xlight'
]

combined_data = []

# GitHub API headers - add token if you have one to increase rate limits
headers = {}
# Use GitHub token if available in environment variables
if os.environ.get('GH_PAT'):
    headers = {"Authorization": f"token {os.environ.get('GH_PAT')}"}

# Rate limiting helper
def make_api_request(url, headers):
    response = requests.get(url, headers=headers)
    if response.status_code == 429:  # Rate limit exceeded
        reset_time = int(response.headers.get('X-RateLimit-Reset', 0))
        current_time = int(time.time())
        sleep_time = max(reset_time - current_time + 1, 1)
        print(f"Rate limit exceeded. Waiting for {sleep_time} seconds...")
        time.sleep(sleep_time)
        return make_api_request(url, headers)  # Retry after waiting
    return response

# Helper function to normalize version strings for comparison
def normalize_version(version_str):
    # Handle versions like "13.2" vs "13.30" by padding with zeros
    parts = version_str.split('.')
    return '.'.join(part.zfill(3) for part in parts)

# Helper function to extract version from asset name
def extract_version_from_asset(asset_name, module_short_name, tag_name):
    # Pattern: bearsampp-{module}-{version}.7z or bearsampp-{module}-{version}-{anything}.7z
    base_pattern = f"bearsampp-{module_short_name}-(.+)\\.7z"
    base_match = re.search(base_pattern, asset_name)
    
    if base_match:
        # Get everything between module name and .7z
        version_with_possible_suffix = base_match.group(1)
        
        # Remove everything after the last hyphen (if there is one)
        if '-' in version_with_possible_suffix:
            version_number = version_with_possible_suffix.rsplit('-', 1)[0]
        else:
            version_number = version_with_possible_suffix
    else:
        # Fallback to tag name if pattern doesn't match
        version_number = tag_name
        print(f"Warning: Could not extract version from asset name for {asset_name}, using tag name instead")
    
    return version_number

for repo_path in repos:
    # Split the repo path into owner and repo
    parts = repo_path.split('/')
    if len(parts) != 2:
        print(f"Skipping invalid repo path: {repo_path}")
        continue

    owner, repo = parts

    # Use GitHub API to fetch releases
    api_url = f"https://api.github.com/repos/{owner}/{repo}/releases"
    response = make_api_request(api_url, headers)

    if response.status_code == 200:
        releases = response.json()
        module_name = repo
        version_data = []

        for release in releases:
            # Find .7z assets
            seven_z_assets = [asset for asset in release['assets']
                              if asset['name'].lower().endswith('.7z')]

            # Skip releases without .7z assets
            if not seven_z_assets:
                print(f"Skipping release {release['tag_name']} in {repo} - no .7z assets found")
                continue

            # Process all .7z assets in this release
            module_short_name = repo.replace('module-', '')
            is_prerelease = release['prerelease']
            
            for asset in seven_z_assets:
                asset_url = asset['browser_download_url']
                asset_name = asset['name']
                
                # Extract version from the asset name
                version_number = extract_version_from_asset(asset_name, module_short_name, release['tag_name'])
                
                # Check if this version already exists in version_data
                if not any(v['version'] == version_number and v['url'] == asset_url for v in version_data):
                    version_data.append({
                        'version': version_number,
                        'url': asset_url,
                        'prerelease': is_prerelease
                    })
                    print(f"Added {module_name} version {version_number} from asset {asset_name}")
                else:
                    print(f"Skipping duplicate {module_name} version {version_number} from asset {asset_name}")
        
        # Sort versions using semantic versioning
        try:
            # First attempt: Use packaging.version for proper semver sorting
            version_data.sort(key=lambda x: version.parse(x['version']), reverse=True)
        except Exception as e:
            print(f"Warning: Could not sort versions for {repo} using semver: {e}")
            # Fallback: Use string normalization for simple version comparison
            version_data.sort(key=lambda x: normalize_version(x['version']), reverse=True)

        combined_data.append({
            'module': module_name,
            'versions': version_data
        })
    else:
        print(f"Failed to fetch releases for {repo_path}: {response.status_code}")

output_path = 'core/resources/quickpick-releases.json'
os.makedirs(os.path.dirname(output_path), exist_ok=True)
with open(output_path, 'w') as f:
    json.dump(combined_data, f, indent=2)

print(f"Combined release data saved to {output_path}")
