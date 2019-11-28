<?php

namespace App\Http\Controllers;

use App\Audit;
use App\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $selectedAudit = Audit::where([['owner', Auth::id()], ['id', $request->id]])->firstOrFail();
        return view('audits/jobs/jobs', compact('selectedAudit'));
    }

    public static function getHumanModuleName($module)
    {
        switch ($module) {
            case 'SearchSubdomainsModule':
                return 'Search subdomains';
            case 'SearchDomain':
                return 'Search domains';
            case 'PortScanModule':
                return 'Port scan';
            default:
                return $module;
        }
    }

}
