<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class Staff
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
        $office = User::where('user_type','=','office staff')->get();
        if ($office->isEmpty()) {
            return redirect()->route('staff_index')->with('unsuccess','Kindly add office staff.');
        }
        $driver = User::where('user_type','=','driver')->get();
        if ($driver->isEmpty()) {
            return redirect()->route('staff_index')->with('unsuccess','Kindly add driver.');
        }
        $teacher = User::where('user_type','=','teacher')->get();
        if ($teacher->isEmpty()) {
            return redirect()->route('staff_index')->with('unsuccess','Kindly add teacher.');
        }
        $attendant = User::where('user_type','=','attendant')->get();
        if ($attendant->isEmpty()) {
            return redirect()->route('staff_index')->with('unsuccess','Kindly add attendant.');
        }
        return $next($request);
    }
}
