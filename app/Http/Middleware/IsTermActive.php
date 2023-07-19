<?php

namespace App\Http\Middleware;

use App\Models\SchoolTermDate;
use Closure;
use Illuminate\Http\Request;

class IsTermActive
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
        $term = SchoolTermDate::where('status','=', 1)->first();

        if (! $term) {
            return redirect()->route('term.index')->with('unsuccess','Please add active school term');
        }
        return $next($request);
    }
}
