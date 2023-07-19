<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolTrip extends Model
{
    use HasFactory;

    public function teacher()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function vehicle()
    {
        return $this->belongsTo('App\Models\Vehicle');
    }

    public function school_trip_grades() {
        return $this->hasMany('App\Models\SchoolTripGrade', 'schooltrip_id');
    }
}
