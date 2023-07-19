<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->hasOne('App\Models\Vehicle');
    }

    public function license()
    {
        return $this->hasOne('App\Models\DriverLicence', 'driver_id');
    }

    public function student()
    {
        return $this->hasMany('App\Models\Student', 'parent_id');
    }

    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission')->withTimestamps();
    }

    public function schooltrip()
    {
        return $this->hasMany('App\Models\SchoolTrip');
    }

    public function generateToken()
    {
        $random = rand(100000, 999999);
        $this->timestamps = false;
        $this->rand_number = $random;
        $this->expire_at = now()->addMinute(2);
        $this->save();

        return $random;
    }

    public function resetToken()
    {
        $this->timestamps = false;
        $this->rand_number = null;
        $this->expire_at = null;
        $this->save();
    }
}
