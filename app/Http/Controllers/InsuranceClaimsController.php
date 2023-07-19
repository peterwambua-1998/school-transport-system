<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use App\Models\InsuranceClaims;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class InsuranceClaimsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $insurance = Insurance::find(Crypt::decrypt($id));
        $vehicle = Vehicle::find($insurance->vehicle_id);
        $claims = InsuranceClaims::where('insurance_id','=',$insurance->id)->get();
        return view('claims.create', compact('insurance','vehicle','claims'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->claim_garage_lat && !$request->claim_garage_lng) {
            return redirect()->back()->with('unsuccess','Select a valid garage location');
        }

        $request->validate([
            'insurance_id' => 'required'
        ]);

        $claim = new InsuranceClaims();
        $claim->insurance_id = $request->insurance_id;
        $claim->claim_number = $request->claim_number;
        $claim->claim_mileage = $request->claim_mileage;
        $claim->claim_date = $request->claim_date;
        $claim->claim_approve_date = $request->claim_approve_date;
        $claim->claim_garage = $request->claim_garage;
        $claim->claim_garage_lat = $request->claim_garage_lat;
        $claim->claim_garage_lng = $request->claim_garage_lng;
        $claim->comment = $request->comment;
        $claim->reported_by = Auth::user()->id;
        if ($request->has('report')) {
            $path_report = $request->file('report')->store('claim-report','public_uploads');
            $claim->report = $path_report;
        }
        if ($request->has('statement')) {
            $path_st = $request->file('statement')->store('claim-statement','public_uploads');
            $claim->statement = $path_st;
        }
        if ($claim->save()) {
            return redirect()->route('claims.show', Crypt::encrypt($request->insurance_id))->with('success', 'Record added successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InsuranceClaims  $insuranceClaims
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $insurance = Insurance::find(Crypt::decrypt($id));
        $vehicle = Vehicle::find($insurance->vehicle_id);
        $claims = InsuranceClaims::where('insurance_id','=',$insurance->id)->get();
        
        return view('claims.index', compact('insurance','vehicle','claims'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InsuranceClaims  $insuranceClaims
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $claim = InsuranceClaims::find($id);
        return view('claim.edit', compact('claim'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InsuranceClaims  $insuranceClaims
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InsuranceClaims $insuranceClaims)
    {
        if(!$request->claim_garage_lat && !$request->claim_garage_lng) {
            return redirect()->back()->with('unsuccess','Select a valid garage location');
        }
        
        $request->validate([
            'insurance_id' => 'required'
        ]);

        $claim = new InsuranceClaims();
        $claim->claim_number = $request->claim_number;
        $claim->claim_mileage = $request->claim_mileage;
        $claim->claim_date = $request->claim_date;
        $claim->claim_approve_date = $request->claim_approve_date;
        $claim->claim_garage = $request->claim_garage;
        $claim->claim_garage_lat = $request->claim_garage_lat;
        $claim->claim_garage_lng = $request->claim_garage_lat;
        $claim->comment = $request->comment;
        $claim->reported_by = Auth::user()->id;
        if ($request->has('report')) {
            if ($claim->report) {
                Storage::disk('public_uploads')->delete($claim->report);
            }
            $path_report = $request->file('report')->store('claim-report','public_uploads');
            $claim->report = $path_report;
        }
        if ($request->has('statement')) {
            if ($claim->statement) {
                Storage::disk('public_uploads')->delete($claim->statement);
            }
            $path_st = $request->file('statement')->store('claim-statement','public_uploads');
            $claim->statement = $path_st;
        }
        if ($claim->save()) {
            return redirect()->route('insurance.index')->with('success', 'Record added successfully');
        }
        return redirect()->route('insurance.index')->with('unsuccess', 'System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InsuranceClaims  $insuranceClaims
     * @return \Illuminate\Http\Response
     */
    public function destroy(InsuranceClaims $insuranceClaims)
    {
        //
    }

    public function downloadReport($id)
    {
        $claim = InsuranceClaims::find(Crypt::decrypt($id));

        $path = Storage::disk('public_uploads')->path($claim->report);
        $ext = pathinfo(Storage::disk('public_uploads')->path($claim->report),PATHINFO_EXTENSION);
        $file_name = 'claim-report'.$claim->claim_number.'.'.$ext;
        return response()->download($path, $file_name);
    }

    public function downloadStatement($id)
    {
        $claim = InsuranceClaims::find(Crypt::decrypt($id));

        $path = Storage::disk('public_uploads')->path($claim->statement);
        $ext = pathinfo(Storage::disk('public_uploads')->path($claim->statement),PATHINFO_EXTENSION);
        $file_name = 'claim-statement'.$claim->claim_number.'.'.$ext;
        return response()->download($path, $file_name);

    }
}
