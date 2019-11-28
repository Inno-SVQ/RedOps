class Technology:
    def __init__(self, serviceId, name, icon):
        self.serviceId = serviceId
        self.name = name
        self.icon = icon

    def toDict(self):
        return {
            "type": "__technology__",
            "serviceId": self.serviceId,
            "name": self.name,
            "icon": self.icon
        }

    def __repr__(self):
        return str(self.toDict())