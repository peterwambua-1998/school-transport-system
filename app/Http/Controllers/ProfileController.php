<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function showPage($id)
    {
        $user = User::find(Crypt::decrypt($id));
        return view('profile.index')->with([
            'user' => $user,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userProfile = User::find(Crypt::decrypt($id));
        $user = Auth::user();
        return view('profile.show')->with([
            'userProfile' => $userProfile,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->user_type  == 'teacher') {
            # code...
            $userProfile = User::find(Crypt::decrypt($id));
            $userProfile->email = $request->email;
            $userProfile->phone_num = $request->phone_num;
            /*
             $userProfile->id_num = $request->id_num;
            $userProfile->grade = $request->grade;
             if ($request->get('new-password') != null) {

                if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
                    // The passwords matches
                    return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
                }

                if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
                    //Current password and new password are same
                    return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
                }

                $userProfile->password = Hash::make($request->get('new-password'));
            }
            */
            if ($userProfile->update()) {
                return redirect()->route('profile_page',  Crypt::encrypt($userProfile->id))->with([
                    'userProfile' => $userProfile,
                    'success' => 'profile updated'
                ]);
            } else {
                return redirect()->back()->with('unsuccess','Sytem error please try again');
            }
        } else {
            $userProfile = User::find(Crypt::decrypt($id));
            $userProfile->email = $request->email;
            $userProfile->phone_num = $request->phone_num;
            /*
            $userProfile->id_num = $request->id_num;
            $userProfile->name = $request->name;
             if ($request->get('new-password') != null) {
                if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
                    // The passwords matches
                    return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
                }

                if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
                    //Current password and new password are same
                    return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
                }
                $userProfile->password = Hash::make($request->get('new-password'));
             }

            */
            if ($userProfile->update()) {
                return redirect()->route('profile_page', Crypt::encrypt($userProfile->id))->with([
                    'userProfile' => $userProfile,
                    'success' => 'profile updated'
                ]);
            } else {
                return redirect()->back()->with('unsuccess','Sytem error please try again');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
