<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\FeeEntry;
use App\Models\FeePayment;
use App\Models\Invoice;
use App\Models\PickupPointStudent;
use App\Models\ReceiptSchoolTrip;
use App\Notifications\GeneratedPassword;
use App\Models\SAndT;
use App\Models\SchoolFees;
use App\Models\SchoolTermDate;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\StudentFee;
use App\Models\StudentFeeDetails;
use App\Models\Trip;
use App\StudentTrips;
use App\Models\User;
use App\Models\Vehicle;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $students = Student::orderBy('grade', 'ASC')->orderBy('Vehicle_id', 'ASC')->get();

        $unallocatedCount = count(Student::where('transport','=', 1)->where('bus_assigned','=', 0)->get());

        return view('students.index')->with(['students'=> $students,'unallocatedCount' => $unallocatedCount]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $vehicles = Vehicle::where('status','=',1)->get();

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $parents = User::where('user_type', 'LIKE', 'parent')->get();
       
        $classes = StudentClass::where('status','=',1)->get();

        return view('students.create')->with(['vehicles'=> $vehicles, 'notifications' => $notifications, 'parents' => $parents, 'classes' => $classes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'grade' => 'required',
        ]);

        if ($request->grade == 'select...') {
            return redirect()->back()->with('unsuccess','Please select grade');
        }

        if ($request->stream == 'select...') {
            return redirect()->back()->with('unsuccess','Please select stream');
        }

        if ($request->gender == 'select...') {
            return redirect()->back()->with('unsuccess','Please select gender');
        }

        if ($request->parent_id == 'select...') {
            return redirect()->back()->with('unsuccess','Please select parent');
        }

        if ($request->relationship == 'select...') {
            return redirect()->back()->with('unsuccess','Please select relationship');
        }

        $term = SchoolTermDate::where('status','=', 1)->first();

        
        DB::transaction(function () use ($request, $term) {
            $student = new Student();
            $student->vehicle_id = $request->vehicle;
            $student->parent_id = $request->parent_id;
            $student->first_name = $request->fname;
            $student->last_name = $request->lname;
            $student->grade = $request->grade;
            $student->add_num = $request->add_num;
            $student->stream = $request->stream;
            $student->gender = $request->gender;
            if($request->image) {

                $path = $request->file('image')->store('students','public_uploads'); 

                $student->image = $path;
            }
            $student->save();

            $parent = User::find($request->parent_id);
            $parent->relationship = $request->relationship;
            $parent->update();

            $fees = SchoolFees::where('grade','=',$request->grade)->where('term','=', $term->id)->first();

            if ($fees) {
                $studentFee = new StudentFee();
                $studentFee->student_id = $student->id;
                $studentFee->fee_id = $fees->id;
                $studentFee->grade = $fees->grade;
                $studentFee->amount = $fees->amount;
                $studentFee->year = $fees->year;
                $studentFee->term = $fees->term;
                $studentFee->status = 0;
                $studentFee->invoice_num = $fees->invoice_num;
                $studentFee->save();

                $fee_entries = FeeEntry::where('fee_id','=', $fees->id)->get();
                foreach ($fee_entries as $fee_entry) {
                    $studentFeeDetails = new StudentFeeDetails();
                    $studentFeeDetails->student_fees_id = $studentFee->id;
                    $studentFeeDetails->detail = $fee_entry->entry;
                    $studentFeeDetails->detail_amount = $fee_entry->amount;
                    $studentFeeDetails->save();
                }
            }
        });
        return redirect()->route('students.index')->with('success', 'Record added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $term = SchoolTermDate::where('status','=',1)->first();
        if (! $term) {
            return  redirect()->route('term.create')->with('unsuccess','Please add term');
        }
        $student = Student::find(Crypt::decrypt($id));
        $parent_two = User::where('user_type','=','parent two')->where('linked_to','=',$student->parent->id)->first();
        $guardian = User::where('user_type','=','other')->where('linked_to','=',$student->parent->id)->first();
        //fee assigned to student
        $fees = StudentFee::where('student_id','=',$student->id)->where('term','=',$term->id)->orderBy('created_at','desc')->get();
        //hold school fees for the child
        $schoolfees = new Collection();
        //school fees for student grade
        $schoolFees = SchoolFees::where('grade','=',$student->grade)->where('term','=',$term->id)->get();
        $student_fee_amt = 0;
        foreach ($fees as $key => $fee) {
            //student fee details holds details for each assigned fee so as to add transport to it
            $feeDetails = StudentFeeDetails::where('student_fees_id','=', $fee->id)->get();
            $fee->details = $feeDetails;

            //add fee payments to the student fees
            $feePayments = FeePayment::where('school_fees_id','=',$fee->id)->get();
            $fee->payemnts = $feePayments;
            $schoolfees->push($fee);
        }
        //payment history for all fees
        $fee_history = StudentFee::where('student_id','=',$student->id)->orderBy('created_at','desc')->get();


        foreach ($fee_history as $history) {
            $pay = FeePayment::where('school_fees_id','=',$history->id)->get();
            $history->payments = $pay;
        }

        //student vehicle trip information
        $student_tripss = SAndT::where('student_id','=',$student->id)->get();


        $vehicle_trips = new Collection();

        foreach ($student_tripss as $key => $student_trip) {
            $trip = Trip::find($student_trip->trip_id);
            $vehicle = Vehicle::where('id','=',$trip->vehicle_id)->first();
            $vehicle->trip = $trip;
            $vehicle_trips->push($vehicle);
        }

        dd($vehicle_trips);
        


        $trips = SAndT::where('student_id','=',$student->id)->get();
        $vehicle = DB::table('vehicle_students')->where('student_id','=',$student->id)->get();
        return view('students.show', compact('student','vehicle_trips','parent_two','guardian','schoolfees','trips','schoolFees','term','fee_history'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = Student::find(Crypt::decrypt($id));


        $vehicles = Vehicle::where('status','=',1)->get();

        $user = Auth::user();

        $parents = User::where('user_type','=','parent')->get();

        $relation = $student->parent->relationship;

        $classes = StudentClass::where('status','=',1)->get();

        return view('students.edit')->with([
            'student' => $student,
            'vehicles' => $vehicles,
            'classes' => $classes,
            'parents' => $parents,
            'relation' => $relation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->grade == 'select...') {
            return redirect()->back()->with('unsuccess','Please select grade');
        }

        if ($request->stream == 'select...') {
            return redirect()->back()->with('unsuccess','Please select stream');
        }

        if ($request->gender == 'select...') {
            return redirect()->back()->with('unsuccess','Please select gender');
        }

        if ($request->parent_id == 'select...') {
            return redirect()->back()->with('unsuccess','Please select parent');
        }

        if ($request->relationship == 'select...') {
            return redirect()->back()->with('unsuccess','Please select relationship');
        }
        
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'grade' => 'required',
        ]);

        $student = Student::find($id);
        $student->first_name = $request->fname;
        $student->last_name = $request->lname;
        $student->parent_id = $request->parent_id;
        $student->grade = $request->grade;
        $student->stream = $request->stream;
        $student->add_num = $request->add_num;
        $student->gender = $request->gender;
        if($request->image) {
            if ($student->image) {
                Storage::disk('public_uploads')->delete($student->image);
            }
            $path = $request->file('image')->store('students','public_uploads'); 

            $student->image = $path;
        }

        $parent = User::find($student->parent_id);
        $parent->relationship = $request->relationship;
        $parent->update();

        if ($student->update()) {
            return redirect()->route('students.index')->with('success', 'Record updated Successfully');
        }

        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $student = Student::find($request->student_id);
        $student->status = 0;
        if($student->update()) {
            return redirect()->back()->with('success', 'Record deactivated successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error, try again.');

        /*
        $attendances = Attendance::where('student_id', '=', $student->id)->get();
        $invoices = Invoice::where('student_id', '=', $student->id)->get();

        DB::transaction(function () use($student, $attendances, $invoices) {
            DB::table('depature_checklists')->where('student_id','=',$student->id)->delete();
            DB::table('return_checklists')->where('student_id','=',$student->id)->delete();
            DB::table('fee_payments')->where('student','=',$student->id)->delete();
            DB::table('vehicle_students')->where('student_id','=',$student->id)->delete();
            DB::table('pickup_point_students')->where('student_id','=',$student->id)->delete();
            DB::table('student_fees')->where('student_id','=',$student->id)->delete();
            DB::table('incidents')->where('student_assulter','=',$student->id)->delete();
            DB::table('flag_offs')->where('student_id','=',$student->id)->delete();
            DB::table('s_and_t_s')->where('student_id','=',$student->id)->delete();
            DB::table('reviews')->where('student_id','=',$student->id)->delete();
            $schooltripReceipt = ReceiptSchoolTrip::where('student_id','=',$student->id)->get();
            foreach ($schooltripReceipt as $inv) {
                $inv->student_id = null;
                $inv->update();
            }
            foreach ($invoices as $invoice) {
                $invoice->student_id = null;
                $invoice->update();
            }
    
            foreach ($attendances as $attendance) {
                $attendance->delete();
            }
    
            Storage::disk('public_uploads')->delete('store/'.$student->image);
            $student->delete();
        });
        */
    }

    public function activate(Request $request) 
    {
        $student = Student::find($request->student_id);
        $student->status = 1;
        if($student->update()) {
            return redirect()->back()->with('success', 'Record activated successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error, try again.');

    }

    public function allstd()
    {
        $students = Student::with('parent', 'vehicle')->get();

        return response($students);
    }


    /**
     * change pick up from yes to no or viceverser
     */
    public function puckUp(Request $request,$id)
    {

        //return response(['student' => $id, 'pickup' => $request->pickup]);
        $student = Student::find($id);

        $student->pick_up = $request->pickup;

        if ($student->update()) {
            return response(1);
        }

        return response(0);
    }

    /**
     * get streams for selected classs
     */
    public function getStreams($id)
    {
        $class = DB::table('student_classes')->where('id','=', $id)->first();

        $streams = DB::table('streams')->where('student_classes_id','=', $class->id)->get();

        return response($streams);
    }

    public function saveTripDefinition(Request $request,$id) 
    {
        $student = Student::find(Crypt::decrypt($id));

        if($request->own_trans == "own") {
            DB::table('pickup_point_students')->where('student_id','=', $student->id)->delete();
            DB::table('s_and_t_s')->where('student_id','=', $student->id)->delete();
            $student->transport = 0;
            if ($student->update()) {
                return redirect()->back()->with('success','Transport definitions successful');
            }
        }

        if ($request->own_trans == "school") {
            $student->transport = 1;
            $student->trip_type = $request->transport_type;
            if ($student->update()) {
                return redirect()->back()->with('success','Transport definitions successful');
            }
        }

        return redirect()->back()->with('unsuccess','System error please try again');
        
    }
}
