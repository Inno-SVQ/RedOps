<?php

namespace App\Http\Controllers\Api;

use App\Agent\Models\Company;
use App\Agent\Models\Credential;
use App\Agent\Models\Domain;
use App\Agent\Models\IP;
use App\Agent\Models\Service;
use App\Agent\Models\WebTechnology;
use App\Agent\Models\WebUrl;
use App\Audit;
use App\Http\Controllers\Controller;
use App\Job;
use App\Models\User;
use App\Events\CurrentJobsUpdate;
use App\Events\WsMessage;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobsController extends Controller
{

    private function decodeIP($data)
    {
        $ip = null;
        if(!is_null($data)){
            $id = null;
            if(array_key_exists("id", $data)){
                $id = $data['id'];
            }
            $ip = new IP($id, $data['value'], $data['isIPv4']);
        }
        return $ip;
    }

    private function decodeDomain($data){
        // Domains can have an IP object inside
        $id = null;
        if(array_key_exists("id",$data)){
            $id = $data['id'];
        }
        // We create the domain object
        return new Domain($id, $data['name'], $data['parent']);
    }

    private function decodeCompany($data){
        // Companies can have an Domain Object inside
        $domain = null;
        if(!is_null($data['mainDomain']) && $data['mainDomain']['type'] == "__domain__"){
            $domain = self::decodeDomain($data['mainDomain']);
        }
        $id = null;
        if(array_key_exists("id", $data)){
            $id = $data['id'];
        }
        return new Company($data['id'], $data['name'], $domain);
    }

    private function decodeService($data){
        if(isset($data['id'])) {
            return new Service($data['id'], $data['host'], $data['port'], $data['protocol'], $data['version'], $data['product'], $data['application_protocol']);
        }
        return new Service(null, $data['host'], $data['port'], $data['protocol'], $data['version'], $data['product'], $data['application_protocol']);
    }

    private function decodeCredential($data, $audit){
        return new Credential($data['username'], $data['password'], $data['domain'], $data['source'], $audit->id);
    }

    private function decodeTechnology($data){
        return new WebTechnology($data['name'], $data['icon'], $data['serviceId']);
    }

    private function decodeWeburl($data){
        return new WebUrl($data['serviceId'], $data['host'], $data['port'], $data['path'], $data['fileType'], $data['wordLength'], $data['charLength'], $data['statusCode']);
    }

    private function decodeJSON(array $data, $audit){

        $result = array();

        foreach($data['data'] as $element){
            if($element['type'] == "__domain__"){
                $result[] = self::decodeDomain($element);
            }

            if($element['type'] == "__ip__"){
                $result[] = self::decodeIP($element);
            }

            if($element['type'] == "__company__"){
                $result[] = self::decodeCompany($element);
            }

            if($element['type'] == "__service__"){
                $result[] = self::decodeService($element);
            }

            if($element['type'] == "__credential__"){
                $result[] = self::decodeCredential($element, $audit);
            }

            if($element['type'] == "__technology__"){
                $result[] = self::decodeTechnology($element, $audit);
            }

            if($element['type'] == "__weburl__"){
                $result[] = self::decodeWeburl($element, $audit);
            }
        }

        return $result;
    }

    public function update(Request $request) {

        $data = $request->json()->all();
        $job = Job::where('id', $data['jobId'])->firstOrFail();

        if($data['finished'] === true) {
            $job->status = 2;
            $job->progress = 100;
            $job->save();
        }

        $openJobs = count(Job::where([['audit_id', $job->audit_id], ['status', 0]])->get());
        $audit = Audit::where('id', $job->audit_id)->firstOrFail();
        $owner = User::where('id', $audit->owner)->firstOrFail();
        event(new WsMessage($owner->rid, 'jobUpdate', json_encode($openJobs)));
        event(new WsMessage($owner->rid, 'debug', json_encode($data)));

        $objects = self::decodeJSON($data, $audit);

        $modelsAdded = array();

        foreach ($objects as $obj) {
            $model = $obj->toEloquent();
            $model->from_job_id = $data['jobId'];
            try {
                $model->save();
                array_push($modelsAdded, basename(get_class($model)));
            } catch (\Exception $exception) {

            }
        }

        if(in_array('App\\Credential', $modelsAdded)) {
            event(new WsMessage($owner->rid, 'addedCredential', json_encode($data)));
        }

        if(in_array('App\\Domain', $modelsAdded)) {
            event(new WsMessage($owner->rid, 'addedDomain', json_encode($data)));
        }

        if(in_array('App\\Service', $modelsAdded)) {
            event(new WsMessage($owner->rid, 'addedService', json_encode($data)));
        }

        if(in_array('App\\WebTechnology', $modelsAdded)) {
            event(new WsMessage($owner->rid, 'addedTechnologies', json_encode($data)));
        }

        if(in_array('App\\WebUrl', $modelsAdded)) {
            event(new WsMessage($owner->rid, 'addedWeburls', json_encode($data)));
        }

        if($data['finished'] === true) {
            Utils::sendNotificationUser($job->getOwner()->rid, 'Job finished', \App\Http\Controllers\JobsController::getHumanModuleName($job->module).' job has been finished.', 'success');
        }

        return 'OK';
    }

}