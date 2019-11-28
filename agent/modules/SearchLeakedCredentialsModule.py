from modules.Generics.BaseModule import BaseModule
from modules.Generics.Domain import Domain
from modules.Generics.Credential import Credential
from bs4 import BeautifulSoup
import requests
import argparse
from email.utils import getaddresses
import json

class Module(BaseModule):
    def run(self, callback):
        self.moduleName = "SearchLeakedCredentialsModule"
        # Callback access
        self.callback =callback

        # SOCKS for TOR
        session = requests.session()
        session.proxies = {'http': 'socks5h://127.0.0.1:9050', 'https': 'socks5h://127.0.0.1:9050'}

        result = []
        for domain in self.params:
            if type(domain) == Domain:
                # For every domain we try to get credentiales
                try:
                    leakedCredentials = self.find_leaks("%@{}".format(domain.name), session)
                    for credential in leakedCredentials:
                        if credential["domain"] != "btc.thx":
                            result.append(Credential(credential["username"], credential["password"], credential["domain"], "Pwndb"))
                except Exception as e:
                    # Log exceptions
                    self.callback.exception(e)
        # End JOB
        self.callback.finish(result)

    # From https://github.com/davidtavarez/pwndb/blob/master/pwndb.py
    def find_leaks(self, email, session):
        url = "http://pwndb2am4tzkvold.onion/"
        username = email
        domain = "%"

        if "@" in email:
            username = email.split("@")[0]
            domain = email.split("@")[1]
            if not username:
                username = '%'

        request_data = {'luser': username, 'domain': domain, 'luseropr': 1, 'domainopr': 1, 'submitform': 'em'}

        r = session.post(url, data=request_data)

        return self.parse_pwndb_response(r.text)

    def parse_pwndb_response(self, text):
        if "Array" not in text:
            return None

        leaks = text.split("Array")[1:]
        emails = []

        for leak in leaks:
            leaked_email = ''
            domain = ''
            password = ''
            try :
                leaked_email = leak.split("[luser] =>")[1].split("[")[0].strip()
                domain = leak.split("[domain] =>")[1].split("[")[0].strip()
                password = leak.split("[password] =>")[1].split(")")[0].strip()
            except:
                pass
            if leaked_email:
                emails.append({'username': leaked_email, 'domain': domain, 'password': password})
        return emails
