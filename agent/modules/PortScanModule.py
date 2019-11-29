#!/usr/bin/python3

# -*- coding: utf-8 -*-

import json
from modules.Generics.BaseModule import BaseModule
from modules.Generics.Domain import Domain
from modules.Generics.CustomExceptions import RedOpsInvalidType
from modules.Generics.IP import IP
from modules.Generics.Service import Service
import nmap
import socket

class Module(BaseModule):
    def run(self, callback):
        self.callback = callback
        try:
            nm = nmap.PortScannerAsync()

            if(type(self.params["hosts"]) != list):
                raise RedOpsInvalidType(type(self.params["hosts"]), list, self.moduleName)
            # Translate host to ID
            self.hostMap = []
            for host in self.params["hosts"]:
                if isinstance(host, Domain):
                    try:
                        self.hostMap.append((socket.gethostbyname(host.name), host.id))
                    except socket.gaierror as e:
                        pass # Invalid domain
                else:
                    self.hostMap.append((host.value, host.id))

            # Convert to dict
            self.hostMap = dict(self.hostMap)
                    
            # We read the IP or domain name and make a list
            hosts = [host.name if isinstance(host, Domain) else host.value for host in self.params["hosts"]]
            hosts = " ".join(hosts)
            # Also a list of ports
            ports = ",".join(self.params["options"]["ports"])

            if(self.params["options"]["mode"] == "TCP"):
                # We update every time a host is scan
                nm.scan(hosts=hosts, arguments='-sS -sV -Pn', ports=ports, callback=self.update)
                # In the last loop we want to finish the task in the server
            elif(self.params["options"]["mode"] == "UDP"):
                nm.scan(hosts=hosts, arguments='-sU -Pn', ports=ports, callback=self.update)
            else:
                self.callback.warning("Invalid scan mode")

            # As is async we cant use callback.finish here, we have to wait
            while nm.still_scanning():
                nm.wait(2)
            callback.finish(list()) # End the job
        except Exception as e:
            self.callback.exception(e)
    
    def update(self, host, data):
        result = []
        if(data == None):
            self.callback.error("Probably privileged scan without root permissions.")
            return
        if(data["scan"][host].get("tcp", None) != None):
            for port in data["scan"][host]["tcp"]:
                if(data["scan"][host]["tcp"][port]["state"] == "open"):
                    result.append(Service(self.hostMap[host], port, "tcp", data["scan"][host]["tcp"][port]["product"], data["scan"][host]["tcp"][port]["version"], data["scan"][host]["tcp"][port]["name"]))
        elif(data["scan"][host].get("udp", None) != None):
            for port in data["scan"][host]["udp"]:
                if(data["scan"][host]["udp"][port]["state"] == "open"):
                    result.append(Service(self.hostMap[host], port, "udp", data["scan"][host]["udp"][port]["product"], data["scan"][host]["udp"][port]["version"], data["scan"][host]["tcp"][port]["name"]))
        
        # We send the update to the server
        if(len(result) > 0):
            self.callback.update(result)