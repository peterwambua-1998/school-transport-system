<?php

namespace App\Http\Middleware;

use App\Models\Route;
use App\Models\Trip;
use App\Models\Vehicle as ModelsVehicle;
use App\Models\Zone;
use Closure;
use Illuminate\Http\Request;

class Vehicle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //zones
        $zones = Zone::all();
        if ($zones->isEmpty()) {
            return redirect()->route('zones.index')->with('unsuccess','Kindly add a zone.');
        }
        //routes
        $routes = Route::all();
        if ($routes->isEmpty()) {
            return redirect()->route('routes.index')->with('unsuccess','Kindly add a route.');
        }
        //buses
        $vehicles = ModelsVehicle::all();
        if ($vehicles->isEmpty()) {
            return redirect()->route('vehicles.index')->with('unsuccess','Kindly add a vehicle.');
        }
        //trips
        $trips = Trip::all();
        if ($trips->isEmpty()) {
            return redirect()->route('vehicles.index')->with('unsuccess','Kindly add a trip to a vehicle.');
        }
        
        return $next($request);
    }
}
