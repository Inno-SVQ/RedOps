class WebURL:
    def __init__(self, serviceId, host, port, path, fileType = None, wordLength = None, charLength = None, statusCode = None):
        self.serviceId = serviceId
        self.host = host
        self.port = port
        self.path = path
        self.fileType = fileType
        self.wordLength = wordLength
        self.charLength = charLength
        self.statusCode = statusCode

    def toDict(self):
        return {
            "type": "__weburl__",
            "serviceId": self.serviceId,
            "host": self.host,
            "port": self.port,
            "path": self.path, # /admin/test
            "fileType": self.fileType,
            "wordLength": self.wordLength,
            "charLength": self.charLength,
            "statusCode": self.statusCode
        }

    def __repr__(self):
        return str(self.toDict())