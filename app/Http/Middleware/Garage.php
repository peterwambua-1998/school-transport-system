<?php

namespace App\Http\Middleware;

use App\Models\Garage as ModelsGarage;
use Closure;
use Illuminate\Http\Request;

class Garage
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
        $garage = ModelsGarage::where('active','=', 1)->first();
        if (! $garage) {
            return redirect()->route('garage.index')->with('unsuccess','Kindly add active garage');
        }
        return $next($request);
    }
}
