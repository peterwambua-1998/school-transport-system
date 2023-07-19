<?php

namespace App\Http\Controllers;

use App\Models\Geofence;
use App\Models\Route;
use App\Models\RoutePolyline;
use App\Models\Settings;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{

    public function __construct()
{
    // Middleware only applied to these methods
    $this->middleware('isAdmin', [
        'only' => [
            'create', 'store', 'edit', 'update', 'destroy' // Could add bunch of more methods too
        ]
    ]);
}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $routes = Route::all();
        $user = Auth::user();

        return view('routes.index')->with(['routes'=> $routes]);
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
        $user = Auth::user();
        $notifications = User::find($user->id)->unreadNotifications;
        $zones = Zone::where('status','=',1)->get();
        return view('routes.create')->with([
            'notifications' => $notifications,
            'zones' => $zones
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
        if(!$request->origin && !$request->destination) {
            return redirect()->back()->with('unsuccess','Please provide a route path');
        }

        $request->validate([
           'title' => 'required',
           'description' => 'required',
        ]);


        DB::transaction(function() use ($request, ) {
            $route = new Route();
            $route->title = $request->title;
            $route->description = $request->description;
            $route->distance = $request->distance_meters;
            $route->price = 0;
            $route->save();

            $routePolyline = new RoutePolyline();
            $routePolyline->route_id = $route->id;
            $routePolyline->origin = $request->origin;
            $routePolyline->destination = $request->destination;
            $routePolyline->way_point_1 = $request->waypoint_1;
            $routePolyline->way_point_2 = $request->waypoint_2;
            $routePolyline->way_point_3 = $request->waypoint_3;
            $routePolyline->way_point_4 = $request->waypoint_4;
            $routePolyline->way_point_5 = $request->waypoint_5;
            $routePolyline->way_point_6 = $request->waypoint_6;
            $routePolyline->way_point_7 = $request->waypoint_7;
            $routePolyline->way_point_8 = $request->waypoint_8;
            $routePolyline->save();

            for ($x=0; $x < count($request->zone); $x++) { 
                DB::table('route_zones')->insert([
                    'route_id' => $route->id,
                    'zone_id' => $request->zone[$x]
                ]);
            }

        });
 
        return redirect()->route('routes.index')->with('success','Route Added Successfuly');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function show(Route $route)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $route = Route::find(Crypt::decrypt($id));

        $user = Auth::user();

        $zones = Zone::all();

        foreach ($zones as $key => $zone) {
            if ($zone->status == 0) {
                $zones->forget($key);
            }
        }

        $notifications = User::find($user->id)->unreadNotifications;

        return view('routes.edit')->with(['route'=> $route, 'notifications' => $notifications, 'zones' => $zones]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
           'title' => 'required',
           'description' => 'required',
           'zone' => 'required'
        ]);

        DB::transaction(function() use ($request, $id) {
            $route = Route::find($id);
            $route->title = $request->title;
            $route->description = $request->description;
            $route->update();
            /*
            $routePolyline = RoutePolyline::where('route_id','=', $route->id)->first();
            $routePolyline->origin = $request->origin;
            $routePolyline->destination = $request->destination;
            $routePolyline->way_point_1 = $request->waypoint_1;
            $routePolyline->way_point_2 = $request->waypoint_2;
            $routePolyline->way_point_3 = $request->waypoint_3;
            $routePolyline->way_point_4 = $request->waypoint_4;
            $routePolyline->way_point_5 = $request->waypoint_5;
            $routePolyline->way_point_6 = $request->waypoint_6;
            $routePolyline->way_point_7 = $request->waypoint_7;
            $routePolyline->way_point_8 = $request->waypoint_8;
            $routePolyline->update();
            */

           DB::table('route_zones')->where('route_id','=', $route->id)->delete();
           
            
           for ($x=0; $x < count($request->zone); $x++) { 
                DB::table('route_zones')->insert([
                    'route_id' => $route->id,
                    'zone_id' => $request->zone[$x]
                ]);
            }
        });

        return redirect()->route('routes.index')->with('success', 'Route Updated Successfuly');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $route = Route::find($id);
        $rts = DB::table('vehicle_routes')->where('route_id','=',$route->id)->get();

        foreach ($rts as $rt) {
            $vehicle = Vehicle::find($rt->vehicle_id);
            if ($vehicle->status = 1) {
                return redirect()->route('routes.index')->with(['success' => 'Vehicles are using this route']);
            }   
        }

        $route->status = 0;
        if ($route->update()) {
            return redirect()->route('routes.index')->with(['success' => 'Record deactivated successfully']);
        }
        /*
        $polyLines = RoutePolyline::where('route_id', '=', $route->id)->get();
        $trips = Trip::where('route_id', '=', $route->id)->get();
        $veh_routes = DB::table('vehicle_routes')->where('route_id','=',$route->id)->get();

        if ($veh_routes->isNotEmpty()) {
            return redirect()->back()->with('unsuccess','Reassign bus that use routes');
        } else {
            DB::table('vehicle_routes')->where('route_id','=',$route->id)->get();
        }

        if ($trips->isNotEmpty()) {
            return redirect()->back()->with('unsuccess','Reassign bus trips that use this route');
        }
        
        foreach ($polyLines as $polyLine) {
            $polyLine->delete();
        }
        */
        return redirect()->route('routes.index')->with(['unsuccess' => 'System error please try again']);

    }


    /**
     * activate
     */
    public function activate(Request $request)
    {
        $route = Route::find($request->route_id);
        $route->status = 1;
        if ($route->update()) {
            return redirect()->route('routes.index')->with(['success' => 'Record activated successfully']);
        }
    }

    public function getZone($id) 
    {
        $zone = Zone::find($id);

        $zone_path = DB::table('zones_coordinates')->where('zone_id','=', $zone->id)->get();

        return response([$zone, $zone_path]);
    }
}
