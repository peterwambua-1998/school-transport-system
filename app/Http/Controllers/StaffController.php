<?php

namespace App\Http\Controllers;

use App\Events\NewMessageNotification;
use App\Events\NewNotification;
use App\Models\FlagOff;
use App\Models\Role;
use App\Notifications\GeneratedPassword;
use App\Notifications\ToParent;
use App\Models\SchoolTrip;
use App\Models\Settings;
use App\Models\Staff;
use App\Models\StandinDriver;
use App\Models\Stream;
use App\Models\User;
use App\Models\Vehicle;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use League\OAuth2\Server\Grant\PasswordGrant;
use Pusher\PushNotifications\PushNotifications;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffs = User::whereNot('user_type','=','parent')->whereNot('user_type','=','parent two')->whereNot('user_type','=','other')->get();

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('staff.index')->with(['staffs' => $staffs, 'notifications' => $notifications]);
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

        $vehicles = Vehicle::where('status','=',1)->get();

        $roles = Role::all();

        return view('staff.create')->with(['notifications' => $notifications, 'vehicles' => $vehicles, 'roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user_type == 'select...') {
            return redirect()->back()->with('unsuccess','Kindly select role.');
        }
        
        $generator = new ComputerPasswordGenerator();

        $generator->setLowercase()->setNumbers(false)->setSymbols(false)->setLength(6);

        $password = $generator->generatePassword();

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'user_type' => 'required',
            'phone_num' => 'required',
            'staff_num' => 'required',
            'email' => 'required|unique:users'
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
        $staff->gender = $request->gender;
        if ($request->user_type == 'teacher') {
            $staff->grade = $request->grade;
        }
        if($request->has('image')) {
            $path = $request->file('image')->store('staff','public_uploads'); 
            $staff->image = $path;
        }

        try {
            Notification::send($staff, new GeneratedPassword($password));
        } catch (\Throwable $th) {
            return redirect()->back()->with('unsuccess','System error. Kindly Check your internet connection.');
        }


        if($staff->save()){
           
            return redirect()->route('staff_index')->with('success', 'Record added successfully');
        };

        return redirect()->route('staff_index')->with('unsuccess', 'System error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $staff = User::find(Crypt::decrypt($id));

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $roles = Role::all();

        $vehicles = Vehicle::where('status','=',1)->get();

        return view('staff.edit')->with(['staff' => $staff, 'notifications' => $notifications,'roles' => $roles, 'vehicles' => $vehicles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone_num' => 'required',
            'staff_num' => 'required'
        ]);

        $staff = User::find($id);
        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->user_type = $request->user_type;
        $staff->phone_num = $request->phone_num;
        $staff->staff_num = $request->staff_num;
        $staff->id_num = $request->id_number;
        $staff->gender = $request->gender;

        if($request->image) {
            if ($staff->image) {
                Storage::disk('public_uploads')->delete($staff->image);
            }
            $path = $request->file('image')->store('staff','public_uploads'); 

            $staff->image = $path;
        }


        if($staff->update()){
            return redirect()->route('staff_index')->with('success', 'Record updated successfully');
        };

        return redirect()->route('staff_index')->with('unsuccess', 'System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $staff = User::find($id);
        $staff->status = 0;
        if ($staff->update()) {
            return redirect()->route('staff_index')->with('success', 'Record deactivated successfully');
        }
        /*
        DB::table('incidents')->where('user_assulter','=',$staff->id)->delete();

        if ($staff->user_type == 'driver') {
            $veh = Vehicle::where('driver_id','=', $staff->id)->first();
            if ($veh) {
                $veh->driver_id = null;
                $veh->update();
            }
        }

        if ($staff->user_type == 'attendant') {
            $veh = Vehicle::where('attendant_id','=', $staff->id)->first();
            if ($veh) {
                $veh->driver_id = null;
                $veh->update();
            }
            
        }

        if ($staff->user_type == 'teacher') {
            $stream = Stream::where('class_teacher','=', $staff->id)->first();
            if ($stream) {
                $stream->class_teacher = null;
                $stream->update();
            }
            

            DB::table('schooltrip_teacher')->where('teacher_id','=', $staff->id)->delete();
        
        }

        foreach ($staff->notifications  as $notification) {
            $notification->delete();
        }

       
        */

        return redirect()->route('staff_index')->with('unsuccess', 'System error please try again');
    }

    public function activate(Request $request)
    {
        $staff = User::find($request->staff_id);
        $staff->status = 1;
        if ($staff->update()) {
            return redirect()->route('staff_index')->with('success', 'Record deactivated successfully');
        }
    }

    public function notificationView()
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $parents = User::where('user_type', 'LIKE', 'parent')->get();

        return view('parents.notification')->with(['notifications'=> $notifications, 'parents' => $parents]);
    }


    public function sendNotification(Request $request)
    {
        $parent = User::find($request->parent_id);

        $msgHeader = $request->msg_header;
        $msgBody = $request->msg_body;

        $parent->notify(new ToParent($msgHeader, $msgBody));

        $settings = Settings::find(1);

        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);

        $publishResponse = $pushNotifications->publishToInterests(
            ['transport-'.$parent->id],
            [
                "fcm" => [
                    "notification" => [
                        "title" => $msgHeader,
                        "body" => $msgBody,
                        "icon" => asset('store/'.$settings->company_logo),
                    ],
                ],
                "web" => [
                    "time_to_live" => 3600,
                    "notification" => [
                        "title" => $msgHeader,
                        "body" => $msgBody,
                        "icon" => asset('store/'.$settings->company_logo),
                        "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                        "hide_notification_if_site_has_focus" => true
                    ]
                ]
            ]
        );


        return redirect()->back()->with('success', 'notification sent');
    }


    public function markAsRead($id)
    {
        $notification = DatabaseNotification::find($id);

        $notification->markAsRead();

        return redirect()->back();
    }

    public function markAll()
    {
        $user = Auth::user();

        foreach ($user->notifications as $notif) {
            $notif->markAsRead();
        };

        return redirect()->back();
    }


    public function getNotification()
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->notifications;

        $numOfNotifications = count($notifications);

        $users = User::all();
        


        return view('staff.seenotification')->with([
            'notifications' => $notifications,
            'numOfNotifications' => $numOfNotifications,
            'users' => $users
        ]);
    }

    public function approveRoute(Request $request)
    {
        
        $schooltrips = SchoolTrip::find($request->schooltrip_id);
        $schooltrips->approved = 1;
        if($schooltrips->update()) {
            return response('Route is approved');
        }

        return response('System error please try gain');


    }

    public function adbsentToday()
    {
        $date = date('Y-m-d');

        $flagoffs = FlagOff::where('date', '=', $date)->get();


        return view('staff.flagoff')->with([
            'flagoffs' => $flagoffs,
            
        ]);
    }

    public function validateEmail(Request $request) 
    {
        $request->validate([
            'email' => 'email|required|unique:users'
        ]);

        return response('email is valid');
    }

    public function validateStaffNumber(Request $request) 
    {
        $request->validate([
            'staff_num' => 'unique:users'
        ]);
        return response('staff number is valid');

    }

    public function validateIDNumber(Request $request) 
    {
        $request->validate([
            'id_num' => 'unique:users'
        ]);
        return response('id number is valid');

    }

    public function validatePhone(Request $request)
    {
        $request->validate([
            'phone_num' => 'unique:users'
        ]);
        return response('phone number is valid');
    }

    public function validateparentEmail(Request $request) 
    {
        $request->validate([
            'email' => 'email|required'
        ]);

        return response('email is valid');
    }

    public function standInDriver(Request $request)
    {
        $vehicle = Vehicle::where('driver_id','=',$request->original_driver)->where('status','=', 1)->first();
        if (! $vehicle) {
            return redirect()->back()->with('unsuccess','Vehicle out of service');
        }
        $stand_in = new StandinDriver();
        $stand_in->stand_in_vehicle = $vehicle->id;
        $stand_in->stand_in_driver = $request->stand_in_driver;
        $stand_in->date_from = $request->date_from;
        $stand_in->date_to = $request->date_to;
        $stand_in->status = $request->stand_in_status;
        if ($stand_in->save()) {
            return redirect()->back()->with('success','Record added successfully.');
        }

        return redirect()->back()->with('unsuccess','System error please try again');

    }

    public function standInAttendant(Request $request)
    {
        $vehicle = Vehicle::where('attendant_id','=',$request->original_attendant)->where('status','=', 1)->first();
        if (! $vehicle) {
            return redirect()->back()->with('unsuccess','Vehicle out of service');
        }
        $stand_in = new StandinDriver();
        $stand_in->stand_in_vehicle = $vehicle->id;
        $stand_in->stand_in_attendant = $request->stand_in_attendant;
        $stand_in->date_from = $request->date_from;
        $stand_in->date_to = $request->date_to;
        $stand_in->status = $request->stand_in_status;
        if ($stand_in->save()) {
            return redirect()->back()->with('success','Record added successfully.');
        }

        return redirect()->back()->with('unsuccess','System error please try again');
    }
}
