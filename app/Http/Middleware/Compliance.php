<?php

namespace App\Http\Middleware;

use App\Models\Inspection;
use App\Models\Insurance;
use App\Models\Vehicle;
use Closure;
use Illuminate\Http\Request;

class Compliance
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
        //insurance
        $vehicles = Vehicle::where('status','=', 1)->get();
        foreach ($vehicles as $key => $vehicle) {
            $insurance = Insurance::where('vehicle_id','=',$vehicle->id)->where('status','=',1)->first();
            if (!$insurance) {
                return redirect()->route('insurance.index')->with('unsuccess','Kindly add insurance to all vehicles');
            }
        }

        //inspection
        foreach ($vehicles as $key => $vehicle) {
            $inspection = Inspection::where('vehicle_id','=', $vehicle->id)->orderBy('created_at','desc')->first();
            if (!$inspection) {
                return redirect()->route('inspection.index')->with('unsuccess','Kindly add inspection to all vehicles');
            }
        }

        return $next($request);
    }
}
