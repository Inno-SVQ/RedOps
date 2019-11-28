<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;


class BroadcastController extends Controller
{

    public function authenticate(Request $request)
    {
        return Broadcast::auth($request);
    }

}