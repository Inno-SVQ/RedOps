from modules.Generics.IP import IP
from modules.Generics.CustomExceptions import RedOpsInvalidType

class Service:
    def __init__(self, host, port, protocol, product, version, application_protocol, id=None):
        self.host = host
        self.port = port
        self.product = product
        self.protocol = protocol
        self.version = version
        self.application_protocol = application_protocol
        self.id = id

    def toDict(self):
        return {
            "type": "__service__",
            "id": self.id,
            "host": self.host,
            "port": self.port,
            "protocol": self.protocol,
            "product": self.product,
            "version": self.version,
            "application_protocol": self.application_protocol
        }

    def __repr__(self):
        return str(self.toDict())