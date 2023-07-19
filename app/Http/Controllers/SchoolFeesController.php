<?php

namespace App\Http\Controllers;

use App\Models\FeeEntry;
use App\Models\FeePayment;
use App\Models\SAndT;
use App\Models\SchoolFees;
use App\Models\SchoolTermDate;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\StudentFeeDetails;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SchoolFeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $term = SchoolTermDate::where('status','=',1)->first();
        if (! $term) {
            return  redirect()->route('term.create')->with('unsuccess','Please add term');
        }

        $schoolFees = SchoolFees::where('term','=', $term->id)->get();
        return view('school-fees.index', compact('schoolFees','term'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $grades = DB::table('student_classes')->where('status','=',1)->get();
        $term = SchoolTermDate::where('status','=',1)->first();

        foreach ($grades as $key => $grade) {
            $sch = SchoolFees::where('grade','=', $grade->id)->where('term','=', $term->id)->first();
            if ($sch) {
                $grades->forget($key);
            }
        }
        if (! $term) {
            return  redirect()->route('term.create')->with('unsuccess','Please add term');
        }

        return view('school-fees.create', compact('term','grades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if ($request->grade == 'select...') {
            return redirect()->back()->with('unsuccess','Please select grade.');
        }

        $request->validate([
            'year' => 'required',
            'term' => 'required',
        ]);

        $term = SchoolTermDate::where('status','=', 1)->first();
        $stds = Student::where('grade','=', $request->grade)->get();

        if ($stds->isNotEmpty()) {
            $fee2 = SchoolFees::where('grade','=', $request->grade)->first();
            $fee_details2 = FeeEntry::where('fee_id','=',$fee2->id)->get();
            $student_fees = StudentFee::where('fee_id','=',$fee2->id)->get();
            $check = false;
            foreach ($student_fees as $key => $ff) {
                $checkF = FeePayment::where('school_fees_id','=',$ff->id)->first();
                if ($checkF) {
                    $check = true;
                }
            }
            if (!$check) {
                
                foreach ($student_fees as $key => $student_fee) {
                    DB::table('student_fee_details')->where('student_fees_id','=',$student_fee->id)->delete();
                }
                DB::table('student_fees')->where('fee_id','=',$fee2->id)->delete();
                DB::table('fee_entries')->where('fee_id','=',$fee2->id)->delete();
                $fee2->delete();
    
                DB::transaction(function() use ($request, $stds) {
                    $amt = 0;
                    for ($i=0; $i < count($request->entries); $i++) { 
                        $amt += $request->amount[$i];
                    }
        
                    $fees = new SchoolFees();
                    $fees->grade = $request->grade;
                    $fees->amount = $amt;
                    $fees->year = $request->year;
                    $fees->term = $request->term;
                    $fees->invoice_num = date('YmdHis');
                    $fees->save();
        
                    for ($i=0; $i < count($request->entries); $i++) { 
                        $fee_entries = new FeeEntry();
                        $fee_entries->fee_id = $fees->id;
                        $fee_entries->entry = $request->entries[$i];
                        $fee_entries->amount = $request->amount[$i];
                        $fee_entries->save();
                    }

                    foreach ($stds as $key => $std) {
                        $studentFee = new StudentFee();
                        $studentFee->student_id = $std->id;
                        $studentFee->fee_id = $fees->id;
                        $studentFee->grade = $fees->grade;
                        $studentFee->amount = $amt;
                        $studentFee->year = $fees->year;
                        $studentFee->term = $fees->term;
                        $studentFee->status = 0;
                        $studentFee->invoice_num = $fees->invoice_num;
                        $studentFee->save();
                        for ($i=0; $i < count($request->entries); $i++) { 
                            $fee_entries = new StudentFeeDetails();
                            $fee_entries->student_fees_id = $studentFee->id;
                            $fee_entries->detail = $request->entries[$i];
                            $fee_entries->detail_amount = $request->amount[$i];
                            $fee_entries->save();
                        }

                    }
                });
            } else {
                return redirect()->back()->with('unsuccess','School fees cannot be changed due to payments.');
            }
        } else {
            $schoolFeesCheck = SchoolFees::where('grade','=', $request->grade)->where('term','=', $term->id)->first();
            
            
            DB::transaction(function() use ($request,$schoolFeesCheck) {
                if ($schoolFeesCheck) {
                    DB::table('fee_entries')->where('fee_id','=',$schoolFeesCheck->id)->delete();
                    $schoolFeesCheck->delete();
                }

                $amt = 0;
                for ($i=0; $i < count($request->entries); $i++) { 
                    $amt += $request->amount[$i];
                }
    
                $fees = new SchoolFees();
                $fees->grade = $request->grade;
                $fees->amount = $amt;
                $fees->year = $request->year;
                $fees->term = $request->term;
                $fees->invoice_num = date('YmdHis');
                $fees->save();
    
                for ($i=0; $i < count($request->entries); $i++) { 
                    $fee_entries = new FeeEntry();
                    $fee_entries->fee_id = $fees->id;
                    $fee_entries->entry = $request->entries[$i];
                    $fee_entries->amount = $request->amount[$i];
                    $fee_entries->save();
                }
               
            });
        }
        return redirect()->route('school-fees.index')->with('success','Record saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SchoolFees  $schoolFees
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fees = SchoolFees::find(Crypt::decrypt($id));
        $entries = FeeEntry::where('fee_id','=', $fees->id)->get();
        $term = SchoolTermDate::find($fees->term);
        $grade = DB::table('student_classes')->where('id','=', $fees->grade)->first();
        return view('school-fees.show', compact('fees','entries','term','grade'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SchoolFees  $schoolFees
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fees = SchoolFees::find(Crypt::decrypt($id));
        $entries = FeeEntry::where('fee_id','=', $fees->id)->get();
        $term = SchoolTermDate::find($fees->term);
        $grades = DB::table('student_classes')->where('status','=',1)->get();

        return view('school-fees.edit', compact('fees','entries','grades','term'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SchoolFees  $schoolFees
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $fees = SchoolFees::find(Crypt::decrypt($id));
        //check for payments
        $check = FeePayment::where('school_fees_id','=',$fees->id)->first();

        if ($check) {
            return redirect()->back()->with('unsuccess','Payments have been made against this fee structure');
        }
        $request->validate([
            'amount' => 'required',
            'year' => 'required',
            'term' => 'required',
        ]);

        DB::transaction(function() use ($request, $id, $fees) {
            $amt = 0;
            for ($i=0; $i < count($request->entries); $i++) { 
                if ($request->entries[$i]) {
                    $amt = $request->amount[$i];
                }
            }

            $fees->grade = $request->grade;
            $fees->amount = $amt;
            $fees->year = $request->year;
            $fees->term = $request->term;
            $fees->update();

            DB::table('fee_entries')->where('fee_id',$fees->id)->delete();
            
            for ($i=0; $i < count($request->entries); $i++) { 
                if ($request->entries[$i]) {
                    $fee_entries = new FeeEntry();
                    $fee_entries->fee_id = $fees->id;
                    $fee_entries->entry = $request->entries[$i];
                    $fee_entries->amount = $request->amount[$i];
                    $fee_entries->save();
                }
                
            }

            $student_fees = StudentFee::where('fee_id','=', $fees->id)->get();
            foreach ($student_fees as $student_fee) {
                DB::table('student_fee_details')->where('student_fees_id','=',$student_fee->id)->delete();
            }
            DB::table('student_fees')->where('fee_id','=',$fees->id)->delete();

            $stds = Student::where('grade','=', $request->grade)->get();

            foreach ($stds as $key => $std) {
                $studentFee = new StudentFee();
                $studentFee->student_id = $std->id;
                $studentFee->fee_id = $fees->id;
                $studentFee->grade = $fees->grade;
                $studentFee->amount = $amt;
                $studentFee->year = $fees->year;
                $studentFee->term = $fees->term;
                $studentFee->status = 0;
                $studentFee->invoice_num = $fees->invoice_num;
                $studentFee->save();
                for ($i=0; $i < count($request->entries); $i++) { 
                    $fee_entries = new StudentFeeDetails();
                    $fee_entries->student_fees_id = $studentFee->id;
                    $fee_entries->detail = $request->entries[$i];
                    $fee_entries->detail_amount = $request->amount[$i];
                    $fee_entries->save();
                }

            }

        });
        
        return redirect()->route('school-fees.index')->with('success','Record updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SchoolFees  $schoolFees
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schoolFees = SchoolFees::find($id);

        $check = FeePayment::where('school_fees_id','=',$schoolFees->id)->first();

        if ($check) {
            return redirect()->back()->with('unsuccess','Payments have been made against this fee structure');
        }
        $schoolFees->active = 0;
        if ($schoolFees->update()) {
            return redirect()->back()->with('success','Record deactivated successfully');
        }
        /*
        DB::table('fee_entries')->where('fee_id',$schoolFees->id)->delete();
        DB::table('student_fees')->where('fee_id',$schoolFees->id)->delete();
        DB::table('student_fee_details')->where('fee_id',$schoolFees->id)->delete();

        if ($schoolFees->delete()) {
            return redirect()->back()->with('succcess','Record deleted successfully');
        }
        */

        return redirect()->route('school-fees.index')->with('unsuccess','System error please try again');
    }

    public function activate(Request $request)
    {
        $schoolFees = SchoolFees::find($request->fee_id);
        $schoolFees->active = 1;
        if ($schoolFees->update()) {
            return redirect()->back()->with('success','Record activated successfully');
        }
        return redirect()->route('school-fees.index')->with('unsuccess','System error please try again');
    }

    /**
     * assigne fee to student
     */
    public function assignFee(Request $request)
    {
        $student = Student::find($request->student_id);


        $schoolFees = SchoolFees::find($request->schoolfees);
        $schoolFeesDetails = FeeEntry::where('fee_id','=',$schoolFees->id)->get();

        DB::transaction(function () use ($student, $schoolFees, $schoolFeesDetails) {
            $studentFee = new StudentFee();
            $studentFee->student_id = $student->id;
            $studentFee->grade = $schoolFees->grade;
            $studentFee->amount = $schoolFees->amount;
            $studentFee->year = $schoolFees->year;
            $studentFee->term = $schoolFees->term;
            $studentFee->status = 0;
            $studentFee->invoice_num = $schoolFees->invoice_num;
            $studentFee->save();

            foreach ($schoolFeesDetails as $key => $detail) {
                $studentFeeDetails = new StudentFeeDetails();
                $studentFeeDetails->student_fees_id = $studentFee->id;
                $studentFeeDetails->detail = $detail->entry;
                $studentFeeDetails->detail_amount = $detail->amount;
                $studentFeeDetails->save();
            }
        });

        return redirect()->back()->with('success', 'School fees assigned to student');
    }

    public function paymentPage($id)
    {
        $student_fee = StudentFee::where('id','=',Crypt::decrypt($id))->first();
        $student = Student::find($student_fee->student_id);
        $fee_payments = FeePayment::where('school_fees_id','=',$student_fee->id)->get();
        $fee_details = StudentFeeDetails::where('student_fees_id','=', $student_fee->id)->get();
        $balance = 0;
        $total = 0;

        foreach ($fee_payments as $key => $fee_payment) {
            $balance += $fee_payment->amount_paid;
        }
        foreach ($fee_details as $key => $detail) {
            $total += $detail->detail_amount;
        }

        $balance = $student_fee->amount - $balance;

        if (!$student) {
            return redirect()->back()->with('unsuccess','Student not found');
        }
        if (!$student_fee) {
            return redirect()->back()->with('unsuccess','School fees not found');
        }
        return view('students.payment', compact('student_fee','student','balance','total'));
    }

    public function storePayment(Request $request)
    {
        
        $student_fee = StudentFee::where('id','=', $request->student_fee_id)->first();

        if (!$student_fee) {
            return redirect()->back()->with('unsuccess','School fees not found');
        }

        if ($request->payment_method == 'select...') {
            return redirect()->back()->with('unsuccess','Please select payment method');
        }

        $student = Student::find($request->student_id);
        $parent = User::find($student->parent_id);

        $prev_payments = FeePayment::where('school_fees_id','=', $student_fee->id)->get();
        $paid_amt = 0;
        foreach ($prev_payments as $payment) {
            $paid_amt+= $payment->amount_paid;
        }
        $paid_amt += $request->amount_paid;

        $fee_payment = new FeePayment();
        $fee_payment->receipt_number = $request->receipt_number;
        $fee_payment->school_fees_id = $student_fee->id;
        $fee_payment->parent = $parent->id;
        $fee_payment->student = $request->student_id;
        $fee_payment->amount_paid = $request->amount_paid;
        $fee_payment->payment_method = $request->payment_method;
        $fee_payment->balance = $student_fee->amount - $paid_amt;
        $fee_payment->date_paid = $request->date_paid;
        if ($fee_payment->save()) {
            return redirect()->route('students.show',Crypt::encrypt($student->id))->with('success','Record added successfully');
        }
        return redirect()->route('students.show',Crypt::encrypt($student->id))->with('unsuccess','System error please try again');

    }
}
