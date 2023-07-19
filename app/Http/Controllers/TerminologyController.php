<?php

namespace App\Http\Controllers;

use App\Models\Terminology;
use Illuminate\Http\Request;

class TerminologyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $terminology = Terminology::find(1);
        
        return view('terminology.create',compact('terminology'));
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
            'terminology_grade' => 'required',
            'plural' => 'required'
        ]);

        $t  = Terminology::find(1);
        if ($t) {
            $t->grade_class = $request->terminology_grade;
            $t->plural = $request->plural;
            if($t->update()){
                return redirect()->back()->with('success', 'Record added successfully');
            };
        } else {
            $tr = new Terminology();
            $tr->grade_class = $request->terminology_grade;
            $tr->plural = $request->plural;
            if($tr->save()){
                return redirect()->back()->with('success', 'Record added successfully');
            };
        }
        

        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

}
