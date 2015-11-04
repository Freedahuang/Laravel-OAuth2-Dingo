<?php
namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;

class PasswordGrantVerifier
{
    public function verify($username, $password){
        $telephoneCredentials = [
            'telephone'    => $username,
            'password' => $password,
        ];
        $usernameCredentials = [
            'username'    => $username,
            'password' => $password,
        ];
        $emailCredentials = [
            'email'    => $username,
            'password' => $password,
        ];

        if (Auth::once($telephoneCredentials)||Auth::once($usernameCredentials)||Auth::once($emailCredentials)) {
            return Auth::user()->id;
        }

        return false;
    }


    static public function verifyTelephone($telephone){
        return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$telephone);
    }
}