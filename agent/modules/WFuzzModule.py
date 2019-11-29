from modules.Generics.BaseModule import BaseModule
from modules.Generics.WebURL import WebURL
import wfuzz
import requests
import random
import string

class Module(BaseModule):
    def run(self, callback):
        self.moduleName = "WFuzzModule"
        # Callback access
        self.callback =callback

        for weburl in self.params:
            # Check if ssl or not
            if(self.checkSSL(weburl.host, weburl.port)):
                hcCodes = [self.detectErrorCode(weburl.host, weburl.port, True)] # List for future manual setting
                for r in wfuzz.fuzz(url="https://{}:{}{}/FUZZ".format(weburl.host, weburl.port, weburl.path), hc=hcCodes, rleve=5, payloads=[("file",dict(fn="wordlists/common.txt"))]):
                    # Mount response Object
                    newPath = "{}/{}".format(weburl.path, r.url.split("/")[-1])
                    extension = None
                    if("." in newPath):
                        extension = newPath.split(".")[-1]
                    self.callback.update([WebURL(weburl.serviceId, weburl.host, weburl.port, newPath, extension, r.words, r.chars, r.code)])
            else:
                hcCodes = [self.detectErrorCode(weburl.host, weburl.port, False)] # List for future manual setting
                for r in wfuzz.fuzz(url="http://{}:{}{}/FUZZ".format(weburl.host, weburl.port, weburl.path), hc=hcCodes, rleve=5, payloads=[("file",dict(fn="wordlists/common.txt"))]):
                    # Mount response Object
                    newPath = "{}/{}".format(weburl.path, r.url.split("/")[-1])
                    extension = None
                    if("." in newPath):
                        extension = newPath.split(".")[-1]
                    self.callback.update([WebURL(weburl.serviceId, weburl.host, weburl.port, newPath, extension, r.words, r.chars, r.code)])        # End JOB
        self.callback.finish(list())

    def checkSSL(self, domain, port):
        try:
            requests.get("https://{}:{}".format(domain, port))
        except requests.exceptions.SSLError as e:
            return False
        except Exception as e:
            self.callback.exception(e)
        return True

    def detectErrorCode(self, domain, port, isSSL):
        # Create random string
        thereIsNoWayThisExists = ["".join([random.choice(string.ascii_letters) for _ in range(10)]) for _ in range(2)] 

        try:
            lastCode = None
            if(isSSL):
                for word in thereIsNoWayThisExists:
                    r = requests.head("https://{}:{}/{}".format(domain, port, word))
                    
                    if(lastCode != None):
                        if(r.status_code == lastCode):
                            return lastCode # Two tries the same code
                    lastCode = r.status_code
            else:
                for word in thereIsNoWayThisExists:
                    r = requests.head("http://{}:{}/{}".format(domain, port, word))

                    if(lastCode != None):
                        if(r.status_code == lastCode):
                            return lastCode # Two tries the same code
                    lastCode = r.status_code
            return 404 # No code detected, fallback to 404
        except Exception as e:
            self.callback.exception(e)
                

