<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*

        DB::table('users')->insert([
            'name' => 'Admin Deno',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'user_type' => 'admin',
            'phone_num' => '+25472134711',
            'staff_num' => 'ST121',
            'gender' => 'male',
            'password_changed' => 1,
        ]);
        
        
        DB::table('users')->insert([
            'name' => 'Parent Other',
            'email' => 'pOe@mail.com',
            'phone_num' => '+254723189792',
            'password' => Hash::make('12345678'),
            'user_type' => 'other',
            'gender' => 'female',
            'password_changed' => 1,
        ]);

        DB::table('users')->insert([
            'name' => 'Elias ',
            'staff_num' => 'ST20000',
            'email' => 'elias@mail.com',
            'phone_num' => '+25477924262',
            'password' => Hash::make('12345678'),
            'user_type' => 'attendant',
            'gender' => 'female',
            'password_changed' => 1,
        ]);

        DB::table('users')->insert([
            'name' => 'Teacher One',
            'staff_num' => 'ST124',
            'email' => 'teacher1@mail.com',
            'phone_num' => '+254723180213',
            'password' => Hash::make('12345678'),
            'user_type' => 'teacher',
            'gender' => 'male',
            'password_changed' => 1,
        ]);

        DB::table('users')->insert([
            'name' => 'Kenedy Oluoch',
            'staff_num' => 'ST123',
            'email' => 'ken@mail.com',
            'phone_num' => '+25472134711',
            'password' => Hash::make('12345678'),
            'user_type' => 'driver',
            'gender' => 'male',
            'password_changed' => 1,
        ]);

        DB::table('users')->insert([
            'name' => 'Rozy Mutuku',
            'staff_num' => 'ST991',
            'email' => 'rose@mail.com',
            'phone_num' => '+25477924262',
            'password' => Hash::make('12345678'),
            'user_type' => 'office staff',
            'gender' => 'female',
            'password_changed' => 1,
        ]);
        

        DB::table('users')->insert([
            'name' => 'Elias ',
            'staff_num' => 'ST124',
            'email' => 'eli@mail.com',
            'phone_num' => '+25477924262',
            'password' => Hash::make('12345678'),
            'user_type' => 'attendant',
            'gender' => 'female',
            'password_changed' => 1,
        ]);


        DB::table('users')->insert([
            'name' => 'Dan Shibweche',
            'staff_num' => 'ST8728',
            'email' => 'dan@mail.com',
            'phone_num' => '+25478462622',
            'password' => Hash::make('12345678'),
            'user_type' => 'driver',
            'gender' => 'male',
            'password_changed' => 1,
        ]);

        DB::table('users')->insert([
            'name' => 'Van Dem',
            'staff_num' => 'ST186',
            'email' => 'van@mail.com',
            'phone_num' => '+2547842728',
            'password' => Hash::make('12345678'),
            'user_type' => 'attendant',
            'gender' => 'male',
            'password_changed' => 1,
        ]);

        DB::table('users')->insert([
            'name' => 'Teacher Two',
            'staff_num' => 'ST127',
            'email' => 'teacher2@mail.com',
            'phone_num' => '+254723180213',
            'password' => Hash::make('12345678'),
            'user_type' => 'teacher',
            'gender' => 'female',
            'password_changed' => 1,
        ]);
        
        
        DB::table('users')->insert([
            'name' => 'Parent Two',
            'email' => 'pTwo@mail.com',
            'phone_num' => '+254723189792',
            'password' => Hash::make('12345678'),
            'user_type' => 'parent',
            'gender' => 'female',
            'password_changed' => 1,
            'id_num' => '873421'
        ]);

        DB::table('users')->insert([
            'name' => 'Teacher Three',
            'staff_num' => 'ST128',
            'email' => 'teacher3@mail.com',
            'phone_num' => '+254723189733',
            'password' => Hash::make('12345678'),
            'user_type' => 'teacher',
            'gender' => 'female',
            'password_changed' => 1,
        ]);
        
        DB::table('users')->insert([
            'name' => 'Teacher Two',
            'staff_num' => 'ST127',
            'email' => 'teacher2@mail.com',
            'phone_num' => '+254723180213',
            'password' => Hash::make('12345678'),
            'user_type' => 'teacher',
            'gender' => 'female',
            'password_changed' => 1,
        ]);
        DB::table('users')->insert([
            'name' => 'Parent One',
            'email' => 'pOne@mail.com',
            'phone_num' => '+254723189792',
            'password' => Hash::make('12345678'),
            'user_type' => 'parent',
            'gender' => 'female',
            'password_changed' => 1,
        ]);
        DB::table('users')->insert([
            'name' => 'Dan Shibweche',
            'staff_num' => 'ST8728',
            'email' => 'dan@mail.com',
            'phone_num' => '+25478462622',
            'password' => Hash::make('12345678'),
            'user_type' => 'driver',
            'gender' => 'male',
            'password_changed' => 1,
        ]);
        DB::table('users')->insert([
            'name' => 'Fisher Wan',
            'staff_num' => 'ST8718',
            'email' => 'wan@mail.com',
            'phone_num' => '+25478462621',
            'password' => Hash::make('12345678'),
            'user_type' => 'driver',
            'gender' => 'male',
            'password_changed' => 1,
        ]);
        DB::table('users')->insert([
            'name' => 'San Pem',
            'staff_num' => 'ST180',
            'email' => 'san@mail.com',
            'phone_num' => '+2547843728',
            'password' => Hash::make('12345678'),
            'user_type' => 'attendant',
            'gender' => 'male',
            'password_changed' => 1,
        ]);

        DB::table('users')->insert([
            'name' => 'Ran Man',
            'staff_num' => 'ST1930',
            'email' => 'ran@mail.com',
            'phone_num' => '+254784372809',
            'password' => Hash::make('12345678'),
            'user_type' => 'attendant',
            'gender' => 'male',
            'password_changed' => 1,
        ]);
        DB::table('users')->insert([
            'name' => 'Parent Two',
            'email' => 'pTwo@mail.com',
            'phone_num' => '+254723189792',
            'password' => Hash::make('12345678'),
            'user_type' => 'parent',
            'gender' => 'female',
            'password_changed' => 1,
            'id_num' => '873421'
        ]);
        */
        DB::table('users')->insert([
            'name' => 'Fisher Wan',
            'staff_num' => 'ST8718',
            'email' => 'wan@mail.com',
            'phone_num' => '+25478462621',
            'password' => Hash::make('12345678'),
            'user_type' => 'driver',
            'gender' => 'male',
            'password_changed' => 1,
        ]);
        DB::table('users')->insert([
            'name' => 'San Pem',
            'staff_num' => 'ST180',
            'email' => 'san@mail.com',
            'phone_num' => '+2547843728',
            'password' => Hash::make('12345678'),
            'user_type' => 'attendant',
            'gender' => 'male',
            'password_changed' => 1,
        ]);
    }
}