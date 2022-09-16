<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResetCodePassword;
use App\Mail\SendCodeResetPassword;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        //remove any old codes which allocated to the requested email. 
        ResetCodePassword::where('email', $request->email)->delete();

        
        $data['code'] = mt_rand(100000, 999999); // produce an new random code

        
        $codeData = ResetCodePassword::create($data); // create a new database entry

        
        Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code)); // send code via mail

        return response(['message' =>'Code has been sent',
        'code' => 'Code is being sent via smtp(mailtrap) for testing purposes find the code here: '.$codeData->code], 200);
    }
}
