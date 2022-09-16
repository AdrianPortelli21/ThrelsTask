<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResetCodePassword;
use App\Models\User;

class CodeCheckController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
        ]);

       
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code); //get the row which contains the code

        
        if ($passwordReset->created_at > now()->addHour()) { //check that code doesn't expire in 1 hour
            $passwordReset->delete();
            return response(['message' => 'Code is expire'], 422);
        }

        return response([
            'code' => $passwordReset->code,
            'message' => 'Code is Valid'
        ], 200);
    }
}
