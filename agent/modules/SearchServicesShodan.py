
import shodan
import socket
import ipaddress
import time
from getpass import getpass
from modules.Generics.Service import Service
from modules.Generics.Domain import Domain
from modules.Generics.IP import IP
from modules.Generics.BaseModule import BaseModule

class Module(BaseModule):
    def run(self, callback):
        data = self.params["data"]
        api_key = self.params["api_key"]

        shodanObj = Shodan(api_key, callback)

        for index, domain in enumerate(data):
            if type(domain) == Domain:
                ips=shodanObj.resDomain(domain.name)

            elif type(domain) == IP:
                ips=domain.value

            res=shodanObj.searchServices(ips)

            jsonStr=[]

            for i in res:
                jsonStr.append(i)

            time.sleep(1)

            # In the last loop we want to finish the task in the server
            if(index < len(self.params) - 1):
                callback.update(jsonStr)

        callback.finish(jsonStr)

class Shodan():
    def __init__(self,api,callback):
        self.api=shodan.Shodan(api)
        self.callback = callback

    def resDomain(self,domain):
        try:
            ip=socket.gethostbyname(domain)

            try:
                ipaddress.IPv4Address(ip)
            except ValueError:
                pass

        except socket.gaierror as e:
            pass

        return ip

    def searchServices(self,ip):
        list=[]

        try:
            host = self.api.host(ip)

            r=host.get("data")

            try:
                ap=r[1]["_shodan"]["module"]
            except Exception as e:
                ap=None

            for item in host['data']:
                list.append(Service(ip,item['port'],item['transport'],r[1]["product"],None,ap))

        except Exception as e:
            self.callback.exception(e)

        return list
