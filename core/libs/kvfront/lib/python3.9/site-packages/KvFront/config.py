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

import configparser
from KvFront.constants import *

class Config:

    def __init__(self):
        print("config init")  
        print(FILE_SERVER_CFG)
        self.cp = configparser.SafeConfigParser()
        self.cp.read(FILE_SERVER_CFG)
       
    def getServerNames(self):
        servernames = self.cp.sections()
        return servernames
    
    def getServerInfo(self,servername):
        return self.cp.items(servername)
    
    def removeServer(self,servername):
        self.cp.remove_section(servername)
        self.cp.write(open(FILE_SERVER_CFG, 'w'))
        
    def addServer(self, servername, host, category, password, ssh_user, ssh_pwd, ssh_prikey, ssh_address):
        if self.cp.has_section(servername):
            return 0
        else:
            self.cp.add_section(servername)
            self.cp.set(servername, "host", host)
            self.cp.set(servername, "category",category)
            self.cp.set(servername, "password",password)
            self.cp.set(servername, "ssh_user",ssh_user)
            self.cp.set(servername, "ssh_pwd",ssh_pwd)
            self.cp.set(servername, "ssh_prikey",ssh_prikey)
            self.cp.set(servername, "ssh_address",ssh_address)
            self.cp.write(open(FILE_SERVER_CFG, 'w'))
            return 1
            
        
