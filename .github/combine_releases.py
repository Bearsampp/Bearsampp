#  Copyright (c) 2024 Bearsampp
#  License:  GNU General Public License version 3 or later; see LICENSE.txt
#  Website: https://bearsampp.com
#  Github: https://github.com/Bearsampp

import requests
import json
import os

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

combined_data = []

for url in urls:
    response = requests.get(url)
    if response.status_code == 200:
        module_name = url.split('/')[4]
        versions = response.text.strip().split('\n')
        version_data = [{'version': version, 'url': f"{url}#{version}"} for version in versions]
        combined_data.append({
            'module': module_name,
            'versions': version_data
        })

output_path = 'core/resources/quickpick-releases.json'
os.makedirs(os.path.dirname(output_path), exist_ok=True)
with open(output_path, 'w') as f:
    json.dump(combined_data, f, indent=2)
