from modules.Generics.BaseModule import BaseModule
from modules.Generics.WebURL import WebURL
from modules.Generics.WebScreenshot import WebScreenshot
from heimdall import heimdall
import requests
import base64
import os

class Module(BaseModule):
    def run(self, callback):
        self.moduleName = "WFuzzModule"
        # Callback access
        self.callback =callback
        try:
            for weburl in self.params["data"]:
                try:
                    # Check if ssl or not
                    #TODO:CONTROLAR EXCEPCION
                    if(self.checkSSL(weburl.host, weburl.port)):
                        screenshot = heimdall.jpeg("https://{}:{}{}".format(weburl.host, weburl.port, weburl.path), optimize=True, width=400, height=300)   
                    else:
                        screenshot = heimdall.jpeg("http://{}:{}{}".format(weburl.host, weburl.port, weburl.path), optimize=True, width=400, height=300)             
                    # Send picture to server
                    self.callback.debug("----------------------------JOB {} update----------------------------\n{}".format(self.params["jobId"], WebScreenshot(weburl.serviceId, weburl.path, None)))
                    if(not self.params["DISABLE_MASTER_SERVER"]):
                        r = requests.post("https://{}/api/job/screenshotUpload/{}".format(self.params["MASTER_DOMAIN"], weburl.serviceId), files={"picture": open(screenshot.path, "rb")})
                        self.callback.debug("----------------------------Response from master for JOB {} UPDATE----------------------------\n{}".format(self.params["jobId"], r.text))
                        
                    # Delete picture
                    os.remove(screenshot.path)
                except Exception as e:
                    # Probably invalid service
                    self.callback.exception(e)

        except Exception as e:
            self.callback.exception(e)
        self.callback.finish(list())

    def checkSSL(self, domain, port):
        try:
            requests.get("https://{}:{}".format(domain, port))
        except requests.exceptions.SSLError as e:
            return False
        except Exception as e:
            self.callback.exception(e)
        return True