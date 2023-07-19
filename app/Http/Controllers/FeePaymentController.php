<?php

namespace App\Http\Controllers;

use App\Models\FeePayment;
use App\Models\SchoolFees;
use App\Models\Student;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fees = FeePayment::orderBy('created_at','desc')->get();

        return view('fee-payments.index', compact('fees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $student = Student::find($id);

        $schoolfees = SchoolFees::where('student','=',$student->id)->orderBy('created_at','desc')->get();

        return view('fee-payments.create', compact('student','schoolfees'));
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
            'receipt_number' => 'required',
            'school_fees_id' => 'required',
            'student' => 'required',
            'amount_paid' => 'required',
            'payment_method' => 'required',
            'date_paid' => 'required',
        ]);

        $feePayment = new FeePayment();
        $feePayment->receipt_number = $request->receipt_number;
        $feePayment->school_fees_id = $request->school_fees_id;
        $feePayment->student = $request->student;
        $feePayment->amount_paid = $request->amount_paid;
        $feePayment->payment_method = $request->payment_method;
        $feePayment->date_paid = $request->date_paid;

        if ($feePayment->save()) {
            return redirect()->route('fee-payment.index')->with('success','Fee payment saved successfully');
        }
        return redirect()->route('fee-payment.index')->with('unsuccess','System error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeePayment  $feePayment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $schoolfees = SchoolFees::where('student','=',$id)->orderBy('created_at','desc')->get();
        
        return view('fee-payment.show',compact('schoolfees'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FeePayment  $feePayment
     * @return \Illuminate\Http\Response
     */
    public function edit(FeePayment $feePayment)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeePayment  $feePayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeePayment $feePayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FeePayment  $feePayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeePayment $feePayment)
    {
        //
    }
}
