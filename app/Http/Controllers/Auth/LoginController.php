<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function authenticated()
    {
        if(Auth::user()->user_type == 'parent'){  
            $parent = Auth::user();

            if ($parent->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('phome');
        }

        if(Auth::user()->user_type == 'office staff'){
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('home');
        }

        if(Auth::user()->user_type == 'admin'){
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('home');
        }

        if(Auth::user()->user_type == 'supervisor'){
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('home');
        }

        if(Auth::user()->user_type == 'manager'){
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('home');
        }

        if(Auth::user()->user_type == 'office_executive'){
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('home');
        }

        if(Auth::user()->user_type == 'driver'){
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('driverlogin_home');
            //return redirect()->route('driver_mystudents');
        }

        if(Auth::user()->user_type == 'teacher'){
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }
            
            return redirect()->route('schoolattcreate');
        }

       

    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
