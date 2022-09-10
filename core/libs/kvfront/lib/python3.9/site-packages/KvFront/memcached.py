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
 
import memcache
from sshtunnel import SSHTunnelForwarder

class MemcachedHelper:
    
    def set(self, key, value, time):
        setRet = self.mc.set(key, value, time)
        return setRet
    
    def get(self, key):
        setRet = self.mc.get(key)
        return setRet
    
    def stats(self):
        setRet = self.mc.get_stats()
        return setRet
    
    def connect(self, endpoints, ssh_user,ssh_pwd,ssh_prikey,ssh_address):
        if ssh_user == "":
            self.mc = memcache.Client(endpoints, debug=False)
        else:
            print("SSH")
            ssh_ip_port=ssh_address.split(":")
            remote_bind_address = []
            list_server = []

            for i in endpoints:
                ipport = i.split(":")
                remote_bind_address.append((ipport[0],int(ipport[1])))

            self.ssh_server = SSHTunnelForwarder(
                    ssh_address_or_host=(ssh_ip_port[0],int(ssh_ip_port[1])),
                    ssh_username=ssh_user,
                    ssh_pkey=ssh_prikey,
                    ssh_password=ssh_pwd,
                    remote_bind_addresses=remote_bind_address
            )
            self.ssh_server.start()
            for p in self.ssh_server.local_bind_ports:
                list_server.append("127.0.0.1" +  ":" + str(p))
            print(list_server)
            self.mc = memcache.Client(list_server, debug=False)

    def close(self):
        if self.mc is not None:
            self.mc.disconnect_all()
        if self.ssh_server is not None:
            self.ssh_server.close()   
        
    def get_multi(self, keys):
        vals = self.mc.get_multi(keys)
        return vals
    
    def delete_multi(self, keys):
        deleteRet = self.mc.delete_multi(keys)
        return deleteRet
    
    def flush(self):
        self.mc.flush_all()


    def __init__(self):
        self.mc = None
        self.ssh_server = None
        
