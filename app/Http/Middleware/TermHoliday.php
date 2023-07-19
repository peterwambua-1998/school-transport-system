<?php

namespace App\Http\Middleware;

use App\Models\TermHoliday as ModelsTermHoliday;
use Closure;
use Illuminate\Http\Request;

class TermHoliday
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
        $holidays = ModelsTermHoliday::all();
        if ($holidays->isEmpty()) {
            return redirect()->route('term_holiday.index')->with('unsuccess','Add term holiday.');
        }
        return $next($request);
    }
}
