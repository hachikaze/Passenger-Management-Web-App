<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Boat;
use App\Models\PassengerManifest;
use App\Models\RegisteredPassenger;
use App\Models\Ridership;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRegisteredPassenger = RegisteredPassenger::count();

        // Get all boats and count operational boats
        $boats = Boat::all();
        $operationalBoats = Boat::where('status', 'ACTIVE')->count();

        // Get today's date and the start of the current month
        $today = Carbon::today()->format('Y-m-d');
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        $day = Carbon::now()->day;  // Get the current day number

        // Calculate daily passengers for today
        $dailyPassengers = Ridership::whereDate('created_at', $today)->count();

        // Get month-to-date passenger count and gender breakdown
        $monthToDatePassengers = Ridership::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $monthToDateMale = Ridership::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('gender', 'Male')
            ->count();

        $monthToDateFemale = Ridership::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('gender', 'Female')
            ->count();

        // Get today's male and female passenger counts
        $todayMaleCount = Ridership::whereDate('created_at', $today)
            ->where('gender', 'Male')
            ->count();

        $todayFemaleCount = Ridership::whereDate('created_at', $today)
            ->where('gender', 'Female')
            ->count();

        // Station breakdown for today
        $stationLabels = [
            'Pinagbuhatan', 'Kalawaan', 'San Joaquin', 'Guadalupe', 'Hulo', 
            'Valenzuela', 'Lambingan', 'Sta.Ana', 'PUP', 'Quinta', 
            'Lawton', 'Escolta'
        ];

        $stationRidershipData = Ridership::select('origin', DB::raw('count(*) as passenger_count'))
            ->whereDate('created_at', $today)
            ->groupBy('origin')
            ->get()
            ->keyBy('origin');  // Key the data by the station names

        $stationPassengerCounts = [];
        foreach ($stationLabels as $station) {
            $stationPassengerCounts[] = $stationRidershipData->has($station) 
                ? $stationRidershipData->get($station)->passenger_count 
                : 0;
        }

        // Count today's professions: Student vs Senior, and calculate "Others"
        $professionDataToday = Ridership::select(
            DB::raw('CASE 
                        WHEN LOWER(profession) LIKE \'%student%\' THEN \'Student\' 
                        WHEN LOWER(profession) LIKE \'%senior%\' THEN \'Senior\'
                        ELSE \'Others\' 
                    END as profession_category'),
            DB::raw('count(*) as total')
        )
        ->whereDate('created_at', $today)
        ->groupBy('profession_category')
        ->get()
        ->keyBy('profession_category');

        $studentsCount = $professionDataToday->has('Student') ? $professionDataToday->get('Student')->total : 0;
        $seniorsCount = $professionDataToday->has('Senior') ? $professionDataToday->get('Senior')->total : 0;
        $othersCount = $dailyPassengers - ($studentsCount + $seniorsCount);

        // Count of today's registered and guest passengers
        $guestPassengersCount = Ridership::whereRaw('is_guest = true')
            ->whereDate('created_at', $today)
            ->count();

        $registeredPassengersCount = Ridership::whereRaw('is_guest = false')
            ->whereDate('created_at', $today)
            ->count();

        // Calculate the number of stations based on user logins
        $stationsByDay = ActivityLog::whereYear('login_date', $year)
            ->whereMonth('login_date', $month)
            ->where('assigned_station', '!=', 'None')
            ->select(DB::raw('EXTRACT(DAY FROM login_date) as day'), DB::raw('count(DISTINCT assigned_station) as count'))
            ->groupBy('day')
            ->pluck('count', 'day');
        
        // Summing the count of distinct stations for today
        $activeStationsToday = $stationsByDay[$day] ?? 0;  // Use $day instead of $today->day

        return view('dashboard', compact(
            'boats',
            'operationalBoats',
            'dailyPassengers',
            'monthToDatePassengers',
            'monthToDateMale',
            'monthToDateFemale',
            'stationLabels',
            'stationPassengerCounts',
            'studentsCount',
            'seniorsCount',
            'othersCount',
            'totalRegisteredPassenger',
            'guestPassengersCount',
            'registeredPassengersCount',
            'todayMaleCount',
            'todayFemaleCount',
            'activeStationsToday'  // Add the active stations to the view
        ));
    }
}
