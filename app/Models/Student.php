<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    public function vehicle()
    {
        return $this->belongsTo('App\Models\Vehicle');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function attendance()
    {
        return $this->hasMany('App\Models\Attendance');
    }

    public function trip()
    {
        return $this->belongsTo('App\Models\Trip');
    }

    public function depature()
    {
        return $this->hasMany('App\Models\DepatureChecklist');
    }

    public function return()
    {
        return $this->hasMany('App\Models\ReturnChecklist');
    }

    public function flagoff()
    {
        return $this->hasMany('App\Models\FlagOff');
    }

    public function review()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function fees()
    {
        return $this->hasMany('App\Models\SchoolFees','student');
    }
}
