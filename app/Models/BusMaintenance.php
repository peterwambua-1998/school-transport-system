<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusMaintenance extends Model
{
    use HasFactory;

    public function busmaintenanceimages()
    {
        return $this->hasMany(BusMaintenanceImage::class, 'bus_maintenance_id');
    }
}
