<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupPoint extends Model
{
    use HasFactory;

    public function PickupPointStudent()
    {
        return $this->belongsTo(PickupPointStudent::class, 'pickuppoint_id');
    }
}
