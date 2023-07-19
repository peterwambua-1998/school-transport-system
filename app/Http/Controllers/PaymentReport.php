<?php

namespace App\Http\Controllers;

use App\Models\FeePayment;
use App\Models\SchoolTrip;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\StudentFeeDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentReport extends Controller
{
    public function schoolFees() 
    {
        $students = Student::all();

        foreach ($students as $key => $student) {
            $student_fees = StudentFee::where('student_id','=', $student->id)->get();
            foreach ($student_fees as $key => $student_fee) {
                $fee_details = StudentFeeDetails::where('school_fees_id','=', $student_fee->id)->get();
                $total = 0;
                foreach ($fee_details as $fee_detail) {
                    $total += $fee_detail->detail_amount;
                }
                $fee_payments = FeePayment::where('student_fees_id','=', $student_fee->id)->get();
                $student_fee->details = $fee_details;
                $student_fee->payments = $fee_payments;
            }
            $student->fees = $student_fee;
        }

        return response($students);
    }

    public function transportFees() 
    {
        $students = Student::where('transport','=', 1)->get();

        foreach ($students as $student) {
            $student_fees = StudentFee::where('student_id','=', $student->id)->get();
            foreach ($student_fees as $student_fee) {
                $fee_details = StudentFeeDetails::where('school_fees_id','=', $student_fee->id)->get();
                $total = 0;
                foreach ($fee_details as $fee_detail) {
                    $total += $fee_detail->detail_amount;
                }
                $student_fee->total_amount = $total;
                $fee_payments = FeePayment::where('student_fees_id','=', $student_fee->id)->get();
                $student_fee->details = $fee_details;
                $student_fee->payments = $fee_payments;
            }
            $student->fees = $student_fee;
        }

        return response($students);
    }

    public function schoolTripFees() 
    {
        $school_trips = SchoolTrip::where('status','=','paid')->get();

        foreach ($school_trips as $school_trip) {
            $payments = DB::table('school_trip_payment_tables')->where('schooltrip_id','=', $school_trip->id)->get();
            foreach ($payments as $payment) {
                $student = Student::find($payment->student_id);
                $grade = DB::table('student_classes')->where('id','=', $student->grade)->first();
                $stream = Stream::where('id','=', $student->stream)->first();
                $student->std_grade = $grade;
                $student->std_stream = $stream;
                $payment->student = $student;
            }
            $vehicles = DB::table('schooltrip_vehicle')->where('schooltrip_id','=', $school_trip->id)->get();
            $destinations = DB::table('school_trips_destinations')->where('schooltrip_id','=', $school_trip->id)->get();
            $school_trip->payment = $payments;
            $school_trip->vehicles = $vehicles;
            $school_trip->destinations = $destinations;
        }

        return response($school_trips);
    }
}
