<?php

namespace App\Http\Controllers;

use App\Audit;
use App\Service;
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
}