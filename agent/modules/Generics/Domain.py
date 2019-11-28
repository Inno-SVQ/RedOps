from modules.Generics.IP import IP
from modules.Generics.CustomExceptions import RedOpsInvalidType

class Domain:
    def __init__(self, id, name, parent, ip):
        self.id = id
        self.name = name
        self.parent = parent
        self.ip = ip

    def toDict(self):
        return {
            "type": "__domain__",
            "id": self.id,
            "ip": self.ip,
            "name": self.name,
            "parent": self.parent
        }

    def __repr__(self):
        return str(self.toDict())