<?php

namespace App\Http\Controllers;

use App\Models\DepatureChecklist;
use App\Models\ReturnChecklist;
use App\Models\SchoolAttendance;
use App\Models\SchoolTermDate;
use App\Models\SchoolTrip;
use App\Models\SchoolTripGrade;
use App\Models\SchoolTripPaymentTable;
use App\Models\Student;
use App\Models\TermEvent;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TeacherContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teacher = Auth::user();
        $schooltrips_teacher = DB::table('schooltrip_teacher')->where('teacher_id','=',$teacher->id)->get();
        $term = SchoolTermDate::where('status','=', 1)->first();
        $schooltrips = new Collection();

        foreach ($schooltrips_teacher as $key => $trip) {
            $trip = SchoolTrip::where('id','=', $trip->schooltrip_id)->first();
            if ($trip->term_id == $term->id) {
                $schooltrips->push($trip);
            }
        }

        
        return view('school_attendance.schooltrips')->with([
            'schooltrips' => $schooltrips
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $schooltrip = SchoolTrip::find(Crypt::decrypt($id));

        $depatureChecklists = DepatureChecklist::where('schooltrip_id', '=', $schooltrip->id)->get();
        
        $returnChecklists = ReturnChecklist::where('schooltrip_id', '=', $schooltrip->id)->get();
      
        return view('school_attendance.showtripdetails')->with([
            'schooltrip' => $schooltrip,
            'depatureChecklists' => $depatureChecklists,
            'returnChecklists' => $returnChecklists
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
        //
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

    /**
     * page to add students to school trip
     */
    public function pageToAddStdToScholTrip($id)
    {
        $schooltrip = SchoolTrip::find(Crypt::decrypt($id));

        $vehicles = DB::table('schooltrip_vehicle')->where('schooltrip_id','=', $schooltrip->id)->get();

        $students = new Collection();

        if ($schooltrip->status == "unpaid") {
            $grades = DB::table('school_trip_grades')->where('schooltrip_id','=', $schooltrip->id)->get();
            foreach ($grades as $key => $grade) {
                $student = Student::where('grade','=', $grade->grade_id)->get();
                foreach ($student as $key => $std) {
                    $students->push($std);
                }
            }
        }


        if ($schooltrip->status == "paid" && $schooltrip->grade != "general") {
            $paidStudents = SchoolTripPaymentTable::where('schooltrip_id','=',$schooltrip->id)->where('marked','=', 0)->get();
            $students = Student::where('grade','=',$schooltrip->grade)->get();
            return view('school_attendance.paid-trip-std-list', compact('schooltrip','vehicles','paidStudents','students'));
        }

        if ($schooltrip->status == "paid" && $schooltrip->grade == "general") {
            $paidStudents = SchoolTripPaymentTable::where('schooltrip_id','=',$schooltrip->id)->where('marked','=', 0)->get();
            $students = Student::all();
            return view('school_attendance.paid-trip-std-list', compact('schooltrip','vehicles','paidStudents','students'));
        }



        return view('school_attendance.unpaid-trip-std-list', compact('schooltrip','vehicles','students'));
    }

    public function addMyStudents(Request $request)
    {
        $trip = SchoolTrip::find($request->trip);

        $date = date('Y-m-d');

        $students = count($request->students);

        DB::transaction(function() use ($trip, $students, $request, $date) {
            for ($i=0; $i < $students; $i++) { 
                $depature = DepatureChecklist::where('schooltrip_id','=', $trip->id)->where('student_id','=',$request->students[$i])->first(); 
                if ($depature) {
                    $depature->vehicle_id = $request->vehicle[$i];
                    $depature->update();
                } else {
                    if ($request->vehicle[$i] !== "select...") {
                        $student = Student::find($request->students[$i]);
                        $depatureChecklists = new DepatureChecklist();
                        $depatureChecklists->schooltrip_id = $trip->id;
                        $depatureChecklists->student_id = $request->students[$i];
                        $depatureChecklists->vehicle_id = $request->vehicle[$i];
                        $depatureChecklists->grade = $student->grade;
                        $depatureChecklists->stream = $student->stream;
                        $depatureChecklists->attendance = 'absent';
                        $depatureChecklists->date = $date;
                        $depatureChecklists->save();

                        DB::table('school_trip_payment_tables')->where('student_id','=',$request->students[$i])->where('schooltrip_id','=', $trip->id)->update([
                            "marked" => 1
                        ]);
                    }
                }
                
            }
        });


        return redirect()->route('teachertrips_show', Crypt::encrypt($trip->id))->with('success','Students added to depature checklist');


        /*
        $trip_id = $request->trip_id;

        $teacher = Auth::user();

        $date = date('Y-m-d');

        $students = SchoolAttendance::where('grade', '=', $teacher->grade)
                                        ->where('created_at', 'LIKE', '%'. $date .'%')
                                        ->get();

        foreach ($students as $student) {
            $depatureChecklists = new DepatureChecklist();
            $depatureChecklists->schooltrip_id = $trip_id;
            $depatureChecklists->student_id = $student->student_id;
            $depatureChecklists->grade = $student->grade;
            $depatureChecklists->attendance = 'absent';
            $depatureChecklists->save();
            
        }

        return redirect()->back()->with('success', 'Students were addes to depature checklist.');
        */
    }


    public function removeStudent(Request $request)
    {

        $request->validate([
            'trip_id' => 'required',
            'student_id' => 'required'
        ]);

        $depatureChecklist = DepatureChecklist::where('schooltrip_id','=', $request->trip_id)->where('student_id','=', $request->student_id)->first();
        if ($depatureChecklist) {
            if($depatureChecklist->delete()){
                return response(1);
            }
        }
        return response(0);
    }

    public function saveAttendance(Request $request)
    {
        $students = $request->student_id;

        $tripid = '';

        foreach ($students as $key => $student) {

            $tripid = $request->trip_id;
            
            $depatureChecklist = DepatureChecklist::where('schooltrip_id', '=', $request->trip_id[$key])
                            ->where('student_id', '=', $student)
                            ->first();

            
            $depatureChecklist->attendance = $request->present[$key];

            $depatureChecklist->update();

            $returnchecklist = new ReturnChecklist();
            $returnchecklist->schooltrip_id = $request->trip_id[$key];
            $returnchecklist->student_id = $student;
            $returnchecklist->attendance = 'absent';
            $returnchecklist->save();
           
        }

        return redirect()->route('teachertrips_show', $tripid)->with('success', 'Attendance stored successfullyy');
    }


    public function markAttendanceReturn($id)
    {
        $return = DepatureChecklist::where('schooltrip_id', '=', $id)->where('attendance', 'LIKE', 'present')->get();
       
        return view('school_attendance.markattendancereturn')->with([
            'depature' => $return
        ]);
    }

    public function saveAttendanceReturn(Request $request)
    {
        $students = $request->student_id;

        $tripid = '';

        foreach ($students as $key => $student) {

            $tripid = $request->trip_id;
            
            $depatureChecklist = ReturnChecklist::where('schooltrip_id', '=', $request->trip_id[$key])->where('student_id', '=', $student)->first();

            
            $depatureChecklist->attendance = $request->present[$key];

            $depatureChecklist->update();
        }

        return redirect()->route('teachertrips_show', $tripid)->with('success', 'Attendance stored successfullyy');
    }
    
    public function event()
    {
        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();

        if (! $schoolterm) {
            return redirect()->back()->with('unsuccess', 'There are no registered terms');
        }
        
	    $terms = TermEvent::where('term_id', '=', $schoolterm->id)->get();

        return view('school_attendance.teacher-events')->with([
            'terms' => $terms
        ]);
    }
}
