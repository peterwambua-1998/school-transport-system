<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\FlagOff;
use App\Notifications\StudentAbsent;
use App\Notifications\StudentAttend;
use App\Models\SchoolAttendance;
use App\Models\Settings;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Terminology;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Pusher\PushNotifications\PushNotifications;

class SchoolAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('school_attendance.index')->with(['notifications' => $notifications]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $date = date('Y-m-d');

        $teacher = Auth::user();

        $stream = Stream::where('class_teacher','=',$teacher->id)->first();

        if ($teacher->user_type != 'teacher') {
            return redirect()->route('attendances.index')->with('unsuccess', 'Current user cannot mark attendance');
        }

        $attendanceAm = Attendance::where('date', '=', $date)
                                    ->where('route_time', '=', 'am')
                                    ->where('present', '=', 'present')
                                    ->where('grade', 'LIKE', $stream->student_classes_id)
                                    ->where('stream','=', $stream->id)
                                    ->get();

        return view('school_attendance.create')->with(['attendanceAm' => $attendanceAm]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $len = count($request->student_id);
        $tr = Terminology::find(1);
        $settings = Settings::find(1);

        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);
        
        for ($i=0; $i < $len; $i++) {
                $student = Student::find($request->student_id[$i]); 
          
                $attendance = new SchoolAttendance();
                $attendance->vehicle_id = $request->vehicle_id[$i];
                $attendance->route_time = 'am';
                $attendance->student_id = $request->student_id[$i];
                $attendance->present = $request->present[$i];
                $attendance->grade = $student->grade;
                $attendance->save();

                $student = Student::find($request->student_id[$i]);

                $user = User::find($student->parent_id);
                //{$this->student->first_name} {$this->student->last_name} is present in school.
                if ($request->present[$i] == 1) {
                    Notification::send($user, new StudentAttend($student));
                    $publishResponse = $pushNotifications->publishToInterests(
                        ['transport-'.$user->id],
                        [
                            "fcm" => [
                                "notification" => [
                                    "title" => "School Attendance",
                                    "body" => "$student->first_name $student->last_name is present in school,",
                                    "icon" => asset('store/'.$settings->company_logo),
                                ],
                            ],
                            "web" => [
                                "time_to_live" => 3600,
                                "notification" => [
                                    "title" => "School Attendance",
                                    "body" => "$student->first_name $student->last_name is present in school.",
                                    "icon" => asset('store/'.$settings->company_logo),
                                    "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                                    "hide_notification_if_site_has_focus" => true
                                ]
                            ]
                        ]
                    );

                } else {
                    Notification::send($user, new StudentAbsent($student));

                    $publishResponse = $pushNotifications->publishToInterests(
                        ['transport-'.$user->id],
                        [
                            "fcm" => [
                                "notification" => [
                                    "title" => "School Attendance",
                                    "body" => "$student->first_name $student->last_name is absent from school,",
                                    "icon" => asset('store/'.$settings->company_logo),
                                ],
                            ],
                            "web" => [
                                "time_to_live" => 3600,
                                "notification" => [
                                    "title" => "School Attendance",
                                    "body" => "$student->first_name $student->last_name is absent from school.",
                                    "icon" => asset('store/'.$settings->company_logo),
                                    "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                                    "hide_notification_if_site_has_focus" => true
                                ]
                            ]
                        ]
                    );
                }
                
            
        }

        return redirect()->route('school-attendance.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SchoolAttendance  $schoolAttendance
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolAttendance $schoolAttendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SchoolAttendance  $schoolAttendance
     * @return \Illuminate\Http\Response
     */
    public function edit(SchoolAttendance $schoolAttendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SchoolAttendance  $schoolAttendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SchoolAttendance $schoolAttendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SchoolAttendance  $schoolAttendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolAttendance $schoolAttendance)
    {
        //
    }

    public function schoolAttendanceData()
    {

        $teacher = Auth::user();

        $stream = Stream::where('class_teacher','=',$teacher->id)->first();

        if ($teacher->user_type != 'teacher') {
            return redirect()->route('attendances.index')->with('unsuccess', 'Current user cannot mark attendance');
        }

        $date = date('Y-m-d');

        $attendanceAm = SchoolAttendance::where('created_at','LIKE','%'.$date.'%')->where('route_time', '=', 'am')->where('grade', '=', $stream->student_classes_id)->get();

        
        $table = '';

        foreach ($attendanceAm as $item) {
            $student = Student::where('id', '=', $item->student_id)->first();
            if ($student->stream == $stream->id) {
                $vehicle = Vehicle::where('id', '=', $item->vehicle_id)->first();
                $driver = User::where('id', '=', $vehicle->driver_id)->first();

                $ifpresent = '';

                if ($item->present == 0) {
                    $ifpresent = 'absent';
                } else if ($item->present == 1){
                    $ifpresent = 'present';
                }
                
                $table .= "<tr>
                    <td>$student->first_name</td>
                    <td>$student->last_name</td>
                    <td>$ifpresent</td>
                    <td>$student->grade</td>
                    <td>$vehicle->title $vehicle->plate_num</td>
                    <td>$driver->name</td>
                    <td>$item->created_at</td>
                </tr>";
            }
                
        }

        return response(['table' => $table]);

    }


    public function schoolAttendanceQuery(Request $request)
    {
        $date = $request->from;

        $teacher = Auth::user();

        $stream = Stream::where('class_teacher','=',$teacher->id)->first();

        if ($teacher->user_type != 'teacher') {
            return abort(401);
        }

        $attendanceAm = SchoolAttendance::where('created_at','LIKE','%'.$date.'%')->where('route_time', '=', 'am')->where('grade', '=', $stream->student_classes_id)->get();

        //$attendancePm = SchoolAttendance::where('created_at', 'LIKE', '%'. $date .'%')->where('route_time', 'LIKE', 'pm')->get();  
        
        $table = 'no data';

        foreach ($attendanceAm as $item) {
            $student = Student::where('id', '=', $item->student_id)->first();
            if ($student->stream == $stream->id) {   
                $vehicle = Vehicle::where('id', '=', $item->vehicle_id)->first();
                $driver = User::where('id', '=', $vehicle->driver_id)->first();

                $ifpresent = '';

                if ($item->present == 0) {
                    $ifpresent = 'absent';
                } else if ($item->present == 1){
                    $ifpresent = 'present';
                }
                
                $table = "<tr>
                    <td>$student->first_name</td>
                    <td>$student->last_name</td>
                    <td>$ifpresent</td>
                    <td>$student->grade</td>
                    <td>$vehicle->title $vehicle->plate_num</td>
                    <td>$driver->name</td>
                    <td>$item->created_at</td>
                </tr>";
            }
        }

        return response(['table' => $table]);
    }


    public function adsentToday()
    {
        $date = date('Y-m-d');

        $flagoffs = FlagOff::where('date', '=', $date)->get();

        $teacher = Auth::user();

        return view('teacher.flagoff')->with([
            'flagoffs' => $flagoffs,
            'teacher' => $teacher
        ]);
    }
}


