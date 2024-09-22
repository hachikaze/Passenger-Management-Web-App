<?php

namespace App\Http\Controllers;

use App\Models\StationSchedule;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create() {
        
        $stations = StationSchedule::distinct()->pluck('station_name');

        return view('auth.register', compact('stations'));
    }

    public function store(Request $request) {
        // validate
        $attributes = $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(6), 'confirmed'],
            'user_type' => ['required'],
            'assigned_station' => ['required']
        ]);

        // create the user
        User::create($attributes);

        // redirect
        return redirect()->route('register')->with('success', 'User added successfully.');
    }
}