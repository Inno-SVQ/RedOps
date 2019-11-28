<?php

namespace App\Http\Controllers;

use App\Audit;
use App\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

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


    }

}