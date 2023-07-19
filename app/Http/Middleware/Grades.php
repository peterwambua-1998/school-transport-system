<?php

namespace App\Http\Middleware;

use App\Models\Stream;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Grades
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
        $group = DB::table('class_groups')->get();
        if ($group->isEmpty()) {
            return redirect()->route('grades_page')->with('unsuccess','Kindly add groups.');
         }
        $grades = DB::table('student_classes')->get();
        if ($grades->isEmpty()) {
           return redirect()->route('grades_page')->with('unsuccess','Kindly add grades.');
        }
        $streams = Stream::all();
        if ($streams->isEmpty()) {
           return redirect()->route('grades_page')->with('unsuccess','Kindly add streams.');
            
        }
        return $next($request);
    }
}
