<?php

namespace App\Http\Controllers;

use App\Models\BusAttendant;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\GeneratedPassword;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class BusAttendantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffs = User::where('user_type', 'LIKE', 'bus attendant')
                        ->get();

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('bus-attendant.index')->with(['staffs' => $staffs, 'notifications' => $notifications]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('bus-attendant.create')->with(['notifications' => $notifications]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $generator = new ComputerPasswordGenerator();

        $generator->setLowercase()->setNumbers(false)->setSymbols(false)->setLength(6);

        $password = $generator->generatePassword();

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'user_type' => 'required',
            'phone_num' => 'required',
            'staff_num' => 'required',
            'email' => 'required|unique:users',
            'vehicle' => 'required'
        ]);

        $staff = new User();
        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->user_type = $request->user_type;
        $staff->phone_num = $request->phone_num;
        $staff->staff_num = $request->staff_num;
        $staff->password = Hash::make($password);
        $staff->password_changed = 0;
        $staff->id_num = $request->id_num;
        if ($request->has('vehicle')) {
            $staff->vehicle_id = $request->vehicle;
        }

        Notification::send($staff, new GeneratedPassword($password));


        if($staff->save()){
            return redirect()->route('bus-attendant.index')->with('success', 'Record added successfully');
        };

        return redirect()->route('bus-attendant.index')->with('unsuccess', 'A problem occured please try again later');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusAttendant  $busAttendant
     * @return \Illuminate\Http\Response
     */
    public function show(BusAttendant $busAttendant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusAttendant  $busAttendant
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $staff = User::find($id);

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $vehicles = Vehicle::all();

        return view('bus-attendant.edit', compact('staff','notifications','vehicles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusAttendant  $busAttendant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'user_type' => 'required',
            'phone_num' => 'required',
            'staff_num' => 'required',
            'email' => 'required|unique:users',
            'vehicle' => 'required'
        ]);

        $staff = new User();
        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->user_type = $request->user_type;
        $staff->phone_num = $request->phone_num;
        $staff->staff_num = $request->staff_num;
        $staff->id_num = $request->id_num;
        if ($request->has('vehicle')) {
            $staff->vehicle_id = $request->vehicle;
        }

        
        if($staff->update()){
            return redirect()->route('bus-attendant.index')->with('success', 'Record updated successfully');
        };

        return redirect()->route('bus-attendant.index')->with('unsuccess', 'A problem occured please try again later');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusAttendant  $busAttendant
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $staff = User::find($id);

        if ($staff->delete()) {
            return redirect()->route('bus-attendant.index')->with('success', 'Record updated successfully');
        }

        return redirect()->route('bus-attendant.index')->with('unsuccess', 'A problem occured please try again later');
    }
}
