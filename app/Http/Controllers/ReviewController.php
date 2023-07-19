<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Review;
use Attribute;
use Illuminate\Http\Request;

class ReviewController extends Controller
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
        $attendance = Attendance::where('student_id', '=', $request->student_id)->get()->last();
        
        $review = new Review();
        $review->user_id = $request->user_id;
        $review->student_id = $request->student_id;
        $review->attendance_id = $attendance->id;
        $review->rating = $request->rating;
        $review->feedback = $request->feedback;

        if ($review->save()) {
            return response('review saved');
        } else {
            return response('system error please try again');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $reviews = Review::orderBy('created_at', 'ASC')->get();
       
        return view('reviews.show')->with('reviews',$reviews);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        //
    }
}
