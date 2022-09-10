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
 
from redis.cluster import RedisCluster
from redis.cluster import ClusterNode
from sshtunnel import SSHTunnelForwarder
import redis

class RedisHelper:
    def stats(self):
        ret = self.rc.info()
        return ret

    def stats4cluster(self):
        ret = self.rc.cluster_info()
        return ret

    def get_all_keys(self):
        data_keys = self.rc.keys()
        return data_keys

    def type(self,key):
        return self.rc.type(key)

    def connect(self, endpoints, password, db_sn,ssh_user,ssh_pwd,ssh_prikey,ssh_address):
        print("connect to redis:" + endpoints)
        print(ssh_user)
        try:
            if ssh_user == "":
                ip_port = endpoints.split(":")
                if len(password) > 0:
                    self.rc = redis.StrictRedis(host=ip_port[0],port=ip_port[1],db=db_sn,password=password,decode_responses=True)
                else:
                    self.rc = redis.StrictRedis(host=ip_port[0],port=ip_port[1],db=db_sn,decode_responses=True)
                self.rc.client_list()
                return 0
            else:
                print("SSH")
                ip_port = endpoints.split(":")
                ssh_ip_port=ssh_address.split(":")
                self.ssh_server = SSHTunnelForwarder(
                    ssh_address_or_host=(ssh_ip_port[0],int(ssh_ip_port[1])),
                    ssh_username=ssh_user,
                    ssh_pkey=ssh_prikey,
                    ssh_password=ssh_pwd,
                    # local_bind_address=('0.0.0.0', 10022),
                    remote_bind_address=(ip_port[0], int(ip_port[1]))
                )
                self.ssh_server.start()
                print(self.ssh_server.local_bind_port)
                if len(password) > 0:
                    self.rc = redis.StrictRedis(host='127.0.0.1',port=self.ssh_server.local_bind_port,db=db_sn,password=password,decode_responses=True)
                else:
                    self.rc = redis.StrictRedis(host='127.0.0.1',port=self.ssh_server.local_bind_port,db=db_sn,decode_responses=True)
                self.rc.client_list()
                return 0

        except redis.ConnectionError as err:
            print(str(err))
            return str(err)
        except Exception as err2:
            print(str(err2))
            return str(err2)

    def connect_cluster(self, endpoints, password, db_sn,ssh_user,ssh_pwd,ssh_prikey,ssh_address):
        try:
            if ssh_user == "":
                servers = endpoints.split(",")
                list_server = []
                for i in servers:
                    ipport = i.split(":")
                    list_server.append(ClusterNode(ipport[0],int(ipport[1])))
                print(list_server)
                if len(password) > 0:
                    self.rc = RedisCluster(startup_nodes=list_server,password=password,decode_responses=True)
                else:
                    self.rc = RedisCluster(startup_nodes=list_server,decode_responses=True)
                self.rc.client_list()
                return 0
            else:
                print("SSH")
                servers = endpoints.split(",")
                ssh_ip_port=ssh_address.split(":")
                remote_bind_address = []
                list_server = []
                for i in servers:
                    ipport = i.split(":")
                    remote_bind_address.append((ipport[0],int(ipport[1])))
                print(remote_bind_address)
                self.ssh_server = SSHTunnelForwarder(
                    ssh_address_or_host=(ssh_ip_port[0],int(ssh_ip_port[1])),
                    ssh_username=ssh_user,
                    ssh_pkey=ssh_prikey,
                    ssh_password=ssh_pwd,
                    # local_bind_address=('0.0.0.0', 10022),
                    remote_bind_addresses=remote_bind_address
                )
                self.ssh_server.start()
                for p in self.ssh_server.local_bind_ports:
                    list_server.append(ClusterNode('127.0.0.1',int(p)))
                print(list_server)
                if len(password) > 0:
                    self.rc = RedisCluster(startup_nodes=list_server,password=password,decode_responses=True,skip_full_coverage_check=True)
                else:
                    self.rc = RedisCluster(startup_nodes=list_server,decode_responses=True,skip_full_coverage_check=True)
                self.rc.client_list()
                return 0
        except Exception as err:
            print("err:" + str(err))
            return str(err)
    
    def close(self):
        if self.rc is not None:
            self.rc.close()
        if self.ssh_server is not None:
            self.ssh_server.close()
    
    def scan_iter(self, key, count):
        vals = self.rc.scan_iter(key,count)
        return vals

    def scan(self, vfrom, key, count):
        vals = self.rc.scan(vfrom, key, count)
        return vals

    def set(self, key, value, time):
        if time == 0:
            setRet = self.rc.set(key, value)
        else:
            setRet = self.rc.set(key, value, time)
        return setRet
    

    def hset(self, key, field, value):
        setRet = self.rc.hset(key, field, value)
        return setRet
    
    def lset(self, key, index, value):
        setRet = self.rc.lset(key, index, value)
        return setRet
    
    def hmset(self, key, mappings):
        setRet = self.rc.hmset(key, mappings)
        return setRet


    def lpush(self, key, values):
        setRet = self.rc.lpush(key, values)
        return setRet

    def sadd(self, key, values):
        setRet = self.rc.sadd(key, values)
        return setRet

    def zadd(self, key, mapping):
        setRet = self.rc.zadd(key,mapping)
        return setRet

    def setbit(self, key, offset, value):
        setRet = self.rc.setbit(key, offset, value)
        return setRet

    def xadd(self, key, id, mapping):
        setRet = self.rc.xadd(key,mapping,id)
        return setRet


    def hgetall(self, key):
        vals = self.rc.hgetall(key)
        return vals

    def hdel(self, key, field):
        vals = self.rc.hdel(key,field)
        return vals
    
    def lrange(self, key, f,t):
        vals = self.rc.lrange(key,f,t)
        return vals

    def smember(self, key):
        vals = self.rc.smembers(key)
        return vals
    
    def lrem(self, key, num, value):
        vals = self.rc.lrem(key,num,value)
        return vals

    def srem(self, key, value):
        vals = self.rc.srem(key, value)
        return vals

    def zrem(self, key, value):
        vals = self.rc.zrem(key, value)
        return vals
    
    def xdel(self, key, id):
        vals = self.rc.xdel(key,id)
        return vals

    def zrange(self, key, f,t):
        vals = self.rc.zrange(key,f,t,True,True)
        return vals

    def smembers(self, key):
        vals = self.rc.smembers(key)
        return vals

    def get_multi(self, keys):
        vals = self.rc.mget(keys)
        return vals

    def get(self, key):
        vals = self.rc.get(key)
        return vals

    def delete_multi(self, keys):
        for k in keys:
            self.rc.delete(k)
        return 1
            
    def xrange(self, key):
        vals = self.rc.xrange(key,"-","+")
        return vals

    def set_json(self, key, path, value):
        vals = self.rc.json().set(key, path, value)
        return vals
    
    def get_json(self, key):
        vals = self.rc.json().get(key)
        return vals

    def del_json(self, key, path):
        vals = self.rc.json().delete(key, path)
        return vals

    def flush(self):
        self.rc.flushall
    
    def execute_command(self, cmd):
        ret = self.rc.execute_command(cmd)
        return ret

    def __init__(self):
        self.rc = None
        self.ssh_server = None