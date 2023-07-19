<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use Illuminate\Http\Request;

class ClassGroupController extends Controller
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
        $group = new ClassGroup();
        $group->name = $request->group;

        if ($group->save()) {
            return redirect()->back()->with('success','Record added successfully');
        }

        return redirect()->back()->with('unsuccess','Sytem error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClassGroup  $classGroup
     * @return \Illuminate\Http\Response
     */
    public function show(ClassGroup $classGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClassGroup  $classGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassGroup $classGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassGroup  $classGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassGroup $classGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassGroup  $classGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassGroup $classGroup)
    {
        //
    }
}
