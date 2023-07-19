<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Incident;
use App\Models\Inspection;
use App\Models\Route;
use App\Models\SAndT;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripReportController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();

        $vehicle_trips_info = new Collection();
        
        foreach ($vehicles as $key => $vehicle) {
            $trips = Trip::where('vehicle_id','=',$vehicle->id)->get();
            $trip_info = ["vehicle" => "", "trips" => []];
            $trip_info["vehicle"] = $vehicle;
            foreach($trips as $trip) {
                $num_of_students = count(SAndT::where('trip_id','=', $trip->id)->get());
                $num_of_incidents = count(Incident::where('trip','=', $trip->id)->get());
                $route_distance = Route::where('id','=', $trip->route_id)->first();
                $distance = 0;
                if ($route_distance) {
                    $distance = $route_distance->distance;
                }
                $trips_count_times_distance = count(DB::table('vehicle_trip_count')->where('vehicle_id','=',$vehicle->id)->where('trip_id','=', $trip->id)->get()) * $distance;
                $trip->num_of_students = $num_of_students;
                $trip->incidents = $num_of_incidents;
                $trip->distance = $trips_count_times_distance;
            }

            $vehicle->trips = $trips;

            $vehicle_trips_info->push($trip_info);
        }

        return view('reports.trip', compact('vehicles'));
    }


    public function generalTripReport() 
    {
        
        
    }

    public function show($id) 
    {
        $trip = Trip::find($id);

        $vehicle = Vehicle::where('id','=', $trip->vehicle_id)->first();

        $attendant = User::find($vehicle->attendant_id);

        $trip_attendance = Attendance::where('trip_id','=', $trip->id)->get();

        $trip_attendant_incidents = Incident::where('trip','=', $trip->id)->where('user_id','=', $attendant->id)->get();;

        $parents = User::where('user_type','=','parent')->get();
        $trip_parent_incidents = [];
        foreach ($parents as $key => $parent) {
            $incident = Incident::where('trip','=', $trip->id)->where('user_id','=', $parent->id)->get();
            array_push($trip_incidents, $incident);
        }

        return view('reports.specific-trip')->with([
            'trip' => $trip, 
            'vehicle' => $vehicle, 
            'attendant' => $attendant, 
            'trip_attendance' => $trip_attendance, 
            "trip_attendant_incidents" => $trip_attendant_incidents, 
            'trip_parent_incidents' => $trip_parent_incidents
        ]);
    }


    public function complianceReport($id)
    {
        $vehicle = Vehicle::find($id);

        $inspections = Inspection::where('vehicle_id','=', $vehicle->id)->get();

        return response([$vehicle, $inspections]);
    }
}
