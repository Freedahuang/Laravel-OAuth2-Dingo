<?php

use Illuminate\Database\Seeder;
use \App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        User::create([
            'email' => 'jack@21bit.cn',
            'nickname' => 'Jack',
            'username' => 'jack',
            'telephone' => '15811118032',
            'password' => 'password',
            'avatar' => '/userdata/avatar/jack.png',
            'status' => 1,
            'type' => 1
        ]);
    }
}
