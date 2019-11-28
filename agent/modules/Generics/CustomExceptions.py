class RedOpsException(Exception):
    """Base exception"""
    pass

class RedOpsInvalidType(RedOpsException):
    """ 
        Exception raised when an invalid type is used
    """
    def __init__(self, callType, expectedType, module = None):
        self.callType = callType.__name__
        self.expectedType = expectedType.__name__
        self.module = module

    def __str__(self):
        if(self.module == None):
            return "Expected parameter type {} found {}".format(self.expectedType, self.callType)
        else:
            return "Expected parameter type {} found {} in {}".format(self.expectedType, self.callType, self.module)