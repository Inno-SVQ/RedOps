
import shodan
import socket
from getpass import getpass
from modules.Generics.Service import Service
from modules.Generics.BaseModule import BaseModule

class Module(BaseModule):
    def run(self, callback):
        data = self.params["data"]
        api_key = self.params["api_key"]

        shodanObj = Shodan(api_key)
        for d in data:
            print(d)
        for index, domain in enumerate(data):

            ips=shodanObj.resDomain(domain.name)
            res=shodanObj.searchServices(ips)
            jsonStr=[]

            for i in res:
                jsonStr.append(i)

            # In the last loop we want to finish the task in the server
            if(index < len(self.params) - 1):
                callback.update(jsonStr)

        callback.finish(jsonStr)

class Shodan():
    def __init__(self,api):
        self.api=shodan.Shodan(api)

    def resDomain(self,domains):
        ip=[]
        try:
            for domain in domains:
                ip.append(socket.gethostbyname(domain))

        except socket.gaierror as e:
            ip.append(None)

        return ip

    def searchServices(self,ips):
        list=[]

        try:
            for ip in ips:
                host = self.api.host(ip)

                r=host.get("data")

                for item in host['data']:
                    list.append(Service(ip,item['port'],item['transport'],r[1]["product"],None,None))

        except Exception as e:
            print('Error: {}'.format(e))

        return list
