<?php

namespace App\Http\Middleware;

use App\Models\SchoolFees;
use App\Models\SchoolTermDate;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TermSchoolFees
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
        $grades = DB::table('student_classes')->get();
        foreach ($grades as $key => $grade) {
            $fee = SchoolFees::where('term','=', $term->id)->where('grade','=', $grade->id)->first();
            if (! $fee) {
                return redirect()->route('school-fees.index')->with('unsuccess','Kindly add school fees for all grades');
            }
        }
        
        return $next($request);
    }
}
