<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    public function vehicle()
    {
        return $this->hasOne('App\Models\Vehicle');
    }

    public function path()
    {
        return $this->hasOne('App\Models\RoutePolyline');
    }


    public function zone()
    {
        return $this->belongsTo('App\Models\Zone');
    }
}
