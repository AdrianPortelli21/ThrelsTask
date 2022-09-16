<?php

namespace App\Http\Controllers;

use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6|confirmed',
        ]);

        //get the row which contains the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        if ($passwordReset->created_at > now()->addHour()) { //check that code doesn't expire in 1 hour
            $passwordReset->delete();
            return response(['message' => 'Code is expire'], 422);
        }

        // get the user who requested the password reset
        $user = User::firstWhere('email', $passwordReset->email);

       

        // Hash the password and update user table
        $user->update([ 'password'=> Hash::make(strval($request->password))]);

       

        //remove the row which contents the code used
        $q = 'DELETE FROM reset_code_passwords where code = ?';
        DB::delete($q, [$request->code]);

        return response(['message' =>'Password has been successfully reset'], 200);
    }
}
