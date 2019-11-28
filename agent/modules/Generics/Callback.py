#!/usr/bin/python3

# -*- coding: utf-8 -*-

class Callback:
    def __init__(self, update, finish, debug, warning, error, exception):
        self.update = update
        self.finish = finish

        # If none then loggin is disabled
        if(debug == None):
            self.debug = self.disabled
        else:
            self.debug = debug
        if(warning == None):
            self.warning = self.disabled
        else:
            self.warning = warning
        if(error == None):
            self.error = self.disabled
        else:
            self.error = error
        if(exception == None):
            self.exception = self.disabled
        else:
            self.exception = exception
    
    def disabled(self, data):
        pass