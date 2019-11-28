<?php

namespace App\Http\Controllers\Api;

use App\Agent\Models\Company;
use App\Agent\Models\Domain;
use App\Agent\Models\IP;
use App\Agent\Models\Service;
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
        return new Service($data['host'], $data['port'], $data['protocol'], $data['version'], $data['product'], $data['application_protocol']);
    }

    private function decodeJSON(array $data){

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

        $objects = self::decodeJSON($data);

        $modelsAdded = array();

        foreach ($objects as $obj) {
            $model = $obj->toEloquent();
            $model->from_job_id = $data['jobId'];
            try {
                $model->save();
                array_push($modelsAdded, basename(get_class($model)));
            } catch (Exception $e) {

            }
        }

        if(in_array('App\\Domain', $modelsAdded)) {
            event(new WsMessage($owner->rid, 'addedDomain', json_encode($data)));
        }

        if(in_array('App\\Service', $modelsAdded)) {
            event(new WsMessage($owner->rid, 'addedService', json_encode($data)));
        }

        if($data['finished'] === true) {
            Utils::sendNotificationUser($job->getOwner()->rid, 'Job finished', \App\Http\Controllers\JobsController::getHumanModuleName($job->module).' job has been finished.', 'success');
        }

        return 'OK';
    }

}