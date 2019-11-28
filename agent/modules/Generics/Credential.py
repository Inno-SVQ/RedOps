#!/usr/bin/env python3
# -*- coding: utf-8 -*-

class Credential:
    def __init__(self, username, password, domain, source):
        self.username = username
        self.password = password
        self.domain = domain
        self.source = source

    def toDict(self):
        return {
            "type": "__credential__",
            "username": self.username,
            "password": self.password,
            "domain": self.domain,
            "source": self.source
        }

    def __repr__(self):
        return str(self.toDict())