<?php

namespace App\Http\Controllers;

use App\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $audits = Audit::where('owner', Auth::id())->get();
        return view('audits/audits', compact('audits'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('audits/create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createExecute(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        $audit = new Audit();
        $audit->name = $request->name;
        $audit->id = Uuid::uuid4();
        $audit->startDate = $request->start_date;
        $audit->endDate = $request->end_date;
        $audit->owner = Auth::id();
        $audit->save();

        return redirect()->route('audits');
    }

    public function auditDetail($id)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $id]])->firstOrFail();
        $selectedAudit = $audit;
        return view('audits/detail', compact('audit', 'selectedAudit'));
    }

    public function delete($id)
    {
        $audit = Audit::where([['owner', Auth::id()], ['id', $id]])->firstOrFail();
        $audit->delete();

        return redirect()->route('audits');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Audit $audit
     * @return \Illuminate\Http\Response
     */
    public function show(Audit $audit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Audit $audit
     * @return \Illuminate\Http\Response
     */
    public function edit(Audit $audit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Audit $audit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Audit $audit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Audit $audit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Audit $audit)
    {
        //
    }
}
