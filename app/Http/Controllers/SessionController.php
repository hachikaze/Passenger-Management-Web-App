<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\ActivityLog;

class SessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse|View
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid email or password.'
            ]);
        }

        if ($request->user()->user_type === 'Aide') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'The account you are trying to access is for the mobile app only.'
            ]);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'assigned_station' => $request->user()->assigned_station,
            'login_date' => Carbon::now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request->session()->regenerate();

        if (! $request->user()->hasVerifiedEmail()) {
            return view('auth.verify-email');
        }

        return redirect()->route('dashboard');
    }

    public function destroy()
    {
        Auth::logout();

        // Redirect to login instead of '/'
        return redirect()->route('login');
    }

    public function showProfile()
    {
        $user = Auth::user();

        return view('profile', ['user' => $user]);
    }

    public function updateProfile(User $user, Request $request)
    {
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required']
        ]);

        $user = Auth::user();
    
        $user->update([
            'first_name' => request('first_name'),
            'middle_name' => request('middle_name'),
            'last_name' => request('last_name'),
        ]);
    
        // redirect
        return redirect()->route('profile')->with('success', 'Profile information update successfully.');
    }

    public function updateContact(User $user, Request $request)
    {
        $request->validate([
            'contact_number' => ['required', 'regex:/^09[0-9]{9}$/', 'min:11', 'max:11', 'unique:users,contact_number'],
        ]);

        $user = Auth::user();
    
        $user->update([
            'contact_number' => request('contact_number')
        ]);
    
        // redirect
        return redirect()->route('profile')->with('success', 'Contact information update successfully.');
    }
}
