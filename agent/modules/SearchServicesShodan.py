
import shodan
import socket
import ipaddress
import time
from modules.Generics.Service import Service
from modules.Generics.Domain import Domain
from modules.Generics.IP import IP
from modules.Generics.BaseModule import BaseModule

class Module(BaseModule):
    def run(self, callback):
        try:
            api_key = self.params["SHODAN_APIKEY"]

            if api_key == "":
                raise Exception("Invalid Shodan API KEY:")

            shodanObj = Shodan(api_key, callback)
            for index, domain in enumerate(self.params["data"]):
                jsonStr=[]
                if type(domain) == Domain:
                    try:
                        ips = socket.gethostbyname(domain.name)
                    except socket.gaierror as e:
                        # does not exists
                        ips = None
                elif type(domain) == IP:
                    ips=domain.value

                if ips!= None:
                    res=shodanObj.searchServices(ips)

                    for i in res:
                        jsonStr.append(i)

                    time.sleep(1)

                # In the last loop we want to finish the task in the server
                if(index < len(self.params) - 1):
                    callback.update(jsonStr)
                    jsonStr = []

            callback.finish(jsonStr)
        except Exception as e:
            callback.exception(e)

class Shodan():
    def __init__(self,api,callback):
        self.api=shodan.Shodan(api)
        self.callback = callback


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
