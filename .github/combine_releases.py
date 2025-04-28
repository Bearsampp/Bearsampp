import requests
import json
import os
import re

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

for repo_path in repos:
    # Split the repo path into owner and repo
    parts = repo_path.split('/')
    if len(parts) != 2:
        print(f"Skipping invalid repo path: {repo_path}")
        continue

    owner, repo = parts

    # Use GitHub API to fetch releases
    api_url = f"https://api.github.com/repos/{owner}/{repo}/releases"
    response = requests.get(api_url, headers=headers)

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

            # Use the first .7z asset
            asset_url = seven_z_assets[0]['browser_download_url']
            asset_name = seven_z_assets[0]['name']

            # Extract version from the asset name
            # Pattern: bearsampp-{module}-{version}.7z or bearsampp-{module}-{version}-{anything}.7z
            module_short_name = repo.replace('module-', '')
            
            # Extract the part between module name and .7z extension
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
                version_number = release['tag_name']
                print(f"Warning: Could not extract version from asset name for {asset_name}, using tag name instead")

            # Include the prerelease state from the GitHub API
            is_prerelease = release['prerelease']

            version_data.append({
                'version': version_number,
                'url': asset_url,
                'prerelease': is_prerelease  # Add prerelease state to the output
            })

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
