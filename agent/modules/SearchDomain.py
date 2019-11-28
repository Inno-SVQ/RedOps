
import requests
import json
from modules.Generics.BaseModule import BaseModule
from modules.Generics.Domain import Domain
from modules.Generics.CustomExceptions import RedOpsInvalidType
from modules.Generics.IP import IP
from modules.Generics.Company import Company
import socket
from urllib.request import urlopen
from bs4 import BeautifulSoup

class Module(BaseModule):

    def run(self, callback):
        app=SearchDomain(callback)

        if(type(self.params) != list):
            raise RedOpsInvalidType(type(self.params), list, self.moduleName)

        for index, org in enumerate(self.params):
            res=app.checkDomains(org("domain"))
            jsonStr=[]
            for i in res:
                jsonStr.append(Company(res[i]["domain"],org["id"], org["main_domain"]))

            # In the last loop we want to finish the task in the server
            if(index < len(self.params) - 1):
                callback.update(jsonStr)


class SearchDomain:

    def __init__(self, callback):
        self.callback = callback

    def checkDomainWebSite(domain):
        web="www."+domain
        res=[]

        try:
            html = urlopen(web)
            bsObj = BeautifulSoup(html.read());

            for link in bsObj.find_all('a'):
                if(link.get('href') in res):
                    pass
                else:
                    res.append(link.get('href'))

        except Exception as e:
            pass

        return res

    def checkDomains(self,hostname):
        ar=[]
        b={}

        defaults=[".com",".org",".net",".es",".edu",".gov",".int",".mil",".arpa",
                  ".de",'.ac','.ad','.ae', '.af', '.ag', '.ai', '.al', '.am', '.ao', '.aq', '.ar', '.as',
                  '.at', '.au', '.aw', '.ax', '.az', '.ba', '.bb', '.bd', '.be', '.bf', '.bg', '.bh', '.bi',
                  '.bj', '.bm', '.bn', '.bo', '.br', '.bs', '.bt', '.bw', '.by', '.bz', '.ca', '.ca', '.cc', '.cd',
                  '.cf', '.cg', '.ch', '.ci', '.ck', '.cl', '.cm', '.cn', '.co', '.cr', '.cu', '.cv', '.cw', '.cx', '.cy',
                  '.cz', '.de', '.dj', '.dk', '.dm', '.do', '.dz', '.ec', '.ee', '.eg', '.er', '.et', '.eu',
                   '.fi', '.fj', '.fk', '.fm', '.fo', '.fr', '.ga', '.gd', '.ge', '.gf', '.gg', '.gh', '.gi', '.gl', '.gal',
                   '.gm', '.gn', '.gp', '.gq', '.gr', '.gs', '.gt', '.gu', '.gw', '.gy', '.hk', '.hm', '.hn', '.hr', '.ht',
                   '.hu', '.id', '.ie', '.il', '.im', '.in', '.io', '.iq', '.ir', '.is', '.it', '.je', '.jm', '.jo', '.jp',
                   '.ke', '.kg', '.kh', '.ki', '.km', '.kn', '.kp', '.kr', '.kw', '.ky', '.kz', '.la', '.lb', '.lc', '.li',
                   '.lk', '.lr', '.ls', '.lt', '.lu', '.lv', '.ly', '.ma', '.mc', '.md', '.me', '.mg', '.mh', '.mk', '.ml',
                   '.mm', '.mn', '.mn', '.mo', '.mp', '.mq', '.mr', '.ms', '.mt', '.mu', '.mv', '.mw', '.mx', '.my', '.mz',
                   '.na', '.nc', '.ne', '.nf', '.ng', '.ni', '.nl', '.no', '.np', '.nr', '.nu', '.nz', '.om', '.pa', '.pe',
                   '.pf', '.pg', '.ph', '.pk', '.pl', '.pm', '.pn', '.pr', '.ps', '.pt', '.pw', '.py', '.qa', '.re', '.ro',
                   '.rs', '.ru', '.su', '.rw', '.sa', '.sb', '.sc', '.sd', '.se', '.sg', '.sh', '.si', '.sk', '.sl', '.sm',
                   '.sn', '.so', '.sr', '.ss', '.st', '.su', '.sv', '.sx', '.sy', '.sz', '.tc', '.td', '.tf', '.tg', '.th',
                   '.tj', '.tk', '.tl', '.tp', '.tm', '.tn', '.to', '.tr', '.tt', '.tv', '.tw', '.tz', '.ua', '.ug', '.us',
                   '.uy', '.uz', '.va', '.vc', '.ve', '.vg', '.vi', '.vn', '.vu', '.wf', '.ws', '.ye', '.yt', '.za', '.zm',
                   '.zw', '.dz', '.am', '.bh', '.bd', '.by', '.bg', '.cn', '.cn', '.eg', '.eu', '.eu', '.ge', '.gr', '.hk',
                   '.in', '.in', '.in', '.in', '.in', '.in', '.in', '.in', '.in', '.in', '.in', '.in', '.in', '.in', '.in',
                   '.ir', '.iq', '.jo', '.kz', '.la', '.mo', '.mo', '.my', '.mr', '.mn', '.ma', '.mk', '.om', '.pk', '.ps',
                   '.qa', '.ru', '.sa', '.rs', '.sg', '.sg', '.kr', '.lk', '.lk', '.sd', '.sy', '.tw', '.tw', '.th', '.tn',
                   '.ua', '.ae', '.ye', '.academy', '.accountant', '.local', '.onion', '.test', '.art', '.bar',
                   '.bible', '.biz', '.church', '.cloud', '.club', '.college', '.design', '.dev', '.download', '.eco',
                   '.google', '.green', '.hiv', '.info', '.kaufen', '.kiwi', '.lat', '.lgbt', '.moe', '.name', '.ninja',
                   '.NGO','.ONG', '.one', '.NGO and .ONG', '.OOO', '.org', '.pro', '.shop', '.wiki', '.wtf', '.xyz', '.aero', '.app',
                   '.asia', '.cat', '.cern', '.coop', '.edu', '.gov', '.int', '.jobs', '.mil', '.mobi', '.museum', '.post', '.tel',
                   '.travel', '.xxx', '.africa', '.amsterdam', '.bcn', '.berlin', '.brussels', '.bzh', '.cymru', '.eu', '.eus', '.frl',
                   '.gal', '.gent', '.irish', '.istanbul', '.kiwi', '.krd', '.lat', '.london', '.melbourne', '.nyc', '.paris', '.quebec',
                   '.rio', '.saarland', '.scot', '.sydney', '.taipei', '.tokyo', '.vegas', '.vlaanderen', '.wales', '.wien', '.arpa',
                   '.nato', '.example', '.invalid', '.local', '.onion', '.test', '.eng', '.sic', '.geo', '.mail', '.web',
                   '.kid', '.kids']

        for domain in defaults:

            try:
                ip=socket.gethostbyname(hostname+domain)
                b["ip"]=ip
                b["domain"]=hostname+domain
                ar.append(b)
                b={}
            except Exception as e:
                #print("No exist "+hostname+domain)
                self.callback.exception(e)

        return ar
