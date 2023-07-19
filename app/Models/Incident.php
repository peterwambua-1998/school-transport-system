<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    public function incidentImages()
    {
        return $this->belongsTo('App\Models\IncidentImages','incident_id');
    }
}
