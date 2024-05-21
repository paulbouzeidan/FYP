<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UsersTableSeeder  extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'user_name' => 'Paul',
            'user_lastname' => 'abz',
            'email' => 'Paulabz@gmail.com',
            'birthday' => '2004-10-21',
            'phone_number' => '71717171',
            'password' => bcrypt('Qwerty1234'), // Use Laravel's bcrypt function to hash the password
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'user_name' => 'Sergio',
            'user_lastname' => 'berberian',
            'email' => 'sergioberberian2001@gmail.com',
            'birthday' => '2001-04-09',
            'phone_number' => '81384086',
            'password' => bcrypt('Qwerty1234'), // Use Laravel's bcrypt function to hash the password
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // You can add more dummy data as needed
}
}
