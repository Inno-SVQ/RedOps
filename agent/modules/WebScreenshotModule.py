from modules.Generics.BaseModule import BaseModule
from modules.Generics.WebURL import WebURL
from modules.Generics.WebScreenshot import WebScreenshot
from heimdall import heimdall
import requests
import base64
import os

'''
npm install -g phantomjs
git clone https://github.com/DistilledLtd/heimdall
python3 setup.py install

'''

class Module(BaseModule):
    def run(self, callback):
        self.moduleName = "WFuzzModule"
        # Callback access
        self.callback =callback
        try:
            for weburl in self.params:
                # Check if ssl or not
                if(self.checkSSL(weburl.host, weburl.port)):
                    screenshot = heimdall.jpeg("https://{}:{}{}".format(weburl.host, weburl.port, weburl.path), width=1440, height=900)   
                else:
                    screenshot = heimdall.jpeg("http://{}:{}{}".format(weburl.host, weburl.port, weburl.path), width=1440, height=900)
                # Screenshot to base64
                with open(screenshot.path, "rb") as image_file:
                    encoded_picture = str(base64.b64encode(image_file.read()))
                # Delete picture
                os.remove(screenshot.path)
                # Send picture to server
                self.callback.update(WebScreenshot(weburl.serviceId, weburl.path, encoded_picture))
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