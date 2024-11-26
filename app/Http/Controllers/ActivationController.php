<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ActivationController extends Controller
{
    public function activate($token): RedirectResponse
    {
        $user = User::where('activation_token', $token)->first();

        if ($user) {
            $user->is_active = true;
            $user->activation_token = null;
            $user->save();

            return redirect()->route('login')->with('status', 'Account activated successfully!');
        }

        return redirect()->route('home')->with('error', 'Invalid activation token.');
    }

}

