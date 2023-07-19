<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected function authenticated()
    {
        if(Auth::user()->user_type == 'parent'){

            
            
            $parent = Auth::user();

            
            $pNofitications = User::find($parent->id)->unreadNotifications;
    
            
            
            $numOfNotifications = count($pNofitications); 

            if ($parent->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('phome')->with([
                'pNofitications' => $pNofitications,
                'numOfNotifications' => $numOfNotifications
            ]);
        }

        if(Auth::user()->user_type == 'office staff'){
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }

            $pNofitications = Auth::user()->unreadNotifications;
    
            
            $numOfNotifications = count($pNofitications); 

            return redirect()->route('home');
        }

        if(Auth::user()->user_type == 'admin'){

            $pNofitications = Auth::user()->unreadNotifications;
    
            
            $numOfNotifications = count($pNofitications); 
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('home');
        }

        if(Auth::user()->user_type == 'supervisor'){
            $pNofitications = Auth::user()->unreadNotifications;
    
            
            $numOfNotifications = count($pNofitications); 
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('home');
        }

        if(Auth::user()->user_type == 'manager'){

            $pNofitications = Auth::user()->unreadNotifications;
    
            
            $numOfNotifications = count($pNofitications); 
            if (Auth::user()->password_changed == false) {
                return redirect()->route('changepass');
            }

            return redirect()->route('home');
        }

        if(Auth::user()->user_type == 'office_executive'){

            $pNofitications = Auth::user()->unreadNotifications;
    
            
            $numOfNotifications = count($pNofitications); 
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
            $pNofitications = Auth::user()->unreadNotifications;
    
            
            $numOfNotifications = count($pNofitications); 
            
            return redirect()->route('schoolattcreate');
        }

       

    }
}
