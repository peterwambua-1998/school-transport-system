<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetPasswordCode;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class ResetPasswordController extends Controller
{
    /**
     * @param Request
     * @return Illuminate\Http\Response
     */
    public function sendMailWithToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email','=', $request->email)->first();

        if (! $user) {
            return abort(401);
        }


        $token = $user->generateToken();

        Notification::send($user, new ResetPasswordCode($token));

        return response('six digit code has been sent to your mail');
    }

    public function validateRedirectTwoFactor(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|integer'
        ]);


        $user = User::where('email','=',$request->email)->first();

        $tokenDate = Carbon::createFromFormat('Y-m-d H:i:s', $user->expire_at);


        if (! $user) {
            return response('Email provided does not exist');
        }

        if ($tokenDate->lt(now())) {

            $user->resetToken();

            return response('Two factor code has expired try again'); 
        }

        if ($user->rand_number == $request->token) {

            $user->resetToken();

            return response('success');
        } 
        return response('Token entered does not match');
        
    }

    public function resend(Request $request)
    {
        $user = User::where('email','=',$request->email)->first();

        if (! $user) {
            return response('Email provided does not exist');
        }

        $token = $user->generateToken();
        
        $user->notify(new ResetPasswordCode($token));

        return response('Token has been resent');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email','=',$request->email)->first();

        if (! $user) {
            return response('Email provided does not exist');
        }

        $user->password = Hash::make($request->password);
        if($user->update()) {
            return response('password changed');
        }

        return response('System error please try again');
    }
}
