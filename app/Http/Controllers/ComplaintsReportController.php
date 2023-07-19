<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ComplaintsReportController extends Controller
{
    /**
     * parents complaints
     */
    public function parentComplaints()
    {
        $parents = User::where('user_type','=','parent')->where('status','=', 1)->get();
        foreach ($parents as $parent) {
            $incidents = Incident::where('user_id','=', $parent->id)->get();
            $parent->complaints = $incidents;
        }
        return response($parents);
    }

    /**
     * attendants complaints
     */
    public function attendantComplaints()
    {
        $attendants = User::where('user_type','=','attendant')->where('status','=', 1)->get();
        foreach ($attendants as $attendant) {
            $incidents = Incident::where('user_id','=', $attendant->id)->get();
            $attendant->complaints = $incidents;
        }
        return response($attendants);
    }
}
