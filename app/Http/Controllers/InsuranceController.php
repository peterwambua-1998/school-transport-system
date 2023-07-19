<?php

namespace App\Http\Controllers;

use App\Events\InsuranceExpired;
use App\Models\Insurance;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class InsuranceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = Vehicle::where('status','=',1)->get();
        foreach ($vehicles as $key => $vehicle) {
            $insurance = Insurance::where('vehicle_id','=',$vehicle->id)->where('status','=',1)->first();
            $vehicle->insurance = $insurance;
        }
        return view('insurance.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $vehicle = Vehicle::find(Crypt::decrypt($id));
        return view('insurance.create', compact('vehicle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'=>'required'
        ]);

        if ($request->status == 'select...') {
            return redirect()->back()->with('unsuccess','Please select insurance status');
        }

        if ($request->type == "select...") {
            return redirect()->back()->with('unsuccess','Please select insurance type');
        }
        $insurance = new Insurance();
        $insurance->vehicle_id = $request->vehicle_id;
        $insurance->type = $request->type;
        $insurance->ins_num = $request->ins_num;
        $insurance->ins_company = $request->company;
        $insurance->issue_date = $request->issue_date;
        $insurance->renew_date = $request->date_renewed;
        $insurance->validity = $request->validity;
        $renew_date = Carbon::createFromFormat('Y-m-d', $request->date_renewed);
        $expiry_date = $renew_date->addDays($request->validity);
        $insurance->exp_date = $expiry_date;
        $today = Carbon::now();
        if ($today->lt($expiry_date)) {
            $insurance->status = 1;
        } else {
            $insurance->status = 0;
        }
        
        if($insurance->save()){
            return redirect()->route('insurance.index')->with('success','Record added successfully');
        }
        return redirect()->route('insurance.index')->with('unsuccess','System eror please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Insurance  $insurance
     * @return \Illuminate\Http\Response
     */
    public function show(Insurance $insurance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Insurance  $insurance
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $insurance = Insurance::find(Crypt::decrypt($id));
        $vehicle = Vehicle::where('id','=', $insurance->vehicle_id)->first();

        return view('insurance.edit', compact('insurance','vehicle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Insurance  $insurance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'vehicle_id'=>'required'
        ]);
       
        if ($request->type == "select...") {
            return redirect()->back()->with('unsuccess','Please select insurance type');
        }
        $insurance = Insurance::find($id);
        $insurance->vehicle_id = $request->vehicle_id;
        $insurance->type = $request->type;
        $insurance->ins_num = $request->ins_num;
        $insurance->ins_company = $request->company;
        $insurance->issue_date = $request->issue_date;
        $insurance->renew_date = $request->date_renewed;
        $insurance->validity = $request->validity;
        $renew_date = Carbon::createFromFormat('Y-m-d', $request->date_renewed);
        $expiry_date = $renew_date->addDays($request->validity);
        $insurance->exp_date = $expiry_date;
        $today = Carbon::now();
        if ($today->lt($expiry_date)) {
            $insurance->status = 1;
        } else {
            $insurance->status = 0;
        }
        

        if($insurance->update()){
            return redirect()->route('insurance.index')->with('success','Record updated successfully');
        }
        return redirect()->route('insurance.index')->with('unsuccess','System eror please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Insurance  $insurance
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
    }

    public function disableInsurance($id)
    {
        $insurance = Insurance::find($id);
        $insurance->status = 0;
        if($insurance->update()){
            return redirect()->back()->with('success', 'Insurance deactivated');
        }
        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    public function activate($id)
    {
        $insurance = Insurance::find($id);
        $insurance->status = 1;
        if($insurance->update()){
            return redirect()->back()->with('success', 'Insurance Activated');
        }
        return redirect()->back()->with('unsuccess', 'System error please try again');
    }


    public function renew(Request $request)
    {
        $renew_date = Carbon::createFromFormat('Y-m-d', $request->date_renewed);
        $expiry_date = $renew_date->addDays($request->validity);

        $insurance = Insurance::find($request->insurance_id);
        $insurance->validity = $request->validity;
        $insurance->renew_date = $request->date_renewed;
        $insurance->exp_date = $expiry_date;
        $today = Carbon::now();
        if ($today->lt($expiry_date)) {
            $insurance->status = 1;
        } else {
            $insurance->status = 0;
        }
        $insurance->notification_send = 0;
        $insurance->notification_send_two = 0;
        if ($insurance->update()) {
            return redirect()->back()->with('success','insurance renewed');
        }
        return redirect()->back()->with('unsuccess','System error please try again');
    }
}
