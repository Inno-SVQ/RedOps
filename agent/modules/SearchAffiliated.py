import json
import urllib

class Module(BaseModule):

    def run(self, callback):

        data = self.params["data"]
        api_key = self.params["api_key"]

        app=SearchAffiliated(api_key,callback)

        for index, domain in enumerate(data):
            res=app.getAffiliated(domain.name)
            jsonStr=[]
            for i in res:
                jsonStr.append(Company())

            # In the last loop we want to finish the task in the server
            if(index < len(self.params) - 1):
                callback.update(jsonStr)

            callback.finish(jsonStr)

class SearchAffiliated:

    def __init__(self, api_key,callback):
        self.api_key = api_key
        self.callback = callback

    def getAffiliated(self,name):
        api_key = self.api_key
        query = name
        service_url = 'https://kgsearch.googleapis.com/v1/entities:search'
        params = {
            'query': query,
            'limit': 100,
            'indent': True,
            'key': api_key,
        }
        url = service_url + '?' + urllib.urlencode(params)
        response = json.loads(urllib.urlopen(url).read())
        for element in response['itemListElement']:
          print element['result']['name'] + ' (' + str(element['resultScore']) + ')'
