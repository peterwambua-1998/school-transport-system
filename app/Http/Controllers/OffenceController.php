<?php

namespace App\Http\Controllers;

use App\Models\DriverLicence;
use App\Models\Offence;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class OffenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offences = Offence::all();
        foreach ($offences as $key => $offence) {
            $vehicle = Vehicle::where('driver_id','=',$offence->user_id)->first();
            $license = DriverLicence::where('driver_id','=',$offence->user_id)->first();
            $user = User::find($offence->user_id);
            $offence->user = $user;
            $offence->vehicle = $vehicle;
            $offence->license = $license;
        }

        return view('offence.index', compact('offences'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('offence.create');
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
            'description'=>'required'
        ]);

        $offence = new Offence();
        $offence->user_id = $request->user;
        $offence->type = $request->type;
        $offence->offence_type = $request->offence_type;
        $offence->description = $request->description;
        $offence->disciplinary_action = $request->disciplinary_action;
        if($offence->save()) {
            return redirect()->route('offence.index')->with('success','Record added successfully');
        }
        return redirect()->back()->with('unsuccess','System error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Offence  $offence
     * @return \Illuminate\Http\Response
     */
    public function show(Offence $offence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Offence  $offence
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $offence = Offence::find($id);
        return view('offence.edit', compact('offence'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Offence  $offence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description'=>'required'
        ]);

        $offence = Offence::find($id);
        $offence->user_id = $request->user;
        $offence->type = $request->type;
        $offence->offence_type = $request->offence_type;
        $offence->description = $request->description;
        $offence->disciplinary_action = $request->disciplinary_action;
        if($offence->update()) {
            return redirect()->route('offence.index')->with('success','Record updated successfully');
        }
        return redirect()->back()->with('unsuccess','System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Offence  $offence
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $offence = Offence::find($id);
        $offence->status = 0;
        if ($offence->update()) {
            return redirect()->route('offence.index')->with('success','Record deactivated successfully');
        }
        return redirect()->back()->with('unsuccess','System error please try again');
    }

    public function activate(Request $request)
    {
        $offence = Offence::find($request->offence_id);
        $offence->status = 1;
        if ($offence->update()) {
            return redirect()->route('offence.index')->with('success','Record activated successfully');
        }
        return redirect()->back()->with('unsuccess','System error please try again');
    }

    /**
     * get user ie driver or attendant based on type
     */
    public function getUser(Request $request)
    {
        if ($request->type == 'driver') {
            $users = User::where('user_type','=','driver')->where('status','=',1)->get();

            return response($users);
        }

        if ($request->type == 'attendant') {
            $users = User::where('user_type','=','attendant')->where('status','=',1)->get();

            return response($users);
        }
    }
}
