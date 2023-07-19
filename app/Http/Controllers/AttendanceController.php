<?php

namespace App\Http\Controllers;


use App\AttendanceDetails;
use App\Models\Attendance;
use App\Models\Stream;
use App\Models\Student;
use App\Models\User;
use Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $date = date('Y-m-d');

        $attendanceAm = Attendance::where('created_at', 'LIKE', '%'. $date .'%')->where('route_time', 'LIKE', 'am')->orderBy('grade', 'ASC')->orderBy('Vehicle_id', 'ASC')->get();
        $attendancePm = Attendance::where('created_at', 'LIKE', '%'. $date .'%')->where('route_time', 'LIKE', 'pm')->orderBy('grade', 'ASC')->orderBy('Vehicle_id', 'ASC')->get();

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('attendance.index')->with(['attendanceAm' => $attendanceAm, 'attendancePm' => $attendancePm, 'notifications' => $notifications]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $students = Student::all();

        $teacher = Auth::user();

        if ($teacher->user_type == 'teacher') {
            return abort(401, 'Unauthorized');
        }

        $stream = Stream::where('class_teacher','=', $teacher->id)->first();

        $date = ('Y-m-d');

        dd($stream);
        if (! $stream) {
            return abort(401, 'Unauthorized');
        }

        $attendanceAm = Attendance::where('created_at', 'LIKE', '%'. $date .'%')->where('route_time', 'LIKE', 'am')->orderBy('grade', 'ASC')->orderBy('Vehicle_id', 'ASC')->get();

        return view('attendance.create')->with([
            'students' => $students,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $date = date('Y-m-d');
        $len = count($request->student_id);
        
        for ($i=0; $i < $len; $i++) { 
          
                $attendance = new Attendance();
                $attendance->vehicle_id = $request->vehicle_id;
                $attendance->route_time = $request->route_time;
                $attendance->student_id = $request->student_id[$i];
                $attendance->present = $request->present[$i];
                $attendance->date = date('Y-m-d');

                $student = Student::find($request->student_id[$i]);
                $attendance->grade = $student->grade;
                $attendance->save();
            
        }

        return redirect()->route('attendaces');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
