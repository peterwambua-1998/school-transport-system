<?php

namespace App\Http\Controllers;

use App\Models\FlagOff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlagOffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $date = date('Y-m-d');
        
        $flag = new FlagOff();
        $flag->parent_id = Auth::user()->id;
        $flag->student_id = $request->student_id;
        $flag->reason = $request->reason;
        $flag->date = $date;

        if ($flag->save()) {
            return response('student will not be picked up');
        }

        return response('System error please try again');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FlagOff  $flagOff
     * @return \Illuminate\Http\Response
     */
    public function show(FlagOff $flagOff)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FlagOff  $flagOff
     * @return \Illuminate\Http\Response
     */
    public function edit(FlagOff $flagOff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FlagOff  $flagOff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FlagOff $flagOff)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FlagOff  $flagOff
     * @return \Illuminate\Http\Response
     */
    public function destroy(FlagOff $flagOff)
    {
        //
    }
}
