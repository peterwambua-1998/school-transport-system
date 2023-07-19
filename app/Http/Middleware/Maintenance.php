<?php

namespace App\Http\Middleware;

use App\Models\Warranty;
use Closure;
use Illuminate\Http\Request;

class Maintenance
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
        $warranty = Warranty::find(1);
        if (! $warranty) {
            return redirect()->route('warranty.index')->with('unsuccess','Kindly add warranty to vehicles.');
        }
        return $next($request);
    }
}
