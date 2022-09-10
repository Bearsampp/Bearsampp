 #   Copyright (C) 2017-2022  Gavin W
 #   This program is free software: you can redistribute it and/or modify
 #   it under the terms of the GNU General Public License as published by
 #   the Free Software Foundation, either version 3 of the License, or
 #   (at your option) any later version.

 #   This program is distributed in the hope that it will be useful,
 #   but WITHOUT ANY WARRANTY; without even the implied warranty of
 #   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 #   GNU General Public License for more details.

 #   You should have received a copy of the GNU General Public License
 #   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 
import sys
import os.path

if os.path.isfile(os.path.join('..', 'ui', 'main.ui')):
    DIR_PREFIX = '..'
    DIR_UI = os.path.join(DIR_PREFIX, 'ui')
    DIR_CFG = os.path.join(DIR_PREFIX, 'config')
    DIR_ICON = os.path.join(DIR_PREFIX, 'icons')
else:
    DIR_PREFIX = os.path.join(os.path.expanduser('~'),'.local','KvFront')
    DIR_UI = os.path.join(DIR_PREFIX, 'ui')
    DIR_CFG = os.path.join(DIR_PREFIX, 'config')

    DIR_PREFIX2 = os.path.join(os.path.expanduser('~'),'.local','share')
    DIR_ICON = os.path.join(DIR_PREFIX2, 'icons')
    
    print("DIR_UI:" + DIR_UI)
    print("DIR_CFG:" + DIR_CFG)
    print("DIR_ICON:" + DIR_ICON)


FILE_UI_MAIN = os.path.join(DIR_UI, 'main.ui')
FILE_UI_ADDSERVER = os.path.join(DIR_UI, 'addserver.ui')
FILE_UI_NEWREDISKEY = os.path.join(DIR_UI, 'newrediskey.ui')
FILE_UI_NEWMEMKEY = os.path.join(DIR_UI, 'newmemkey.ui')
FILE_UI_DETAILPAGE = os.path.join(DIR_UI, 'detailpage-2.ui')
FILE_UI_DETAILPAGE4REDIS = os.path.join(DIR_UI, 'detailpage4redis-2.ui')
FILE_SERVER_CFG = os.path.join(DIR_CFG, 'server.conf')
