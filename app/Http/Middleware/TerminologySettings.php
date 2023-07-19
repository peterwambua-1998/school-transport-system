<?php

namespace App\Http\Middleware;

use App\Models\Terminology;
use Closure;
use Illuminate\Http\Request;

class TerminologySettings
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
        $terminology = Terminology::find(1);
        if (! $terminology) {
            return redirect()->route('terminology.create')->with('unsuccess','Please register terminologies to be used within the system.');
        }
        return $next($request);
    }
}
