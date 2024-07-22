import requests
import json

urls = [
    'https://raw.githubusercontent.com/Bearsampp/module-adminer/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-apache/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-composer/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-consolez/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-filezilla/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-ghostscript/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-git/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-gitlist/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-mailhog/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-mariadb/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-memcached/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-mysql/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-ngrok/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-nodejs/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-perl/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-php/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-phpmemadmin/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-phpmyadmin/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-phppgadmin/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-postgresql/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-python/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-ruby/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-webgrind/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-xdc/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-xlight/main/releases.properties',
    'https://raw.githubusercontent.com/Bearsampp/module-yarn/main/releases.properties'
]

combined_data = {"modules": []}

for url in urls:
    response = requests.get(url)
    if response.status_code == 200:
        properties = response.text.splitlines()
        module_name = url.split('/')[4]
        module_data = {
            "name": module_name,
            "versions": []
        }
        for prop in properties:
            if '=' in prop:
                version, version_url = prop.split('=', 1)
                module_data["versions"].append({
                    "version": version.strip(),
                    "url": version_url.strip()
                })
        combined_data["modules"].append(module_data)

with open('core/resources/quickpick-releases.json', 'w') as json_file:
    json.dump(combined_data, json_file, indent=4)
