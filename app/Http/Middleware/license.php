<?php

namespace App\Http\Middleware;

use App\Models\DriverLicence;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class license
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
        $drivers = User::where('user_type','=', 'driver')->get();
        foreach ($drivers as $key => $driver) {
            $license = DriverLicence::where('driver_id','=', $driver->id)->first();
            if (! $license) {
                return redirect()->route('license.index')->with('unsuccess','Add driving license to all drivers.');
            }
        }

        
        return $next($request);
    }
}
