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
            'email'=>'collin.gitonga@gmail.com',
            'password'=>bcrypt('123456'),
            'email_verified_at' => now(),
            //'admin'=>1,
            'remember_token'=>bin2hex(random_bytes(10)),
        ]);
    }
}
