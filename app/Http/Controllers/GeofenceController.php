<?php

namespace App\Http\Controllers;

use App\Models\Geofence;
use App\Models\Route;
use App\Models\RoutePolyline;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class GeofenceController extends Controller
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

        $polylines = RoutePolyline::where('id', '=', Crypt::decrypt($id))->first();

        //$geofence = Geofence::find($id);

        $route = Route::where('id', '=', $polylines->route_id)->first();

        
        

        $colors = ['red', 'blue', 'green', 'yellow', 'purple', 'orange', 'teal'];

        return view('routes.showgeofence')->with([
            'notifications' => $notifications,
            
            'route' => $route,
            'polylines' => $polylines,
            'colors' => $colors
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('routes.geofence')->with('notifications', $notifications);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $route = Route::find($request->route_id);
        $geofence = new Geofence();
        $geofence->route_id = $request->route_id;
        $geofence->arrone_first = $request->arrayone_first;
        $geofence->arrone_second = $request->arrayone_second;
        $geofence->arrtwo_first = $request->arraytwo_first;
        $geofence->arrtwo_second = $request->arraytwo_second;
        $geofence->arrthree_first = $request->arraythree_first;
        $geofence->arrthree_second = $request->arraythree_second;
        $geofence->arrfour_first = $request->arrayfour_first;
        $geofence->arrfour_second = $request->arrayfour_second;
        $geofence->save();

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('routes.polyline')->with(['geofence' => $geofence, 'route' => $route, 'notifications' => $notifications]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Geofence  $geofence
     * @return \Illuminate\Http\Response
     */
    public function show(Geofence $geofence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Geofence  $geofence
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $geofence = Geofence::find($id);

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $route_id = $geofence->route_id;

        return view('routes.editgeofence')->with([
            'geofence' => $geofence,
            'notifications' => $notifications,
            'route_id' => $route_id
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Geofence  $geofence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $geofence = Geofence::find($id);
        
        $geofence->arrone_first = $request->arrayone_first;
        $geofence->arrone_second = $request->arrayone_second;
        $geofence->arrtwo_first = $request->arraytwo_first;
        $geofence->arrtwo_second = $request->arraytwo_second;
        $geofence->arrthree_first = $request->arraythree_first;
        $geofence->arrthree_second = $request->arraythree_second;
        $geofence->arrfour_first = $request->arrayfour_first;
        $geofence->arrfour_second = $request->arrayfour_second;

        $route = Route::where('id', '=',$geofence->route_id)->first();

        $polylines = RoutePolyline::where('route_id', '=', $route)->get();

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $colors = ['red', 'blue', 'green', 'yellow', 'purple', 'orange', 'teal'];

        if ($geofence->update()) {
            return redirect()->route('geofence_show', $route->geofence->id);
                
        }

        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Geofence  $geofence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Geofence $geofence)
    {
        //
    }

    public function add($id)
    {
        $route = Route::find($id);

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('routes.geofence')->with([
            'success' => 'Route Added Successfuly, add geo fence to route',
            'route_id' => $route->id,
            'notifications' => $notifications
        ]);
    }
}
