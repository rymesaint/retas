<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'rymetutor@gmail.com',
            'password' => Hash::make('123456'),
            'email_verified_at' => Carbon::now(),
            'role' => 'owner'
        ]);
    }
}
