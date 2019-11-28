from modules.Generics.BaseModule import BaseModule
from modules.Generics.Domain import Domain
from bs4 import BeautifulSoup
import requests
from dnsdumpster.DNSDumpsterAPI import DNSDumpsterAPI
import socket
from pycrtsh import Crtsh

class Module(BaseModule):
    def run(self, callback):
        self.moduleName = "SearchSubdomainsModule"
        # Callback access
        self.callback =callback

        SECURITYTRAILS_APIKEY = self.params["SECURITYTRAILS_APIKEY"]
        for domain in self.params["data"]:
            if type(domain) == Domain:
                # For every domain we try every API
                # If available use API key version
                if(SECURITYTRAILS_APIKEY != ""):
                    self.securityTrailsAPI(SECURITYTRAILS_APIKEY, domain.name, domain.id)
                else:
                    self.securityTrailsBasic(domain.name, domain.id)
                self.dnsDumpster(domain.name, domain.id)
                self.crtshSearch(domain.name, domain.id)
        # End JOB
        self.callback.finish(list())

    def crtshSearch(self, domain, parentDomain):
        result = []
        discardDomains = []
        c = Crtsh()
        # Get certs
        try:
            certs = c.search(domain)
            # Get data of every cert available
            for cert in certs:
                data = c.get(cert["id"], type="id")
                for alternative_name in data["extensions"]["alternative_names"]:
                    # Discard wildcard names
                    if alternative_name not in discardDomains and "*" not in alternative_name:
                        try:
                            ip = socket.gethostbyname(alternative_name)
                        except socket.gaierror as e:
                            # Subdomains does not resolve
                            ip = None
                        discardDomains.append(alternative_name)
                        result.append(Domain(None, alternative_name, parentDomain, ip))
        except Exception as e:
            self.callback.exception(e)
        
        self.callback.update(result)

    def dnsDumpster(self, domain, parentDomain):
        result = []
        try:
            dnsDumpster = DNSDumpsterAPI().search(domain)
            # Get domains from all types of records
            for subdomain in dnsDumpster["dns_records"]["host"]:
                result.append(Domain(None, subdomain["domain"], parentDomain, subdomain.get("ip", None)))
        except Exception as e:
            # Log exceptions
            self.callback.exception(e)

        # Send results to RedOps
        if len(result) > 0:
            self.callback.update(result)

    def securityTrailsAPI(self, apiKey, domain, parentDomain):
        result = []
        try:
            r = requests.get("https://api.securitytrails.com/v1/domain/{}/subdomains".format(domain), headers={"apikey": apiKey})
            for subdomain in r.json()["subdomains"]:
                subdomain = "{}.{}".format(subdomain, domain)
                try:
                    ip = socket.gethostbyname(subdomain)
                except socket.gaierror as e:
                    # Subdomains does not resolve
                    ip = None 
                result.append(Domain(None, subdomain, parentDomain, ip))
                # Send results to RedOps
                if len(result) > 50:
                    # Update every 50
                    self.callback.update(result)
                    result = []
        except Exception as e:
            self.callback.exception(e)
        
        # Send results to RedOps
        if len(result) > 0:
            self.callback.update(result)


    def securityTrailsBasic(self, domain, parentDomain):
        result = []
        try:
            r = requests.get("https://securitytrails.com/list/apex_domain/{}/subdomains".format(domain), headers={"User-Agent": "Mozilla/5.0 (X11; Linux x86_64; rv:68.0) Gecko/20100101 Firefox/68.0"})

            soup = BeautifulSoup(r.text, "lxml")

            # Results are in the first table
            table = soup.find_all("table")[1]

            # Repeat for every row in table
            for row in table.find_all("tr"):
                # Get a list of columns
                columns = row.find_all("td")
                if(len(columns) == 5): # Check expected columns number
                    subdomain = columns[1].getText()
                    try:
                        ip = socket.gethostbyname(subdomain)
                    except socket.gaierror as e:
                        # Subdomains does not resolve
                        ip = None 
                    # Results are unique
                    result.append(Domain(None, subdomain, parentDomain, ip)) # First column is the subdomain
                    if len(result) > 50:
                        # Update every 50
                        self.callback.update(result)
                        result = []
        except Exception as e:
            # Log exceptions
            self.callback.exception(e)
        
        # Send results to RedOps
        if len(result) > 0:
            self.callback.update(result)

    