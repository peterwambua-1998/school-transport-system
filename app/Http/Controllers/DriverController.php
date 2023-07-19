<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\Student;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneratedPassword;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Illuminate\Support\Facades\Crypt;

class DriverController extends Controller
{

    /**
     * for driver login to see his/her students
     */
    public function myStudents()
    {
        $driver = Auth::user()->id;

        $vehicle = Vehicle::where('driver_id', '=', $driver)->first();

        if (! $vehicle) {
            return redirect()->back()->with('unsuccess', 'driver is not assigned vehicle');
        }

        $students = Student::where('vehicle_id', '=', $vehicle->id)->get();

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('drivers.mystudents')->with([
            'students' => $students,
            'notifications' => $notifications
        ]);
    }
}
