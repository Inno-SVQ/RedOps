<?php


namespace App\Http\Controllers;

use App\Audit;
use App\Company;
use App\Domain;
use App\Job;
use App\Models\User;
use App\JobNotification;
use App\Utils;
use App\Events\CurrentJobsUpdate;
use App\Events\WsMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Tebru\Gson\Gson;
use Yajra\DataTables\Facades\DataTables;

class EnumerationController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $id]])->firstOrFail();
        $selectedAudit = $audit;
        return view('audits/enumeration/enumeration', compact('audit', 'selectedAudit'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function companies($id)
    {
        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $id]])->firstOrFail();

        foreach ($selectedAudit->companies() as &$company) {
            foreach ($selectedAudit->companies() as $company2) {
                if ($company->parent === $company2->id) {
                    $company->parentName = $company2->name;
                    break;
                }
            }
        }

        return view('audits/enumeration/companies', compact('selectedAudit'));
    }

    public function addCompany(Request $request)
    {
        $companyJson = json_decode($request->data);

        $auditId = $request->id;
        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $auditId]])->firstOrFail();

        if ($companyJson->parent === '-') {
            $companyJson->parent = null;
        }

        $company = new Company();
        $company->audit_id = $selectedAudit->id;
        $company->name = $companyJson->name;
        $company->parent = $companyJson->parent;
        $company->country = $companyJson->country;
        $company->linkedin = $companyJson->linkedin;
        $company->save();

        if(!empty($companyJson->domain)) {
            $mainDomain = new Domain();
            $mainDomain->domain = $companyJson->domain;
            $mainDomain->company_id = $company->id;
            $mainDomain->is_main = true;
            $mainDomain->save();
        }

        $company->country = '';
        if ($company->parent != null) {
            $parentCompany = Company::where('id', $companyJson->parent)->first();
            if ($parentCompany != null) {
                $company->parentName = $parentCompany->name;
            }
        } else {
            $company->parentName = '';
        }

        event(new WsMessage(Auth::user()->rid, 'addedCompany', json_encode($company)));

        return 'OK';
    }

    function findDomains(Request $request)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();

        $job = new Job();
        $job->module = 'SearchDomain';
        $job->status = 0;
        $job->audit_id = $audit->id;
        $job->parameters = $request->data;
        $job->save();

        $companies = Company::whereIn('id', json_decode($request->data))
            ->where('audit_id', $audit->id)
            ->get(['id', 'name', 'domain']);

        $jsonCompanies = array();

        foreach ($companies as $company) {
            array_push($jsonCompanies, \App\Agent\Models\Company::fromEloquent($company));
        }

        $data = array(
            'module' => $job->module,
            'id' => (string) $job->id,
            'data' => $jsonCompanies
        );

        $gson = Gson::builder()->build();
        $json = $gson->toJson($data);

        $resp = Utils::sendRequestToAgent($json);

        $openJobs = count(Job::where([['audit_id', $job->audit_id], ['status', 0]])->get());
        $owner = User::where('id', $audit->owner)->firstOrFail();

        $companyNames = '';
        $out = array();
        foreach ($companies as $comp) {
            array_push($out, $comp->name);
        }
        $companyNames = implode(', ', $out);

        $notif = new JobNotification();
        $notif->user_id = Auth::id();
        $notif->job_id = $job->id;
        $notif->title = 'Find domains';
        $notif->content = 'Searching domains for ' . $companyNames;
        $notif->read = false;
        $notif->save();

        event(new WsMessage($owner->rid, 'jobUpdate', json_encode($openJobs)));
        event(new WsMessage($owner->rid, 'notification', json_encode($notif)));

        return $json;
    }

    function findSubdomains(Request $request)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();

        $job = new Job();
        $job->module = 'SearchSubdomainsModule';
        $job->status = 0;
        $job->audit_id = $audit->id;
        $job->parameters = $request->data;
        $job->save();

        $domains = Domain::whereIn('id', json_decode($request->data))
            ->get();

        $domainsAllowed = array();
        foreach ($domains as $domain) {
            if($domain->parentCompany()->audit()->owner === Auth::id()) {
                array_push($domainsAllowed, \App\Agent\Models\Domain::fromEloquent($domain));
            }
        }

        $data = array(
            'module' => $job->module,
            'id' => (string) $job->id,
            'data' => $domainsAllowed
        );

        $gson = Gson::builder()->build();
        $json = $gson->toJson($data);

        $resp = Utils::sendRequestToAgent($json);

        $domainNames = array();
        foreach($domainsAllowed as $domain) {
            array_push($domainNames, $domain->name);
        }
        $strdomains = implode(', ', $domainNames);
        if(strlen($strdomains)  > 200) {
            $strdomains = substr($strdomains, 0, 200).'...';
        }
        Utils::sendNotificationUser(Auth::user()->rid, 'Job started', 'Searching subdomains for '.$strdomains, 'success');

        return $json;
    }

    function deleteCompanies(Request $request)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();

        $companies = Company::whereIn('id', json_decode($request->data))
            ->where('audit_id', $audit->id)
            ->delete();

        event(new WsMessage(Auth::user()->rid, 'deletedCompanies', $request->data));

        return json_encode($companies);
    }

    function companiesAjax(Request $request)
    {

        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();

        $companies = DB::table('companies')
            ->where('audit_id', $selectedAudit->id)
            ->select(['id', 'name', 'name', 'parent', 'country', 'domain', 'linkedin']);

        return Datatables::of($companies)
            ->addColumn('checkbox', function ($company) {
                return '<td><input class="checkbox-item" id="' . get_object_vars($company)['id'] . '" type="checkbox" aria-label="...">';
            })
            ->addColumn('DT_RowId', function ($company) {
                return get_object_vars($company)['id'];
            })
            ->editColumn('parent', function ($company) {
                $parent = Company::where('id', get_object_vars($company)['parent'])->first();
                if ($parent != null) {
                    return $parent->name;
                }
                return '';
            })
            ->rawColumns(['checkbox'])
            ->removeColumn('id')
            ->make(true);
    }

    public function addDomain(Request $request)
    {
        $domainJson = json_decode($request->data);

        $auditId = $request->id;
        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $auditId]])->firstOrFail();

        if ($domainJson->company_id === '-') {
            $domainJson->company_id = null;
        }

        if (!isset($domainJson->company_id)) {
            $parentDomain = Domain::where('id', $domainJson->company_id)->firstOrFail();
            $domainJson->company_id = $parentDomain->id;
        }

        $parentCompany = Company::where([['audit_id', $auditId], ['id', $domainJson->company_id]])->firstOrFail();

        $domain = new Domain();
        $domain->company_id = $parentCompany->id;
        $domain->domain = $domainJson->domain;
        if(isset($domainJson->domain_id)) {
            $domain->domain_id = $domainJson->domain_id;
        }
        $domain->save();

        event(new WsMessage(Auth::user()->rid, 'addedDomain', json_encode($domain)));

        return 'OK';
    }

    public function domains($id)
    {
        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $id]])->firstOrFail();

        foreach ($selectedAudit->companies() as &$company) {
            foreach ($selectedAudit->companies() as $company2) {
                if ($company->parent === $company2->id) {
                    $company->parentName = $company2->name;
                    break;
                }
            }
        }

        return view('audits/enumeration/domains', compact('selectedAudit'));
    }

    function domainsAjax(Request $request)
    {

        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();
        $companiesIds = Company::where('audit_id', $selectedAudit->id)->get(['id']);

        $domains = DB::table('domains')
            ->whereIn('company_id', $companiesIds)
            ->select(['id', 'domain', 'company_id', 'domain_id', 'ip']);

        return Datatables::of($domains)
            ->addColumn('checkbox', function ($domain) {
                return '<td><input class="checkbox-item" id="' . get_object_vars($domain)['id'] . '" type="checkbox" aria-label="...">';
            })
            ->addColumn('DT_RowId', function ($domain) {
                return get_object_vars($domain)['id'];
            })
            ->editColumn('domain_id', function ($domain) {
                $parentDomain = Domain::where('id', get_object_vars($domain)['domain_id'])->first();
                if ($parentDomain != null) {
                    return $parentDomain->domain;
                }
                return '';
            })
            ->editColumn('company_id', function ($domain) {
                $parentCompany = Company::where('id', get_object_vars($domain)['company_id'])->first();
                if ($parentCompany != null) {
                    return $parentCompany->name;
                }
                return '';
            })
            ->rawColumns(['checkbox'])
            ->removeColumn('id')
            ->make(true);
    }

    function deleteDomains(Request $request)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();
        $companiesIds = Company::where('audit_id', $audit->id)->get(['id']);
        $domains = Domain::whereIn('id', json_decode($request->data))
            ->whereIn('company_id', $companiesIds)
            ->delete();

        event(new WsMessage(Auth::user()->rid, 'deletedDomains', $request->data));

        return json_encode($domains);
    }

}