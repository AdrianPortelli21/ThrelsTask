<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CodeCheckController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
        ]);

       
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code); //get the row which contains the code

        $created_date = Carbon::createFromDate($passwordReset->created_at);
        
        if ($created_date->addMinute() <= now()) { //check that code doesn't expire
            $q = 'DELETE FROM reset_code_passwords where code = ?';
            DB::delete($q, [$request->code]);

            return response(['message' => 'Code is expire'], 422);
        }

        return response([
            'code' => $passwordReset->code,
            'message' => 'Code is Valid'
        ], 200);
    }
}
