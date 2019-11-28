import json
from modules.Generics.Domain import Domain
from modules.Generics.IP import IP
from modules.Generics.Company import Company
from modules.Generics.Service import Service

class RedOpsEncoder(json.JSONEncoder):
    # We override de default function of JSONEncoder
    def default(self, obj):
        # If the object is type Domain we know how to serialize it
        if(isinstance(obj, Domain)):
            return obj.toDict()

        # If the object is type IP we serialize it
        if(isinstance(obj, IP)):
            return obj.toDict()

        # Idem with Company
        if(isinstance(obj, Company)):
            return obj.toDict()

        # Idem with Service
        if(isinstance(obj, Service)):
            return obj.toDict()

        return json.JSONEncoder.default(self, obj)