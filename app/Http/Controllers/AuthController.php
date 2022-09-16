<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\SessionGuard;

class AuthController extends Controller
{
    public $loginAfterSignUp = true;

    public function register(Request $request)
    {
       
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if(User::where('email', 'LIKE', '%'.$request->email.'%')->count() >= 1){

            return response()->json(['error' => 'See other - User Already Created'], 303);

        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized Access'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function getAuthUser(Request $request)
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message'=>'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
      return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory('api')->getTTL() * 60
      ]);
    }
}
