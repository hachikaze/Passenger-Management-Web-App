<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StationSchedule;

class ScheduleController extends Controller
{
    public function index() {
        // Fetch distinct stations from the station_schedules table
        $stations = StationSchedule::select('station_name')->distinct()->get();

        return view('admin.schedules', compact('stations'));
    }

    public function getTimeRanges($station_name) {
        // Get all time ranges for the selected station, ordered by start_time
        $timeRanges = StationSchedule::where('station_name', $station_name)
                    ->orderBy('start_time', 'asc')
                    ->get();

        return response()->json($timeRanges);
    }

    public function updateTimeRange(Request $request) {
        // Update the time range for the selected station and time range ID
        StationSchedule::where('id', $request->time_range_id)
                        ->update([
                            'start_time' => $request->start_time,
                            'end_time' => $request->end_time
                        ]);

        return redirect()->back()->with('success', 'Time range updated successfully!');
    }
}
