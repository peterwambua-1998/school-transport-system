<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    public function claims() 
    {
        return $this->hasMany(InspectionClaim::class, 'inspection_id');
    }
}
