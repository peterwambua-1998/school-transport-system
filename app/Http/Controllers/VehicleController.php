<?php

namespace App\Http\Controllers;

use App\Events\VehicleLocation;
use App\Models\Attendance;
use App\Models\BusMaintenance;
use App\Models\Garage;
use App\Models\Geofence;
use App\Notifications\VehicleOutOfFence;
use App\Models\Route;
use App\Models\SchoolAttendance;
use App\Models\SchoolTrip;
use App\Models\Settings;
use App\Models\StandinBus;
use App\Models\Student;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function fireEvent()
    {
        
        //-1.2834096388459237, 36.87279276298031
        event(new VehicleLocation('-1.2803960447960747','36.845498752743254',1,'70','50'));

        return "success";
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = Vehicle::all();

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('vehicle.index')->with(['vehicles' => $vehicles, 'notifications' => $notifications]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $settings = Settings::find(1);
        if (! $settings) {
            return redirect()->route('settings.create')->with('unsuccess', 'Please register system settings');
        }
        $garage = Garage::where('active','=',1)->first();
        if (! $garage) {
            return redirect()->back()->with('unsuccess','Kindly add active garage.');
        }
        $routes = Route::where('status','=',1)->get();
        $drivers = User::where('user_type', 'LIKE', 'driver')->where('status','=',1)->get();
        $attendants = User::where('user_type', 'LIKE', 'attendant')->where('status','=',1)->get();
        foreach ($drivers as $key => $driver) {
            $vehicle = Vehicle::where('driver_id','=',$driver->id)->where('status','=',1)->first();

            if($vehicle) {
                $drivers->forget($key);
            }
        }
        foreach ($attendants as $key => $attendant) {
            $vehicle = Vehicle::where('attendant_id','=',$attendant->id)->where('status','=',1)->first();

            if($vehicle) {
                $attendants->forget($key);
            }
        }

        return view('vehicle.create')->with(['attendants'=> $attendants, 'routes'=> $routes, 'drivers' => $drivers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->arrone) {
            return redirect()->back()->with('unsuccess','Please create vehicle geo fence.');
        }
        $request->validate([
            'title' => 'required',
            'platenum' => 'required',
            'driver' => 'required',
            'num_of_seats' => 'required'
        ]);

        $garage = Garage::where('active','=',1)->first();

        if (! $garage) {
            return redirect()->back()->with('unsuccess','Kindly add active garage.');
        }

        DB::transaction(function() use ($request, $garage) {
            //get school location
            $settings = Settings::find(1);
            //store vehicle details
            $vehicle = new Vehicle();
            $vehicle->title = $request->title;
            $vehicle->plate_num = $request->platenum;
            $vehicle->driver_id = $request->driver;
            $vehicle->num_of_seats = $request->num_of_seats;
            $vehicle->latitude = $settings->lat;
            $vehicle->longitude = $settings->lng;
            $vehicle->attendant_id = $request->attendant;
            $vehicle->mileage = $request->mileage;
            $vehicle->last_service = $request->last_service;
            $vehicle->service_interval = $request->service_interval;
            $vehicle->active = 1;

            if ($request->has('image')) {
                $image_path = $request->file('image')->store('logo', 'public_uploads');
                $vehicle->image = $image_path;
            }
            $vehicle->save();

            //give attendant vehicle_id
            $user = User::find($request->attendant);
            $user->vehicle_id = $vehicle->id;
            $user->update();

            //vehicle route
            for ($i=0; $i < count($request->routes); $i++) { 
                DB::table('vehicle_routes')->insert([
                    'vehicle_id' => $vehicle->id,
                    'route_id' => $request->routes[$i]
                ]);
            }

            $coords = count($request->arrone);

            for ($i=0; $i < $coords; $i++) { 
                DB::table('vehicle_geo_fence')->insert([
                    "vehicle_id" => $vehicle->id,
                    "coordinates" => $request->arrone[$i]
                ]);

                DB::table('vehicle_geo_fence')->insert([
                    "vehicle_id" => $vehicle->id,
                    "coordinates" => $request->arrtwo[$i]
                ]);
            }
            //vehicle routine maintenance
            $rts = new BusMaintenance();
            $rts->vehicle_id = $vehicle->id;
            $rts->garage = $garage->id; 
            $rts->status = 'routine'; 
            $rts->place_name = $garage->location; 
            $rts->last_service = $request->last_service;
            $rts->next_service = $request->last_service + $request->service_interval;
            $rts->save();
        });

        return redirect()->route('vehicles.index')->with('success', 'Vehicle Saved To Fleet Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(Vehicle $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vehicle = Vehicle::find(Crypt::decrypt($id));
        $routes = Route::where('status','=',1)->get();
        $drivers = User::where('user_type', 'LIKE', 'driver')->where('status','=',1)->get();
        foreach ($drivers as $key => $driver) {
            if($driver->id == $vehicle->driver_id) {
                continue;
            } else {
                $check = Vehicle::where('driver_id','=',$driver->id)->where('status','=',1)->first();
                if ($check) {
                    $drivers->forget($key);
                }
            }
        }
        $attendants = User::where('user_type', 'LIKE', 'attendant')->where('status','=',1)->get();

        return view('vehicle.edit')->with([
            'vehicle'=> $vehicle,
            'routes' => $routes,
            'drivers' => $drivers,
            'attendants' => $attendants
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->driver == 'select...') {
            return redirect()->back()->with('unsuccess','Select driver.');
        }
        if ($request->attendant == 'select...') {
            return redirect()->back()->with('unsuccess','Select attendant.');
        }

        if (count($request->routes) <= 0) {
            return redirect()->back()->with('unsuccess','Select attendant.');
        }
        $request->validate([
            'title' => 'required',
            'platenum' => 'required',
            'driver' => 'required',
            'routes' => 'required'
        ]);
        $vehicle = Vehicle::find($id);
        $vehicle->title = $request->title;
        $vehicle->plate_num = $request->platenum;
        $vehicle->driver_id = $request->driver;
        $vehicle->num_of_seats = $request->num_of_seats;
        $vehicle->attendant_id = $request->attendant;
        $vehicle->mileage = $request->mileage;
        $vehicle->last_service = $request->last_service;
        $vehicle->service_interval = $request->service_interval;
        $vehicle->active = 1;
        if ($request->has('image')) {
            if ($vehicle->image) {
                Storage::disk('public_uploads')->delete($vehicle->image);
            }
            $image_path = $request->file('image')->store('vehicle', 'public_uploads');
            $vehicle->image = $image_path;
        }

        $user = User::find($request->attendant);
        $user->vehicle_id = $vehicle->id;
        $user->update();

        $old_routes = DB::table('vehicle_routes')->where('vehicle_id','=', $vehicle->id)->delete();


        for ($i=0; $i < count($request->routes); $i++) { 
            DB::table('vehicle_routes')->insert([
                'vehicle_id' => $vehicle->id,
                'route_id' => $request->routes[$i]
            ]);
        }


        if($vehicle->update()) {
            return redirect()->route('vehicles.index')->with('success', 'Changes To Vehicle Was Added Successfuly');
        }

        return redirect()->back()->with('unsuccess', 'Something Went Wrong Please Try Again Later');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);
        $vehicle->status = 0;

        $veh = DB::table('vehicle_students')->where('vehicle_id','=',$vehicle->id)->get();

        
        foreach ($veh as $key => $vehi) {
            $student = Student::find($vehi->student_id);
            if ($student->status == 1) {
                return redirect()->back()->with('unsuccess','Vehicle has student assigned. Kindly reassign students to another bus');
            }
        }
        
        if ($vehicle->update()) {
            return redirect()->back()->with('success', 'Record deactivated successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error, try again.');
        /*
        $veh = DB::table('vehicle_students')->where('vehicle_id','=',$vehicle->id)->get();

        if (count($veh) > 0) {
            return redirect()->back()->with('unsuccess','Vehicle has student assigned. Kindly reassign students to another nus');
        } 

        $schoolTrip = DB::table('schooltrip_vehicle')->where('vehicle_id','=',$vehicle->id)->get();

        if ($schoolTrip->isNotEmpty()) {
            return redirect()->back()->with('unsuccess','Vehicle is used in a school trip.Kindly reassign another vehicle');
        } 
        DB::table('vehicle_students')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('schooltrip_vehicle')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('vehicle_routes')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('depature_checklists')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('bus_maintenances')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('vehicle_routes')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('pickup_points')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('warranties')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('insurances')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('inspections')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('incidents')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('vehicle_geo_fence')->where('vehicle_id','=',$vehicle->id)->delete();
        DB::table('trips')->where('vehicle_id','=',$vehicle->id)->delete();

        $attendances = Attendance::where('vehicle_id', '=', $vehicle->id)->get();
        if ($attendances) {
            foreach ($attendances as $attendance) {
                $attendance->vehicle_id = null;
                $attendance->update();
            }
        }
        $schoolattendances = SchoolAttendance::where('vehicle_id', '=', $vehicle->id)->get();
        if ($schoolattendances) {
            foreach ($schoolattendances as $sattendance) {
                $sattendance->vehicle_id = null;
                $sattendance->update();
            }
        }
        $students = Student::where('vehicle_id', '=', $vehicle->id)->get();
        if ($students) {
            foreach ($students as $student) {
                $student->vehicle_id = null;
                $student->update();
            }
        }
        $vehicle->delete();
        */

        return redirect()->route('vehicles.index')->with('success', 'Vehicle Removed From Fleet');
    }


    public function activate(Request $request)
    {
        $vehicle = Vehicle::find($request->vehicle_id);
        $vehicle->status = 1;

        if ($vehicle->update()) {
            return redirect()->back()->with('success', 'Record activated successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error, try again.');
    }



    public function outOfFence(Request $request)
    {
        $users = User::where('user_type', 'LIKE', 'office staff')
                        ->orWhere('user_type', 'LIKE', 'admin')
                        ->orWhere('user_type', 'LIKE', 'supervisor')
                        ->orWhere('user_type', 'LIKE', 'head teacher')
                        ->orWhere('user_type', 'LIKE', 'director')
                        ->get();

        $vehicle = Vehicle::find($request->vehicle_id);
        $driver = User::find($vehicle->driver_id);

        Notification::send($users, new VehicleOutOfFence($vehicle->title, $vehicle->plate_num, $driver->name));

        return response('success');
        
    }




    /**
     * edit page for vehicle geo fence
    */
    public function editFence($id)
    {
        $vehicle = Vehicle::find(Crypt::decrypt($id));

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('vehicle.editgeofence')->with([
            'vehicle' => $vehicle,
            'notifications' => $notifications
        ]);
    }
    /**
     * update geo fence 
    */
    public function updateFence(Request $request)
    {
        $vehicle = Vehicle::find($request->vehicle_id);

        DB::table('vehicle_geo_fence')->where('vehicle_id','=', $vehicle->id)->delete();

        $coords = count($request->arrone);

        for ($i=0; $i < $coords; $i++) { 
            DB::table('vehicle_geo_fence')->insert([
                "vehicle_id" => $vehicle->id,
                "coordinates" => $request->arrone[$i]
            ]);

            DB::table('vehicle_geo_fence')->insert([
                "vehicle_id" => $vehicle->id,
                "coordinates" => $request->arrtwo[$i]
            ]);
        }
        /*

        $geofence = Geofence::where('vehicle_id', '=', $vehicle->id)->first();
        $geofence->arrone_first = $request->arrayone_first;
        $geofence->arrone_second = $request->arrayone_second;
        $geofence->arrtwo_first = $request->arraytwo_first;
        $geofence->arrtwo_second = $request->arraytwo_second;
        $geofence->arrthree_first = $request->arraythree_first;
        $geofence->arrthree_second = $request->arraythree_second;
        $geofence->arrfour_first = $request->arrayfour_first;
        $geofence->arrfour_second = $request->arrayfour_second;

        if ($geofence->update()) {
            return redirect()->route('vehicles.index')->with('success', 'Changes To Vehicle GeoFence Was Added Successfuly');
        }

        return redirect()->back()->with('unsuccess', 'Something Went Wrong Please Try Again Later');
        */
        return redirect()->route('vehicles.index')->with('success', 'Changes To Vehicle GeoFence Was Added Successfuly');
    }


    public function standInVehicle(Request $request)
    {
        //add to stand in table
        $stand_in = new StandinBus();
        $stand_in->original_vehicle = $request->original_vehicle;
        $stand_in->stand_in_vehicle = $request->stand_in_vehicle;
        $stand_in->date_from = $request->date_from;
        $stand_in->date_to = $request->date_to;
        $stand_in->status = $request->stand_in_status;
       
        if( $stand_in->save()) {
            return redirect()->back()->with('success','Record added successfully');
        }
        return redirect()->back()->with('unsuccess','System error please try again');

    }
    
}
