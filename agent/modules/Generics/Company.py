#!/usr/bin/env python3
# -*- coding: utf-8 -*-
from modules.Generics.CustomExceptions import RedOpsInvalidType
from modules.Generics.Domain import Domain

class Company:
    def __init__(self, name, id, main_domain):
        self.name = name
        self.id = id
        self.main_domain=main_domain

        # main_domain must be a Domain Object or None
        if(not isinstance(main_domain, Domain) and main_domain != None):
            raise RedOpsInvalidType(type(main_domain), Domain)

    def toDict(self):
        return {
            "type": "__company__",
            "name": self.name,
            "id": self.id,
            "main_domain": self.main_domain
        }

    def __repr__(self):
        return str(self.toDict())