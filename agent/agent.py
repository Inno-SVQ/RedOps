from flask import Flask, request, jsonify
import json
import ssl
import importlib
from modules.Generics.Callback import Callback
from modules.Generics.RedOpsEncoder import RedOpsEncoder
from modules.Generics.RedOpsDecoder import RedOpsDecoder
import multiprocessing
from signal import SIGTERM
import logging
import requests
import atexit
import os
import string

# We load the .env files with the configuration of the agent.
try:
    with open(".agent_config", "r") as f:
        configJSON = json.load(f)

        # API_KEY checks
        if ("API_KEY") not in configJSON:
            raise Exception("API_KEY key not in .env file.")

        if len(configJSON["API_KEY"]) < 8:
            raise Exception("API_KEY must be greater than 7 caracters.")

        if ("MASTER_DOMAIN") not in configJSON:
            raise Exception("MASTER_DOMAIN key not in .env file.")

        if ("ONE_THREAD") not in configJSON:
            raise Exception("ONE_THREAD key not in .env file.")

        if ("DISABLE_MASTER_SERVER") not in configJSON:
            raise Exception("DISABLE_MASTER_SERVER key not in .env file.")
        
        if ("LOG_WHOLE_PETITIONS") not in configJSON:
            raise Exception("LOG_WHOLE_PETITIONS key not in .env file.")

        if ("ROOT") not in configJSON:
            raise Exception("ROOT key not in .env file")

        if ("WORDLISTS_PATH") not in configJSON:
            raise Exception("WORDLISTS_PATH key not in .env file")

        if ("SECURITYTRAILS_APIKEY") not in configJSON:
            raise Exception("SECURITYTRAILS_APIKEY key not in .env file")

        if ("SHODAN_APIKEY") not in configJSON:
            raise Exception("SHODAN_APIKEY key not in .env file")

        # Check root
        if configJSON["ROOT"]:
            if os.getuid() != 0:
                raise Exception("ROOT mode set but not root. Change supervisor user")
except FileNotFoundError:
    raise Exception("Enviroment file not found.")
except TypeError:
    raise Exception(".agent_config not valid JSON")

# Load wordlists list
try:
    wordlists = {"wordlists": [x for x in os.listdir(configJSON["WORDLISTS_PATH"])]}
except FileNotFoundError:
    raise Exception("Invalid wordlists folder.")

if(configJSON["LOG_WHOLE_PETITIONS"]):
    import http.client as http_client
    http_client.HTTPConnection.debuglevel = 1

app = Flask(__name__)
# Dictionary of jobs
runningJobs = dict()

# Dynamic Module load
def loadModule(moduleName):
    if not moduleName.endswith('.py'):
        moduleName = moduleName + '.py'
    spec = importlib.util.spec_from_file_location("modules", moduleName)
    foo = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(foo)

    module = foo.Module()

    return module

# Jobs management functions

def updateJob(data, id):

    # We serialize data to JSON before send it to the server
    postData = json.dumps({"finished": False, "jobId": id, "data": data}, cls=RedOpsEncoder)
    
    app.logger.debug("----------------------------JOB {} update----------------------------\n{}".format(id, postData))

    if(not configJSON["DISABLE_MASTER_SERVER"]):
        r = requests.post("https://{}/api/job/update".format(configJSON["MASTER_DOMAIN"]), headers={"Content-Type": "application/json"}, data=postData)
        app.logger.debug("----------------------------Response from master for JOB {} UPDATE----------------------------\n{}".format(id, r.text))

def finishJob(data, id):

    # We serialize data to JSON before send it to the server
    postData = json.dumps({"finished": True, "jobId": id, "data": data}, cls=RedOpsEncoder)

    app.logger.debug("----------------------------JOB {} finish----------------------------\n{}".format(id, postData))

    if(not configJSON["DISABLE_MASTER_SERVER"]):
        r = requests.post("https://{}/api/job/update".format(configJSON["MASTER_DOMAIN"]), headers={"Content-Type": "application/json"}, data=postData)
        app.logger.debug("----------------------------Response from master for JOB {} FINISH----------------------------\n{}".format(id, r.text))
    
def startJob(moduleName, id, data, spawn_process=False):
             
        module = loadModule("modules/{}".format(moduleName))
        # Add API key if needed
        
        if(moduleName == "SearchSubdomainsModule"):
            data = {"SECURITYTRAILS_APIKEY": configJSON["SECURITYTRAILS_APIKEY"], "data": data}
        elif(moduleName == "SearchServicesShodan"):
            data = {"SHODAN_APIKEY": configJSON["SHODAN_APIKEY"], "data": data}
        
        module.params = data
        module.moduleName = moduleName
        if(spawn_process):
            return multiprocessing.Process(target=module.run, args=(Callback(lambda data: updateJob(data, id), lambda data: finishJob(data, id), app.logger.debug,
         app.logger.warning, app.logger.error, app.logger.exception),))
        else:
            module.run(Callback(lambda data: updateJob(data, id), lambda data: finishJob(data, id), app.logger.debug,
         app.logger.warning, app.logger.error, app.logger.exception))


@app.route("/jobs", methods=["POST"])
def jobs():
    global runningJobs
    if request.method == "POST":

        requestJSON = json.loads(request.get_data(), object_hook=RedOpsDecoder)

        app.logger.debug("----------------------------NEW JOB----------------------------\n{}".format(requestJSON))

        if(requestJSON["id"] in runningJobs.keys()):
            return {"status": "A job with the same id is already running..."}

        # Check for invalid characters in module name
        for character in requestJSON["module"]:
            if character not in string.ascii_letters:
                app.logger.debug("Invalid Module Name: {}".format(requestJSON["module"]))
                return jsonify({'result':'Invalid Module Name'})

        if(configJSON["ONE_THREAD"]):
            # El modo debug usa solo un hilo para asi poder ver los errores en consola
            startJob(requestJSON["module"], requestJSON["id"], requestJSON["data"])
        else:
            job =  startJob(requestJSON["module"], requestJSON["id"], requestJSON["data"], True)
            # We save it in the runningjobs dict
            runningJobs[requestJSON["id"]] = job
            job.start()
            # We want to kill all workers on exit
            atexit.register(lambda p: p.terminate(), job)

        return jsonify({'result':'success'})

@app.route("/jobs/status", methods=["GET", "POST", "DELETE"])
def jobsinfo():
    global runningJobs

    if request.method == "GET":
        return jsonify({"jobs": list(runningJobs.keys())})
    else:
        # In other cases we want to read a job id
        jobID = request.get_json()["id"]

        job = runningJobs.get(jobID, None)
        if(job == None):
            return jsonify({"status": "404"})
        
        if request.method == "POST":
            return jsonify({"status": job.is_alive()})
        elif request.method == "DELETE":
            # We kill the process and check if dead
            job.terminate()
            status = job.is_alive()
            if(not status):
                # We delete the job from the dict
                del runningJobs[jobID]
                return jsonify({"status": True})
            else:
                return jsonify({"status": False})

if __name__ == "__main__":
    #context = ssl.SSLContext(ssl.PROTOCOL_TLSv1_2)
    #context.verify_mode = ssl.CERT_REQUIRED
    #context.load_verify_locations('certs/CA.pem')
    #context.load_cert_chain('certs/server.crt', 'certs/server.key')
    #app.run(ssl_context=context, host='127.0.0.1', debug=True)
    app.run(host='127.0.0.1', debug=True)

if __name__ != "__main__":
    gunicorn_logger = logging.getLogger("gunicorn.error")
    app.logger.handlers = gunicorn_logger.handlers
    app.logger.setLevel(gunicorn_logger.level)