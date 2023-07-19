<?php

namespace App\Http\Controllers;

use App\Models\Geofence;
use App\Models\Route;
use App\Models\RoutePolyline;
use App\Models\SAndT;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use stdClass;

class TrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $user = Auth::user();

        $driver = User::find(Crypt::decrypt($id));

        $vehicle = Vehicle::where('driver_id','=',$driver->id)->first();

        $vehicle_image = $vehicle->image;
    
        $notifications = User::find($user->id)->unreadNotifications;

        
        return view('vehicle.track')->with([
            'notifications' => $notifications,
            'driver' => $driver,
            'vehicle' => $vehicle->id,
            'vehicle_image'=> $vehicle_image,
            'veh' => $vehicle
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function allVehicles($id)
    {

        $vehicle = Vehicle::find($id);
        $locations = [];
        $final = [];
        $arr = [$vehicle->latitude - 0, $vehicle->longitude - 0, $vehicle->id, $vehicle->title];
        $geofence = DB::table('vehicle_geo_fence')->where('vehicle_id','=',$vehicle->id)->get();
        //get vehicle trip at the moment
        $trips = Trip::where('vehicle_id','=',$vehicle->id)->get();
        $routes = new Collection();
    
        foreach ($trips as $key => $trip) {
            $start = Carbon::createFromFormat('H:i:s', $trip->time_from);
            $end = Carbon::createFromFormat('H:i:s', $trip->time_to);
            $time = Carbon::createFromFormat('H:i:s', date('H:i:s'));

            if ($time->between($start, $end)) {
                $route = Route::find($trip->route_id);
                $coordinates = RoutePolyline::where('route_id','=', $route->id)->first();
                $route->coordinates = $coordinates;
                $route->trip_id = $trip->id;
                $routes->push($route);
                
            }
        }
        
        array_push($final, $arr, $geofence, $routes);
            
        return response($final);
    }


    public function getVehicle(Request $request)
    {
        $vehicle = Vehicle::find($request->id);

        $driver = User::where('id', '=', $vehicle->driver_id)->first();

        $arr = [$vehicle, $driver];

        return response($arr);
    }

    public function getTripStudentd($id) {
        $trip = Trip::find($id);
        $sant = SAndT::where('trip_id','=', $id)->get();
        $stds = new Collection();

        foreach ($sant as $key => $st) {
            $student = Student::find($st->student_id);
            $parent = User::find($student->parent_id);
            $obj = new stdClass;
            $obj->name = $student->first_name . ' '.$student->last_name;
            $obj->grade = DB::table('student_classes')->where('id','=', $student->grade)->first()->name;
            $obj->stream = Stream::find($student->stream)->name;
            $obj->parent = $parent->name;
            $obj->parent_phone = $parent->phone_num;
            $stds->push($obj);
        }

        return response([$trip, $stds]);
    }

}
