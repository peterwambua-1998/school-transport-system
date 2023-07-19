<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class redirect_home_admin
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
        $parents = User::where('user_type','=','parent')->get();
        if (Auth::user()->user_type == 'admin') { 
            return response()->view('home', compact('parents'));
        }
        
        if (Auth::user()->user_type == "office staff") {
            return response()->view('home', compact('parents'));
        }

        if (Auth::user()->user_type == 'parent') {
            return response()->view('phome');
        }

        if (Auth::user()->user_type == 'teacher') {
            return redirect('/schoolattendance/create');
        }
        
        return abort(403, 'Access denied');
    
    }
}
