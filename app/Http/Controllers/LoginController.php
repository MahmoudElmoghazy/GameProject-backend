<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        if(auth()->attempt($request->all())){
            $user = auth()->user();
            $token = $user->createToken("Login Token")->plainTextToken;
            return response()->json(['user' => UserCollection::make($user), 'token' => $token], 200);
        }else{
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset link sent to your email.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send reset link.'], 500);
        }
    }


    public function resetPassword(Request $request)
    {
        // Validate the token and email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $status = Password::reset(
            $request->only('email', 'token'),
            function ($user, $request) {
                $user->password = Hash::make($request->password);
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json(['error' => 'Invalid token or email.'], 400);
        }

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
