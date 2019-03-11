<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'=>'Collin',
            'email'=>'njugushtosh@gmail.com',
            'password'=>bcrypt('123456'),
            'admin'=>1,
            'remember_token'=>str_random(10),
        ]);
    }
}
