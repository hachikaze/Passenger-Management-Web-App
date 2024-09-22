<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request)
    {
        // Validate the request data
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:6', 'confirmed']
        ]);

        $user = Auth::user();

        // Check if the provided current password matches the user's actual current password
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return redirect()->route('profile')->with('error', 'Invalid current password.');
        }

        // Update the user's password
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('profile')->with('success', 'Password updated successfully.');
    }
}
