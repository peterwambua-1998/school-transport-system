<?php

namespace App\Http\Controllers;

use App\Models\Geofence;
use App\Models\Route;
use App\Models\RoutePolyline;
use App\Models\Student;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RoutePolylineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $route = Route::find(Crypt::decrypt($id));

        $students = Student::all();

        return view('routes.polyline')->with([
            'notifications' => $notifications,
            'route' => $route,
            'students' => $students
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $route = Route::find($id);

        $geoFence = Geofence::where('route_id', '=', $route->id)->first();

        if (! $geoFence) {
            return redirect()->back()->with('unsuccess', 'Please add geo fence then add trips');
        }

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('routes.polyline')->with([
            'notifications' => $notifications,
            'route' => $route,
            'geofence' => $geoFence
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $route = Route::find($id);

        

        
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
        

        

        if ($routePolyline->save()) {
            return redirect()->route('routes.index')->with('success'. 'Route path saved');
        }

        return redirect()->route('routes.index')->with('System error please try again');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RoutePolyline  $routePolyline
     * @return \Illuminate\Http\Response
     */
    public function show(RoutePolyline $routePolyline)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RoutePolyline  $routePolyline
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $polyline = RoutePolyline::find(Crypt::decrypt($id));
        $route = Route::find($polyline->route_id);
        $route_zones = DB::table('route_zones')->where('route_id', '=', $route->id)->get();

        $zones = new Collection();

        foreach ($route_zones as $key => $route_zone) {
            $zone = Zone::find($route_zone->zone_id);
            $zones->push($zone);
        }

        return view('routes.editpolyline')->with([
            'polyline' => $polyline,
            'route' => $route,
            'zones' => $zones
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RoutePolyline  $routePolyline
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!$request->origin && !$request->destination) {
            return redirect()->back()->with('unsuccess','Please provide a route path');
        }

        $routePolyline = RoutePolyline::find(Crypt::decrypt($id));
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

        $route = Route::where('id','=', $routePolyline->route_id)->first();
        $route->distance = $request->distance_meters;
        $route->update();

        if ($routePolyline->update()) {
            return redirect()->route('geofence_show', Crypt::encrypt($routePolyline->route_id))->with('success', 'Update was successful');
        }

        return redirect()->back()->with('unsuccess', 'System error please try again');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RoutePolyline  $routePolyline
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $polyline = RoutePolyline::find($request->id);

        $polyline->delete();

        return response('delete successfully');
    }
}
