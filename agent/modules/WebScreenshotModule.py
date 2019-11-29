from modules.Generics.BaseModule import BaseModule
from modules.Generics.WebURL import WebURL
from modules.Generics.WebScreenshot import WebScreenshot
from heimdall import heimdall
import requests
import base64
import os

'''
apt install phantomjs xvfb
git clone https://github.com/DistilledLtd/heimdall
python3 setup.py install

'''

class Module(BaseModule):
    def run(self, callback):
        self.moduleName = "WFuzzModule"
        # Callback access
        self.callback =callback
        try:
            for weburl in self.params["data"]:
                # Check if ssl or not
                if(self.checkSSL(weburl.host, weburl.port)):
                    screenshot = heimdall.jpeg("https://{}:{}{}".format(weburl.host, weburl.port, weburl.path), optimize=True, width=800, height=600)   
                else:
                    screenshot = heimdall.jpeg("http://{}:{}{}".format(weburl.host, weburl.port, weburl.path), width=800, height=600)
                # Screenshot to base64
                
                # Delete picture
                os.remove(screenshot.path)
                # Send picture to server
                if(not self.params["DISABLE_MASTER_SERVER"]):
                    requests.post("https://{}/job/screenshotUpload".format(self.params["MASTER_DOMAIN"]), files={"picture": open(screenshot.path, "rb"), "service_id": weburl.serviceId,
                    "path": weburl.path, "jobId": self.params["jobId"]})
                else:
                    self.callback.debug("----------------------------JOB {} update----------------------------\n{}".format(self.params["jobId"], WebScreenshot(weburl.serviceId, weburl.path, encoded_picture)))

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