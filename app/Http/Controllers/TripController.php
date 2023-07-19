<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\SAndT;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        
    }

    public function myCreate($id)
    {
        $vehicle = Vehicle::find(Crypt::decrypt($id));
        $grades = DB::table('student_classes')->get();
        $vehicle_routes = DB::table('vehicle_routes')->where('vehicle_id','=', $vehicle->id)->get();
        $user = Auth::user();
        return view('trips.create')->with([
            'vehicle' => $vehicle,
            'grades' => $grades,
            'vehicle_routes' => $vehicle_routes
        ]);
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
            'vehicle_id' => 'required'
        ]);

        if($request->route_time == 'select...') {
            return redirect()->back()->with('unsuccess', "Please select if it's AM or PM trip");
        }

        DB::transaction(function() use ($request) {
            $trip = new Trip();
            $trip->vehicle_id = $request->vehicle_id;
            $trip->route_id = $request->route;
            $trip->title = $request->title;
            $trip->time = $request->route_time;
            $trip->time_from = $request->from;
            $trip->time_to = $request->to;
            $trip->save();

            for ($i=0; $i < count($request->grades); $i++) { 
                DB::table('grade_groups')->insert([
                    'trip_id' => $trip->id,
                    'grade_id' => $request->grades[$i]
                ]);
            }
        });

       return redirect()->route('vehicles.index')->with('success', 'Record added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vehicle = Vehicle::find(Crypt::decrypt($id));


        $trips = Trip::where('vehicle_id', '=', $vehicle->id)->get();

        return view('trips.show')->with([
            'vehicle' => $vehicle,
            'trips' => $trips
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $trip = Trip::find(Crypt::decrypt($id));
        $grades = DB::table('student_classes')->get();
        $vehicle = Vehicle::find($trip->vehicle_id);
        $vehicle_routes = DB::table('vehicle_routes')->where('vehicle_id','=', $vehicle->id)->get();
        $trip_grades = DB::table('grade_groups')->where('trip_id','=', $trip->id)->get();
        if (! $trip) {
            return redirect()->back()->with('unsuccess', 'System error please try again');
        }

        return view('trips.edit', compact('trip','vehicle','vehicle_routes', 'grades', 'trip_grades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'vehicle_id' => 'required'
        ]);

        if($request->route_time == 'select...') {
            return redirect()->back()->with('unsuccess', "Please select if it's AM or PM trip");
        }

        $trip = Trip::find($id);
        $trip->vehicle_id = $request->vehicle_id;
        $trip->route_id = $request->route;
        $trip->title = $request->title;
        $trip->time = $request->route_time;
        $trip->time_from = $request->from;
        $trip->time_to = $request->to;
        DB::table('grade_groups')->where('trip_id','=', $trip->id)->delete();
        for ($i=0; $i < count($request->grades); $i++) { 
            DB::table('grade_groups')->insert([
                'trip_id' => $trip->id,
                'grade_id' => $request->grades[$i]
            ]);
        }

        if($trip->update()) {
            return redirect()->route('vehicles.index')->with('success', 'Record updated successfully');
        }

        return redirect()->route('vehicles.index')->with('unsuccess', 'System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trip = Trip::find($id);

        $check = SAndT::where('trip_id','=',$trip->id)->get();

        if ($check->isNotEmpty()) {
            return redirect()->back('unsuccess', 'Trip has allocated students');
        }

        if($trip->delete()) {
            return redirect()->route('vehicles.index')->with('success', 'Trip deleted successfully');
        } else {
            return redirect()->back('unsuccess', 'system error please try again');
        }
    }


    public function getVehicleTrip ($id) {

        
        $vehicle = Vehicle::find($id);

        //$trips = $vehicle->route->trips;


        


        $myselect = "<label id='myedittrips'>Select Trips</label><select multiple  name='trip[]' id='myeditselect' class='form-control' required>";

        foreach ($vehicle->trips as $trips) {
            $myselect .= "<option value='$trips->id'>Title: $trips->title, AM/PM: $trips->time,  From: $trips->time_from,  To: $trips->time_to</option>";
        }

        $myselect .= '</select>';



        return response($myselect);
    }


    public function getVehicleTripEdit (Request $request) {

        
        $vehicle = Vehicle::find($request->vehicle_id);

       

        $student = Student::find($request->student_id);

        $tripss = $vehicle->trips;

        $myselect = "<label id='myedittrips'>Select Trip</label><select multiple name='trip_id[]' id='myeditselect' class='form-control' required>";

        foreach ($tripss as $trips) {
            $sandt = SAndT::where('student_id', '=', $student->id)->where('trip_id', '=', $trips->id)->first(); 
            $selected = '';
            if ($sandt) {
                

                if ($trips->id == $sandt->trip_id) {
                    $selected = 'selected';
                }

            
            }
            $myselect .= "<option $selected value='$trips->id'>Title: $trips->title, AM/PM: $trips->time,  From: $trips->time_from,  To: $trips->time_to</option>";
            
        }

        $myselect .= '</select>';

        return response($myselect);
    }
}
