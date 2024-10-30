<?php

namespace App\Http\Controllers;

use App\Models\Boat;
use App\Models\BoatStatusLog;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoatController extends Controller
{
    public function index(Request $request)
    {
        // Get today's date and the last status update date from settings
        $lastUpdateDate = Carbon::parse(
            Setting::where('key', 'last_boat_status_update')->value('value')
        )->format('Y-m-d');
        $today = now()->format('Y-m-d');

        // Check if the status has already been updated today
        if ($lastUpdateDate !== $today) {
            // Find all active boats except those under maintenance
            Boat::where('status', 'ACTIVE')
                ->whereNot('status', 'MAINTENANCE')
                ->update(['status' => 'INACTIVE']);

            // Update the last status update date in the settings
            Setting::updateOrCreate(
                ['key' => 'last_boat_status_update'],
                ['value' => now()->format('Y-m-d')] // Store only the date
            );
        }

        // Retrieve input parameters for year and month (defaults to current year/month)
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // Get total number of boats
        $totalBoats = Boat::count();

        // Get today's date to check for past and present days only
        $today = now();

        // Retrieve boat status counts by day for the given month and year
        $boatCountsByDay = BoatStatusLog::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->select(
                DB::raw("EXTRACT(DAY FROM date) as day"),
                DB::raw("SUM(CASE WHEN status = 'ACTIVE' THEN 1 ELSE 0 END) as active_count"),
                DB::raw("SUM(CASE WHEN status = 'MAINTENANCE' THEN 1 ELSE 0 END) as maintenance_count")
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Prepare data for each day of the month
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $dailyData = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($year, $month, $day);
            $statusCounts = $boatCountsByDay->firstWhere('day', $day);

            // Assign values from the query or set them to 0 if not found
            $activeCount = $statusCounts->active_count ?? 0;
            $maintenanceCount = $statusCounts->maintenance_count ?? 0;

            // Set counts to 0 for future days
            if ($currentDate->greaterThan($today)) {
                $activeCount = 0;
                $maintenanceCount = 0;
            }

            $inactiveCount = $totalBoats - $activeCount - $maintenanceCount;

            // Store the data for this day
            $dailyData[] = [
                'day' => $day,
                'active_count' => $activeCount,
                'inactive_count' => $inactiveCount,
                'maintenance_count' => $maintenanceCount,
            ];
        }

        // Sorting logic
        $sort = $request->input('sort', 'date'); // Default sort by date
        $direction = $request->input('direction', 'asc'); // Default sort direction is ascending

        // Retrieve sorted boat status logs
        $statusLogs = BoatStatusLog::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy($sort, $direction)
            ->get();

        // Fetch all boats to display in the view
        $boats = Boat::all();

        return view('boats', compact('dailyData', 'year', 'month', 'boats', 'statusLogs'));
    }

    public function updateStatus(Request $request)
    {
        $boat = Boat::find($request->id);
        if ($boat) {
            $boat->status = $request->status;
            $boat->save();

            BoatStatusLog::create([
                'boat_id' => $boat->id,
                'status' => $request->status,  // Use $request->status instead
                'date' => Carbon::now()->format('Y-m-d'),
            ]);

            return redirect()->route('boats')->with('success', 'Status updated successfully.');
        }

        return redirect()->route('boats')->with('error', 'Boat not found.');
    }

    public function addBoat(Request $request)
    {
        $attributes = $request->validate([
            'boat_name' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value !== strtoupper($value)) {
                        $fail('The BOAT NAME must be in all capital letters.');
                    }
                }
            ],
            'max_capacity' => ['required', 'integer'],
            'status' => ['required', 'in:ACTIVE,NOT ACTIVE,UNDER REPAIR'],
        ]);

        // Ensure 'in_use' is explicitly set to boolean false
        $attributes['in_use'] = 'false';

        // Initialize 'occupied_seats' to 0 and 'available_seats' to max_capacity
        $attributes['occupied_seats'] = 0;
        $attributes['available_seats'] = $attributes['max_capacity'];

        // Save the boat with the provided and modified attributes
        $boat = new Boat($attributes);
        $boat->save();

        return redirect()->route('boats')->with('success', 'Boat added successfully.');
    }

    public function deleteBoat(Boat $boat)
    {
        $boat->delete();

        return redirect()->route('boats')->with('success', 'Boat deleted successfully.');
    }
}
