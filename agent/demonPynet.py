#!/usr/bin/python3

# -*- coding: utf-8 -*-

__author__ = "antjim"
__license__ = " "
__version__ = "0.0.1"
__web__ = " "
__status__ = "Preproduction"


import time
import pymysql  # pip install pymysql
from daemon import runner   # pip isntall daemon


class Mysql(object):
    def __init__(self,database_host,username,password,database_name):
        self.database_host=database_host
        self.username=username
        self.password=password
        self.database_name=database_name

    def getConnection(self):
        return pymysql.connect(self.database_host,self.username,self.password,self.database_name)

    def getData(self,db):
        cursor = db.cursor()
        sql = "SELECT * FROM jobs WHERE status > {0}".format(0) #if status = 0, it's necessary to work.
        cursor.execute(sql)
        results=cursor.fetchall() # get all filtered data.
        db.close()


    def getVersion(self,db):    # test connection
        cursor = db.cursor()
        cursor.execute("SELECT VERSION()")
        data = cursor.fetchone()
        print ("Database version : {0}".format(data))
        db.close()




class App(object):
    def __init__(self):
        self.stdin_path = '/dev/null'
        self.stdout_path = '/dev/tty'
        self.stderr_path = '/dev/tty'
        self.pidfile_path =  '/tmp/redemop.pid'
        self.pidfile_timeout = 5

        self.mysql=Mysql("localhost","root","test1234","jobs")   # configure own.
        #self.db=self.mysql.getConnection()    #connection

        #prepareMySQL=mysql.getVersion(db)

    def next_job(self):
        #busca el siguiente trabajo de la bd
        #identifica el tipo de trabajo y avisa al esclavo correspondiente
        pass

    def finally_job(self):
        #recoge el trabajo finalizado del esclavo
        #actualiza campos en BD
        pass

    def run(self):
        while True:
            print("Working")
            time.sleep(10)


if __name__ == '__main__':
   app=App()
   serv = runner.DaemonRunner(app)
   serv.do_action()
