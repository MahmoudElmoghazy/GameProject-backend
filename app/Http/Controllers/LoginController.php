<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        if(auth()->attempt($request->all())){
            $user = auth()->user();
            $token = $user->createToken("Login Token")->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token], 200);
        }else{
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
