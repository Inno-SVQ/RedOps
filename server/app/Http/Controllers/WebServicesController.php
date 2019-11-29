<?php

namespace App\Http\Controllers;

use App\Agent\Models\WebUrl;
use App\Audit;
use App\Domain;
use App\Job;
use App\Service;
use App\Utils;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Tebru\Gson\Gson;
use Yajra\DataTables\Facades\DataTables;

class WebServicesController extends Controller
{
    public function index($id, $serviceid)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $id]])->firstOrFail();
        $service = Service::where('id', $serviceid)->firstOrFail();
        if($service->getDomain()->parentCompany()->audit()->id !== $audit->id) {
            return abort(404);
        }
        $selectedAudit = $audit;
        return view('audits/enumeration/servicesdetail', compact('audit', 'selectedAudit', 'service'));
    }

    public function webtechnologies(Request $request) {

        $audit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();

        $job = new Job();
        $job->module = 'WappalyzerModule';
        $job->status = 0;
        $job->audit_id = $audit->id;
        $job->parameters = $request->data;
        $job->save();

        $services = Service::whereIn('id', json_decode($request->data))->get();

        $servicesAllowed = array();
        foreach ($services as $service) {
            if($service->getDomain()->parentCompany()->audit()->getOwner()->rid === Auth::user()->rid) {
                $serv = \App\Agent\Models\Service::fromEloquent($service);
                $serv->host = $service->getDomain()->domain;
                array_push($servicesAllowed, $serv);
            }
        }

        $data = array(
            'module' => $job->module,
            'id' => (string) $job->id,
            'data' => $servicesAllowed
        );

        $gson = Gson::builder()->build();
        $json = $gson->toJson($data);

        $resp = Utils::sendRequestToAgent($json);

        $serviceNames = array();
        foreach($services as $service) {
            array_push($serviceNames, $service->getDomain()->domain.':'.$service->port);
        }
        $strservices = implode(', ', $serviceNames);
        if(strlen($strservices)  > 200) {
            $strdomains = substr($strservices, 0, 200).'...';
        }
        Utils::sendNotificationUser(Auth::user()->rid, 'Job started', 'Searching technologies for '.$strservices, 'success');

        return $json;

    }

    public function fuzz(Request $request) {

        $audit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();

        $job = new Job();
        $job->module = 'WFuzzModule';
        $job->status = 0;
        $job->audit_id = $audit->id;
        $job->parameters = $request->data;
        $job->save();

        $services = Service::whereIn('id', json_decode($request->data))->get();

        $weburlsAllowed = array();
        foreach ($services as $service) {
            if($service->getDomain()->parentCompany()->audit()->getOwner()->rid === Auth::user()->rid && ($service->application_protocol === 'http' || $service->application_protocol === 'https')) {
                $webUrl = new WebUrl($service->id, $service->getDomain()->domain, $service->port, '', '', '', 0, 0);
                array_push($weburlsAllowed, $webUrl);
            }
        }

        $data = array(
            'module' => $job->module,
            'id' => (string) $job->id,
            'data' => $weburlsAllowed
        );

        $gson = Gson::builder()->build();
        $json = $gson->toJson($data);

        $resp = Utils::sendRequestToAgent($json);

        $serviceNames = array();
        foreach($weburlsAllowed as $weburl) {
            array_push($serviceNames, $weburl->host.':'.$weburl->port.'/');
        }
        $strservices = implode(', ', $serviceNames);
        if(strlen($strservices)  > 200) {
            $strdomains = substr($strservices, 0, 200).'...';
        }
        Utils::sendNotificationUser(Auth::user()->rid, 'Job started', 'Fuzzing webservices: '.$strservices, 'success');

        return $json;

    }

    public function directories($id, $serviceid) {
        $selectedService = Service::where('id', $serviceid)->firstOrFail();

        if($selectedService->getDomain()->parentCompany()->audit()->id !== $id || $selectedService->getDomain()->parentCompany()->audit()->getOwner()->rid !== Auth::user()->rid) {
            return abort(404);
        }

        $webUrls = DB::table('web_urls')
            ->where('service_id', $selectedService->id)
            ->select(['id', 'path', 'file_type', 'word_length', 'char_length', 'status_code']);

        return Datatables::of($webUrls)
            ->addColumn('checkbox', function ($webUrl) {
                return '<td><input class="checkbox-item" id="' . get_object_vars($webUrl)['id'] . '" type="checkbox" aria-label="...">';
            })
            ->addColumn('DT_RowId', function ($webUrl) {
                return get_object_vars($webUrl)['id'];
            })
            ->rawColumns(['checkbox'])
            ->removeColumn('id')
            ->make(true);
    }

}