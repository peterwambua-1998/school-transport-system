<?php

namespace App\Http\Controllers;

use App\Events\DlExpired;
use App\Models\DriverLicence;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\DlExpredNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;

class DriverLicenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drivers = User::where('user_type','=','driver')->where('status','=',1)->get();
        foreach ($drivers as $key => $driver) {
            $licence = DriverLicence::where('driver_id','=', $driver->id)->first();
            $vehicle = Vehicle::where('driver_id','=',$driver->id)->first();
            $driver->licence = $licence;
            $driver->vehicle = $vehicle;
        }
        return view('drivers_licence.index', compact('drivers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $driver = User::find(Crypt::decrypt($id));
        return view('drivers_licence.create', compact('driver'));
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
            'driver_id' => 'required',
            'dl_number' => 'required',
            'dl_class' => 'required',
            'date_issued' => 'required'
        ]);

        $licence = new DriverLicence();
        $licence->driver_id = $request->driver_id;
        $licence->dl_number = $request->dl_number;
        $licence->dl_class = $request->dl_class;
        $licence->date_issued = $request->date_issued;
        $licence->date_renewed = $request->date_renewed;
        $licence->validity = $request->validity;
        

        $renew_date = Carbon::createFromFormat('Y-m-d', $request->date_renewed);
        $expiry_date = $renew_date->addYears($request->validity)->addDay();
        $licence->exp_date = $expiry_date;
        $today = Carbon::now();
        if ($today->lt($expiry_date)) {
            $licence->status =  1;
        } else {
            $licence->status =  0;
        }

        if ($licence->save()) {
            return redirect()->route('license.index')->with('success','Record added successfully');
        }
        return redirect()->back()->with('unsuccess','Sytem error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $driver = User::find(Crypt::decrypt($id));
        $license = DriverLicence::where('driver_id','=', $driver->id)->first();
        $driver->license = $license;
        return view('drivers_licence.edit', compact('driver'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DriverLicence  $driverLicence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required',
            'dl_number' => 'required',
            'dl_class' => 'required',
            'date_issued' => 'required'
        ]);

        $licence = DriverLicence::find($id);
        $licence->driver_id = $request->driver_id;
        $licence->dl_number = $request->dl_number;
        $licence->dl_class = $request->dl_class;
        $licence->date_issued = $request->date_issued;
        $licence->date_renewed = $request->date_renewed;
        $licence->validity = $request->validity;

        $renew_date = Carbon::createFromFormat('Y-m-d', $request->date_renewed);
        $expiry_date = $renew_date->addYears($request->validity)->addDay();;
        $licence->exp_date = $expiry_date;
        $today = Carbon::now();
        if ($today->lt($expiry_date)) {
            $licence->status =  1;
        } else {
            $licence->status =  0;
        }

        if ($licence->save()) {
            return redirect()->route('license.index')->with('success','Record updated successfully');
        }
        return redirect()->back()->with('unsuccess','Sytem error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DriverLicence  $driverLicence
     * @return \Illuminate\Http\Response
     */
    public function destroy(DriverLicence $driverLicence)
    {
        //
    }

    public function renew(Request $request)
    {
        $renew_date = Carbon::createFromFormat('Y-m-d', $request->date_renewed);
        $expiry_date = $renew_date->addYears($request->validity);

        $dl = DriverLicence::find($request->dl_id);
        $dl->validity = $request->validity;
        $dl->date_renewed = $request->date_renewed;
        $dl->exp_date = $expiry_date;
        $today = Carbon::now();
        if ($today->lt($expiry_date)) {
            $dl->status = 1;
        } else {
            $dl->status = 0;
        }
        $dl->notification_send = 0;
        $dl->notification_send_two = 0;
        if ($dl->update()) {
            return redirect()->back()->with('success','Driving license renewed');
        }
        return redirect()->back()->with('unsuccess','System error please try again');
    }
}
