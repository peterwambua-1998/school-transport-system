<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'role' => 'admin',
            'role_name' => 'Admin'
        ]);

        DB::table('roles')->insert([
            'role' => 'director',
            'role_name' => 'Director'
        ]);

        DB::table('roles')->insert([
            'role' => 'head teacher',
            'role_name' => 'Head Teacher'
        ]);

        DB::table('roles')->insert([
            'role' => 'supervisor',
            'role_name' => 'Supervisor'
        ]);

        DB::table('roles')->insert([
            'role' => 'office staff',
            'role_name' => 'Office Staff'
        ]);

        DB::table('roles')->insert([
            'role' => 'driver',
            'role_name' => 'Driver'
        ]);

        DB::table('roles')->insert([
            'role' => 'attendant',
            'role_name' => 'Attendant',
        ]);

        DB::table('roles')->insert([
            'role' => 'teacher',
            'role_name' => 'Teacher',
        ]);
    }
}
