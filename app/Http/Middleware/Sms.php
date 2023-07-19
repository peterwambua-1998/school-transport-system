<?php

namespace App\Http\Middleware;

use App\Models\SmsSetting;
use Closure;
use Illuminate\Http\Request;

class Sms
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
        if (! SmsSetting::find(1)) {
            return redirect()->route('sms_settings')->with('unsuccess','Kindly create sms service details.');
        }
        return $next($request);
    }
}
