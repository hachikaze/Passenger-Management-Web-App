<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\StationSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Fetch users with pagination
        $users = User::paginate(10);
        $stations = StationSchedule::distinct()->pluck('station_name');

        // Define offline threshold (in minutes)
        $offlineThreshold = 5;

        // Fetch sessions and check last activity
        $sessions = DB::table('sessions')
                    ->whereNotNull('user_id')
                    ->get()
                    ->map(function ($session) use ($offlineThreshold) {
                        $session->is_online = Carbon::now()->diffInMinutes(Carbon::createFromTimestamp($session->last_activity)) < $offlineThreshold;
                        return $session;
                    });

        // Check if a date is selected for filtering, otherwise use today's date
        $filterDate = $request->input('date', Carbon::today()->toDateString());

        // Fetch activity logs for the selected date
        $activity_logs = ActivityLog::whereDate('login_date', $filterDate)->paginate(10);

        return view('admin.users', compact('users', 'stations', 'activity_logs', 'filterDate', 'sessions'));
    }

    public function update(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_type' => 'required|string',
        ]);

        // Find the user by ID and update the user_type
        $user = User::find($request->id);
        if ($user) {
            $user->user_type = $request->user_type;
            $user->save();

            return redirect()->route('users.index')->with('success', 'User type updated successfully.');
        }
    }

    public function updateAssignedStation(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'id' => 'required|exists:users,id',
            'assigned_station' => 'required|string|max:255',
        ]);

        // Find the user by ID
        $user = User::find($request->id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Update the user's assigned station
        $user->assigned_station = $request->assigned_station;
        $user->save();

        if ($user->save()) {
            return redirect()->back()->with('success', 'Assigned station updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update assigned station.');
        }
    }
    
    public function destroy(User $user)
    {
        if ($user) {
            // The activity logs should be deleted automatically due to cascade delete
            $user->delete();

            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        }

        return redirect()->route('users.index')->with('error', 'User not found.');
    }
}
