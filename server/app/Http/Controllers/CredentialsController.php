<?php

namespace App\Http\Controllers;

use App\Audit;
use App\Company;
use App\Domain;
use App\Events\WsMessage;
use App\Credential;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class CredentialsController extends Controller
{

    public function index($id)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $id]])->firstOrFail();
        $selectedAudit = $audit;
        return view('audits/enumeration/credentials', compact('audit', 'selectedAudit'));
    }

    function credentialsAjax(Request $request)
    {
        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();

        $credentials = DB::table('credentials')
            ->whereIn('audit_id', $selectedAudit)
            ->select(['id', 'username', 'password', 'domain', 'source']);

        return Datatables::of($credentials)
            ->addColumn('checkbox', function ($credential) {
                return '<td><input class="checkbox-item" id="' . get_object_vars($credential)['id'] . '" type="checkbox" aria-label="...">';
            })
            ->addColumn('DT_RowId', function ($credential) {
                return get_object_vars($credential)['id'];
            })
            ->rawColumns(['checkbox'])
            ->make(true);
    }

    public function addCredential(Request $request)
    {
        $serviceJson = json_decode($request->data);

        $auditId = $request->id;
        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $auditId]])->firstOrFail();

        $credential = new Credential();
        $credential->username = $serviceJson->username;
        $credential->password = $serviceJson->password;
        $credential->domain = $serviceJson->domain;
        $credential->audit_id = $auditId;

        try {
            $credential->save();
            event(new WsMessage(Auth::user()->rid, 'addedCredential', json_encode($credential)));
        } catch (QueryException $e) {
            return 'Duplicate entry';
        }

        return 'OK';
    }

    function deleteCredentials(Request $request)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();
        $credentials = Credential::where('audit_id', $audit->id)->whereIn('id', json_decode($request->data))->delete();
        event(new WsMessage(Auth::user()->rid, 'deletedCredentials', $request->data));

        return json_encode($credentials);
    }


}