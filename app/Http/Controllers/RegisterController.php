<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Mail\ActivationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        //create user add random code that is unique for activation token and return success message
        try {
            $user = new User();
            $user->fill($request->all());
            $user->password =  Hash::make($request->password);
            do {
                $token = Str::random(60);
            } while (User::where('activation_token', $token)->exists());

            $user->avatar = $request->file('avatar')->store('avatars');
            $user->save();
/*            Mail::to($user->email)->send(new ActivationEmail(route('activation.verify', ['token' => $user->activation_token])));*/
            return response()->json(['message' => 'User created successfully'], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => 'User not created'], 500);
        }
    }
}
