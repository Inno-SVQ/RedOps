class IP:
    def __init__(self, id, value, isIPv4):
        self.id = id
        self.value = value
        self.isIPv4 = isIPv4

    def toDict(self):
        return {
            "type": "__ip__",
            "id": self.id,
            "value": self.value,
            "isIPv4": self.isIPv4
        }

    def __repr__(self):
        return str(self.toDict())