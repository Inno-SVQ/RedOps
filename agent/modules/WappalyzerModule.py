
from modules.Generics.Service import Service
from modules.Generics.Domain import Domain
from modules.Generics.IP import IP
from modules.Generics.BaseModule import BaseModule
from modules.Generics.Technology import Technology
import requests

class Module(BaseModule):
    def run(self, callback):
        self.callback = callback
        self.result = []
        try:
            for service in self.params:
                # Check for ssl
                self.getTechnologies("{}{}:{}".format("http://", service.host, service.port), service.id)
                self.getTechnologies("{}{}:{}".format("https://", service.host, service.port), service.id)
            callback.finish(self.result)
        except Exception as e:
            callback.exception(e)

    def getTechnologies(self, domain, id):
        # We try http and https
        try:
            r = requests.get("https://api.wappalyzer.com/lookup-basic/v1/?url={}".format(domain), headers={"User-Agent": "Mozilla/5.0 (X11; Linux x86_64; rv:68.0) Gecko/20100101 Firefox/68.0", "Referer": "https://www.wappalyzer.com/",
            "Origin": "https://www.wappalyzer.com"})
            
            for tech in r.json():
                self.result.append(Technology(id, tech["name"], tech["icon"]))

        except Exception as e:
            self.callback.exception(e)