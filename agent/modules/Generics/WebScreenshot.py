class WebScreenshot:
    def __init__(self, serviceId, path, picture):
        self.serviceId = serviceId
        self.path = path
        self.picture = picture

    def toDict(self):
        return {
            "type": "__webscreenshot__",
            "service_id": self.serviceId,
            "path": self.path,
            "picture": self.picture            
        }

    def __repr__(self):
        return str(self.toDict())