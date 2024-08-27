<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',[
            'except'=> ['login','refresh','logout']
        ]);
    }

    public function login(Request $request){

        $this-> validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email','password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message'=>'Unauthorized'],401);
        }

        return $this->responsdWithToken($token);
    }

    public function me() {
        return response()->json(auth()->user());
    }

    public function logout()  {
        auth()->logout();

        return response()->json(['message'=>'successfully logged out']);
    }

    public function refresh()  {
        return $this->responsdWithToken(auth()->refresh());
    }

    protected function responsdWithToken($token){
        
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'user'=>auth()->user(),
            'expires_in'=> auth()->factory()->getTTL()
        ]);
    }
}