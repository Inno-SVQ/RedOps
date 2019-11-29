<?php


namespace App\Http\Controllers;

use App\Audit;
use App\Company;
use App\Domain;
use App\Job;
use App\Models\User;
use App\JobNotification;
use App\Service;
use App\Utils;
use App\Events\CurrentJobsUpdate;
use App\Events\WsMessage;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Tebru\Gson\Gson;
use Yajra\DataTables\Facades\DataTables;

class ServicesController extends Controller {

    public function services($id)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $id]])->firstOrFail();
        $selectedAudit = $audit;
        return view('audits/enumeration/services', compact('audit', 'selectedAudit'));
    }

    function servicesAjax(Request $request)
    {

        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();

        $allHosts = array();
        foreach ($selectedAudit->companies() as $company) {
            foreach ($company->domains() as $domain) {
                array_push($allHosts, $domain->id);
            }
        }

        $services = DB::table('services')
            ->whereIn('host_id', $allHosts)
            ->select(['id', 'host_id', 'protocol', 'port', 'product', 'version', 'application_protocol']);

        return Datatables::of($services)
            ->addColumn('checkbox', function ($service) {
                return '<td><input class="checkbox-item" id="' . get_object_vars($service)['id'] . '" type="checkbox" aria-label="...">';
            })
            ->addColumn('DT_RowId', function ($service) {
                return get_object_vars($service)['id'];
            })
            ->addColumn('host', function ($service) {

                $domain = Domain::where('id', $service->host_id)->first();

                if($service->application_protocol == 'http' or $service->application_protocol == 'https') {
                    return '<a type="button" href="'.route('servicedetail', ['id' => $domain->parentCompany()->audit()->id, 'serviceid' => $service->id]).'" class="btn btn-default" aria-label="Left Align">'.$domain->domain.' <span class="glyphicon glyphicon-globe" aria-hidden="true"></span></a>';
                }

                if ($domain != null) {
                    return $domain->domain;
                }
                return '';
            })
            ->rawColumns(['checkbox', 'host'])
            ->removeColumn('id')
            ->removeColumn('host_id')
            ->make(true);
    }

    public function addService(Request $request)
    {
        $serviceJson = json_decode($request->data);

        $auditId = $request->id;
        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $auditId]])->firstOrFail();

        $service = new Service();
        $service->host_id = $serviceJson->host;
        $service->port = $serviceJson->port;
        $service->protocol = $serviceJson->protocol;
        $service->application_protocol = $serviceJson->application_protocol;
        $service->product = $serviceJson->product;
        $service->version = $serviceJson->version;

        try {
            $service->save();
            event(new WsMessage(Auth::user()->rid, 'addedService', json_encode($service)));
        } catch (QueryException $e) {
            return 'Duplicate entry';
        }

        return 'OK';
    }

    function findServices(Request $request)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();

        $domains = Domain::whereIn('id', json_decode($request->data))
            ->get();

        $domainsAllowed = array();
        foreach ($domains as $domain) {
            if($domain->parentCompany()->audit()->owner === Auth::id()) {
                array_push($domainsAllowed, \App\Agent\Models\Domain::fromEloquent($domain));
            }
        }

        $ports = array();

        if(strpos($request->ports, ',') !== false) {
            $port_groups = explode(',', $request->port);
            foreach ($port_groups as $port_group) {
                array_push($ports,(string) $port_group);
            }
        } else {
            array_push($ports,(string) $request->port);
        }


        $dataOptions = array(
            'options' => array(
                'mode' => $request->protocol,
                'ports' => $ports
            ),
            'hosts' => $domainsAllowed
        );

        $job = new Job();
        $job->module = 'PortScanModule';
        $job->status = 0;
        $job->audit_id = $audit->id;
        $job->parameters = json_encode($dataOptions);
        $job->save();

        $data = array(
            'module' => $job->module,
            'id' => (string) $job->id,
            'data' => $dataOptions
        );

        $gson = Gson::builder()->build();
        $json = $gson->toJson($data);

        $resp = Utils::sendRequestToAgent($json);

        return $json;
    }

    function deleteServices(Request $request)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();
        $companiesIds = Company::where('audit_id', $audit->id)->get(['id']);
        $domainsIds = Domain::whereIn('company_id', $companiesIds)->get(['id']);
        $services = Service::whereIn('host_id', $domainsIds)->whereIn('id', json_decode($request->data))->delete();
        event(new WsMessage(Auth::user()->rid, 'deletedServices', $request->data));

        return json_encode($services);
    }


}
