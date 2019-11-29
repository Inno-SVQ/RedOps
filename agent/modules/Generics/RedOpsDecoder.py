from modules.Generics.Domain import Domain
from modules.Generics.IP import IP
from modules.Generics.Company import Company
from modules.Generics.Service import Service
from modules.Generics.Credential import Credential
from modules.Generics.Technology import Technology
from modules.Generics.WebURL import WebURL

def RedOpsDecoder(p):
    if(p.get("type", None) == "__ip__"):
        return IP(p["id"], p["value"], p["isIPv4"])
    if(p.get("type", None) == "__domain__"):
        return Domain(p["id"], p["name"], p.get("parent", None), p.get("ip", None))
    if(p.get("type", None) == "__company__"):
        return Company(p["name"], p["id"], p["main_domain"])
    if(p.get("type", None) == "__service__"):
        return Service(p["host"], p["port"], p["protocol"], p["product"], p["version"], p["application_protocol"], p["id"])
    if(p.get("type", None) == "__credential__"):
        return Credential(p["username"], p["password"], p["domain"], p["source"])
    if(p.get("type", None) == "__technology__"):
        return Technology(p["serviceId"], p["name"], p["icon"])
    if(p.get("type", None) == "__weburl__"):
        return WebURL(p["serviceId"], p["host"], p["port"], p["path"])
    return p