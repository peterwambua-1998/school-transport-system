<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    public function driver()
    {
        return $this->belongsTo('App\Models\User','driver_id');
    }

    public function attendant()
    {
        return $this->belongsTo('App\Models\User','attendant_id');
    }


    public function route()
    {
        return $this->belongsTo('App\Models\Route');
    }

    public function student()
    {
        return $this->hasMany('App\Models\Student');
    }

    public function schooltrip()
    {
        return $this->hasMany('App\Models\SchoolTrip');
    }

    public function trips()
    {
        return $this->hasMany('App\Models\Trip');
    }
}
