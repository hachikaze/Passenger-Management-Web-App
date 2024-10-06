<?php

namespace App\Http\Controllers;

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

        // Get today's date
        $today = Carbon::today();

        // Get the start of the current month
        $startOfMonth = Carbon::now()->startOfMonth();

        // Calculate Daily Passengers
        $dailyPassengers = Ridership::whereDate('created_at', $today)->count();

        // Get monthly ridership data and calculate month-to-date passengers for male and female
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        $monthlyData = Ridership::select(
            DB::raw("DATE(created_at) as date"),
            DB::raw('count(*) as total'),
            DB::raw('sum(case when gender = \'Male\' then 1 else 0 end) as male_count'),
            DB::raw('sum(case when gender = \'Female\' then 1 else 0 end) as female_count')
        )
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy(DB::raw("DATE(created_at)"))
        ->get();

        $monthToDatePassengers = 0;
        $monthToDateMale = 0;
        $monthToDateFemale = 0;

        $dailyData = [];

        foreach ($monthlyData as $data) {
            // Accumulate total passengers, male, and female passengers
            $monthToDatePassengers += $data->total;
            $monthToDateMale += $data->male_count;
            $monthToDateFemale += $data->female_count;

            // Store data for each day
            $dailyData[$data->date] = [
                'date' => $data->date,
                'ridership' => $data->total,
                'month_to_date' => $monthToDatePassengers,
                'male_passengers' => $data->male_count,
                'month_to_date_male' => $monthToDateMale,
                'female_passengers' => $data->female_count,
                'month_to_date_female' => $monthToDateFemale,
            ];
        }

        $totalMonthlyPassengers = Ridership::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $stationLabels = [
            'Pinagbuhatan', 'Kalawaan', 'San Joaquin', 'Guadalupe', 'Hulo', 
            'Valenzuela', 'Lambingan', 'Sta-Ana', 'PUP', 'Quinta', 
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

        // Count professions (Student vs Senior) within the current month
        $professionData = Ridership::select(
            DB::raw('profession'),
            DB::raw('count(*) as total')
        )
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->whereIn('profession', ['Student', 'Senior'])
        ->groupBy('profession')
        ->get()
        ->keyBy('profession');

        $studentsCount = $professionData->has('Student') ? $professionData->get('Student')->total : 0;
        $seniorsCount = $professionData->has('Senior') ? $professionData->get('Senior')->total : 0;
        $othersCount = $totalMonthlyPassengers - ($studentsCount + $seniorsCount);

        // Count of registered and guest passengers
        $guestPassengersCount = Ridership::whereRaw('is_guest = true')
            ->whereDate('created_at', Carbon::today())
            ->count();

        $registeredPassengersCount = Ridership::whereRaw('is_guest = false')
            ->whereDate('created_at', Carbon::today())
            ->count();


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
            'guestPassengersCount',  // Pass guest count to view
            'registeredPassengersCount'  // Pass registered count to view
        ));
    }
}
