<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Boat;
use App\Models\BoatStatusLog;
use App\Models\StationSchedule;
use App\Models\PassengerManifest;
use App\Models\Ridership;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;


class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Default year and month
        $year = $request->input('year', Carbon::now()->year);  // Default to current year
        $month = $request->input('month', Carbon::now()->month); // Default to current month
        $selectedDate = $request->input('date', Carbon::now()->format('Y-m-d'));  // Default to current date

        // Fetch the boats and their passenger data for the selected date
        $boats = Boat::leftJoin('ridership', function ($join) use ($selectedDate) {
            $join->on('boats.id', '=', 'ridership.boat_id')
                ->whereDate('ridership.created_at', $selectedDate); // Filter by selected date
        })
        ->select(
            'boats.id', 
            'boats.boat_name', 
            DB::raw('COUNT(ridership.id) as total_passengers'),
            DB::raw('MAX(ridership.created_at) as last_trip_date')
        )
        ->groupBy('boats.id', 'boats.boat_name')
        ->get();

        // List of stations to display in the report
        $stations = [
            'Guadalupe', 'Hulo', 'Valenzuela', 'Lambingan', 
            'Sta.Ana', 'PUP', 'Quinta', 'Lawton', 'Escolta', 
            'Pinagbuhatan', 'San Joaquin', 'Kalawaan', 'Maybunga'
        ];

        // Initialize station data with default values
        $stationData = [];
        foreach ($stations as $station) {
            $stationData[$station] = [
                'total_manifest' => 0,
                'regular' => 0,
                'student' => 0,
                'senior' => 0,
                'pwd' => 0,
                'ticket_sold' => ' ',
                'free_ride' => ' ',
                'cash_collected' => ' ',
                'vessel_trip' => ' ',
            ];
        }

        // Get ridership data for the selected date, grouped by station
        $stationRidershipData  = Ridership::select(
            'origin',
            DB::raw('COUNT(*) as total_manifest'),
            DB::raw("SUM(CASE WHEN LOWER(profession) NOT LIKE '%student%' AND LOWER(profession) NOT LIKE '%senior%' AND LOWER(profession) NOT LIKE '%pwd%' THEN 1 ELSE 0 END) as regular"),
            DB::raw("SUM(CASE WHEN LOWER(profession) LIKE '%student%' THEN 1 ELSE 0 END) as student"),
            DB::raw("SUM(CASE WHEN LOWER(profession) LIKE '%senior%' THEN 1 ELSE 0 END) as senior"),
            DB::raw("SUM(CASE WHEN LOWER(profession) LIKE '%pwd%' THEN 1 ELSE 0 END) as pwd")
        )
        ->whereDate('created_at', $selectedDate)  // Filter by selected date
        ->groupBy('origin')
        ->get();

        // Map ridership data to stations
        foreach ($stationRidershipData as $data) {
            if (isset($stationData[$data->origin])) {
                $stationData[$data->origin]['total_manifest'] = $data->total_manifest;
                $stationData[$data->origin]['regular'] = $data->regular;
                $stationData[$data->origin]['student'] = $data->student;
                $stationData[$data->origin]['senior'] = $data->senior;
                $stationData[$data->origin]['pwd'] = $data->pwd;
            }
        }

        // Get all the ridership data for the current year, grouped by month
        $monthlyRidershipData = Ridership::select(
            DB::raw("TO_CHAR(created_at, 'MM') as month"),
            DB::raw('count(*) as total'),
            DB::raw("sum(case when LOWER(profession) LIKE '%student%' then 1 else 0 end) as student_count"),  // Case-insensitive match for 'student'
            DB::raw("sum(case when LOWER(profession) LIKE '%senior%' then 1 else 0 end) as senior_count"),   // Case-insensitive match for 'senior' (includes 'senior citizen')
            DB::raw("sum(case when gender = 'Male' then 1 else 0 end) as male_count"),
            DB::raw("sum(case when gender = 'Female' then 1 else 0 end) as female_count")
        )
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->get();

        $yearlyRidership = Ridership::select(
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('count(*) as total'),
            DB::raw("sum(case when LOWER(profession) LIKE '%student%' then 1 else 0 end) as student_count"),
            DB::raw("sum(case when LOWER(profession) LIKE '%senior%' then 1 else 0 end) as senior_count"),
            DB::raw("sum(case when gender = 'Male' then 1 else 0 end) as male_count"),
            DB::raw("sum(case when gender = 'Female' then 1 else 0 end) as female_count")
        )
        ->groupBy(DB::raw('EXTRACT(YEAR FROM created_at)'))
        ->orderBy(DB::raw('EXTRACT(YEAR FROM created_at)'), 'asc')
        ->get();
    
        // Extract the data for the chart (years and totals)
        $years = $yearlyRidership->pluck('year');
        $totals = $yearlyRidership->pluck('total');

        $monthlyData = [];
        $ridershipData = [];

        // Initialize all months with zero values
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = [
                'month' => Carbon::create(null, $i, 1)->format('F'),  // Convert month number to full month name
                'ridership' => 0,
                'student' => 0,
                'senior' => 0,
                'male' => 0,
                'female' => 0,
            ];
        }

        foreach ($monthlyRidershipData as $data) {
            $monthNumber = ltrim($data->month, '0');
            $monthlyData[$monthNumber] = [
                'month' => Carbon::create(null, $monthNumber, 1)->format('F'),
                'ridership' => $data->total,
                'student' => $data->student_count,
                'senior' => $data->senior_count,
                'male' => $data->male_count,
                'female' => $data->female_count,
            ];
            $ridershipData[$monthNumber] = $data->total;
        }
    
        $ridershipData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthKey = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ridershipData[] = $monthlyRidershipData->where('month', $monthKey)->first()->total ?? 0;
        }
    
        ksort($ridershipData);
        ksort($monthlyData);

        // Get the current date to limit the `month_to_date` calculation
        $currentDate = Carbon::now()->day;

        // Get daily ridership and operational data for the current month
        $dailyRidershipData = Ridership::select(
            DB::raw("to_char(created_at, 'DD') as day"),
            DB::raw('count(*) as total'),
            DB::raw("sum(case when gender = 'Male' then 1 else 0 end) as male_count"),
            DB::raw("sum(case when gender = 'Female' then 1 else 0 end) as female_count")
        )
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy('day')
        ->orderBy(DB::raw("to_char(created_at, 'DD')"), 'asc') // Ensure data is ordered by day in ascending order
        ->get();

        // Retrieve active boats with their updated_at dates for the current month and year
        $activeBoatsByDay = BoatStatusLog::where('status', 'ACTIVE')
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->select(DB::raw('EXTRACT(DAY FROM date) as day'), DB::raw('count(*) as count'))
        ->groupBy('day')
        ->pluck('count', 'day');

        $stationsByDay = ActivityLog::whereYear('login_date', $year)
            ->whereMonth('login_date', $month)
            ->where('assigned_station', '!=', 'None')
            ->select(DB::raw('EXTRACT(DAY FROM login_date) as day'), DB::raw('count(DISTINCT assigned_station) as count'))
            ->groupBy('day')
            ->pluck('count', 'day');

        // Initialize daily ridership data
        $dailyData = [];
        $cumulativeTotal = 0; // Ensure this is initialized correctly

        // Build daily data and calculate cumulative total for each day
        foreach ($dailyRidershipData as $data) {
            $day = (int) $data->day;

            // Accumulate the total if ridership exists
            if ($data->total > 0) {
                $cumulativeTotal += $data->total;
            }

            $activeBoatsCount = $activeBoatsByDay[$day] ?? 0;
            $stationsCount = $stationsByDay[$day] ?? 0;

            // Populate the daily data for the given day
            $dailyData[$day] = [
                'date' => $day,
                'ridership' => $data->total,
                'boats' => $activeBoatsCount,
                // Always display cumulative total if ridership exists
                'month_to_date' => $cumulativeTotal,
                'stations' => $stationsCount,
                'male_passengers' => $data->male_count,
                'female_passengers' => $data->female_count,
            ];
        }

        ksort($dailyData);

        // Fill in missing days in the month
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            if (!isset($dailyData[$day])) {
                $activeBoatsCount = $activeBoatsByDay[$day] ?? 0;
                $stationsCount = $stationsByDay[$day] ?? 0;

                // Set month_to_date to 0 for missing days with no ridership
                $dailyData[$day] = [
                    'date' => $day,
                    'ridership' => 0,
                    'boats' => $activeBoatsCount,
                    'month_to_date' => 0,
                    'stations' => $stationsCount,
                    'male_passengers' => 0,
                    'female_passengers' => 0,
                ];
            }
        }

        ksort($dailyData);

        // Pagination for manifests is already handled above
        $passengerCounts = [];

        $selectedDate = $request->input('date', Carbon::now()->format('Y-m-d'));

        // Station index mapping
        $stationIndexes = [
            'Pinagbuhatan' => 0,
            'Kalawaan' => 1,
            'San Joaquin' => 2,
            'Guadalupe' => 3,
            'Hulo' => 4,
            'Valenzuela' => 5,
            'Lambingan' => 6,
            'Sta.Ana' => 7,
            'PUP' => 8,
            'Quinta' => 9,
            'Lawton' => 10,
            'Escolta' => 11,
        ];

        // Fetch all station schedules with related passenger data for the selected date
        $stationSchedules = StationSchedule::with(['riderships' => function($query) use ($selectedDate) {
            $query->whereDate('created_at', $selectedDate)
                ->select('id', 'origin', 'destination', 'created_at');
        }])->get()->groupBy(['station_name', 'direction']);

        $passengerCounts = [];

        foreach ($stationSchedules as $station => $directions) {
            foreach ($directions as $direction => $schedules) {
                foreach ($schedules as $schedule) {
                    $count = $schedule->riderships->filter(function($ridership) use ($schedule, $stationIndexes) {
                        $createdAt = $ridership->created_at->format('H:i:s');

                        // Ensure that the schedule time matches
                        if ($createdAt >= $schedule->start_time && $createdAt <= $schedule->end_time) {

                            $originIndex = $stationIndexes[$ridership->origin] ?? null;
                            $destinationIndex = $stationIndexes[$ridership->destination] ?? null;

                            if ($originIndex !== null && $destinationIndex !== null) {
                                if ($originIndex > $destinationIndex && $schedule->direction === 'upstream') {
                                    return true;
                                } elseif ($originIndex < $destinationIndex && $schedule->direction === 'downstream') {
                                    return true;
                                }
                            }
                        }
                        return false;
                    })->count();

                    $passengerCounts[$station][$direction][] = [
                        'time_range' => "{$schedule->start_time} - {$schedule->end_time}",
                        'count' => $count,
                    ];
                }
            }
        }

        // Weekly report data
        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $previousWeekData = Ridership::whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])->count();
        $currentWeekData = Ridership::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count();

        $varianceQuantity = $currentWeekData - $previousWeekData;
        $variancePercentage = $previousWeekData > 0 ? ($varianceQuantity / $previousWeekData) * 100 : 0;

        $previousWeekDateRange = $previousWeekStart->format('M d') . ' - ' . $previousWeekEnd->format('M d, Y');
        $currentWeekDateRange = $currentWeekStart->format('M d') . ' - ' . $currentWeekEnd->format('M d, Y');

        $previousWeekBreakdown = Ridership::selectRaw('DATE(created_at) as date,
            COUNT(*) as ridership,
            SUM(CASE WHEN gender = \'male\' THEN 1 ELSE 0 END) as male,
            SUM(CASE WHEN gender = \'female\' THEN 1 ELSE 0 END) as female,
            SUM(CASE WHEN profession ILIKE \'%student%\' THEN 1 ELSE 0 END) as student,
            SUM(CASE WHEN profession ILIKE \'%senior citizen%\' THEN 1 ELSE 0 END) as senior,
            SUM(CASE WHEN profession NOT ILIKE \'%student%\' AND profession NOT ILIKE \'%senior citizen%\' THEN 1 ELSE 0 END) as other')
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $currentWeekBreakdown = Ridership::selectRaw('DATE(created_at) as date,
            COUNT(*) as ridership,
            SUM(CASE WHEN gender = \'male\' THEN 1 ELSE 0 END) as male,
            SUM(CASE WHEN gender = \'female\' THEN 1 ELSE 0 END) as female,
            SUM(CASE WHEN profession ILIKE \'%student%\' THEN 1 ELSE 0 END) as student,
            SUM(CASE WHEN profession ILIKE \'%senior citizen%\' THEN 1 ELSE 0 END) as senior,
            SUM(CASE WHEN profession NOT ILIKE \'%student%\' AND profession NOT ILIKE \'%senior citizen%\' THEN 1 ELSE 0 END) as other')
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Data for 2023
        $months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $ridership = [20244, 22921, 28282, 18983, 21361, 20723, 15048, 20302, 19056, 22230, 23607, 20671];

        // Perform linear regression
        $predictor = $this->linearRegression($months, $ridership);

        // Actual 2024 data as per the image
        $actual2024 = [
            1 => 22653, // January
            2 => 23012, // February
            3 => 20649, // March
            4 => 17329, // April
            5 => 17021, // May
            6 => 12705, // June
            7 => 0,     // July
            8 => 0,     // August
            9 => 0,     // September
            10 => 0,    // October
            11 => 0,    // November
            12 => 0     // December
        ];

        // Predict ridership for each month in 2024 and calculate errors for months with actual data
        $predictions = [];
        $absoluteErrors = [];
        $squaredErrors = [];

        foreach ($months as $month) {
            $predicted = round($predictor($month));
            $predictions[$month] = $predicted;

            // Calculate errors only if we have actual data for 2024
            if ($actual2024[$month] > 0) {
                $absoluteErrors[] = abs($actual2024[$month] - $predicted);
                $squaredErrors[] = pow($actual2024[$month] - $predicted, 2);
            }
        }

        // Calculate Mean Absolute Error (MAE) and Mean Squared Error (MSE) for months with actual data
        $mae = count($absoluteErrors) ? array_sum($absoluteErrors) / count($absoluteErrors) : 0;
        $mse = count($squaredErrors) ? array_sum($squaredErrors) / count($squaredErrors) : 0;

        // Calculate Mean Absolute Error (MAE) and Mean Squared Error (MSE)
        $mae = array_sum($absoluteErrors) / count($absoluteErrors);
        $mse = array_sum($squaredErrors) / count($squaredErrors);

        return view('admin.reports',compact(
            'predictions',
            'months',
            'ridership',
            'mae',
            'mse',
            'actual2024',
            'monthlyData', 
            'ridershipData', 
            'dailyData', 
            'passengerCounts',
            'selectedDate',
            'previousWeekDateRange', 
            'currentWeekDateRange',
            'previousWeekData', 
            'currentWeekData', 
            'varianceQuantity', 
            'variancePercentage', 
            'year', 
            'month',
            'yearlyRidership',
            'years',
            'totals',
            'stationData',
            'boats',
            'currentWeekBreakdown',
            'previousWeekBreakdown'
            )
        );
    }

    private function linearRegression($x, $y)
    {
        $n = count($x);
        $mean_x = array_sum($x) / $n;
        $mean_y = array_sum($y) / $n;

        $numerator = $denominator = 0;
        for ($i = 0; $i < $n; $i++) {
            $numerator += ($x[$i] - $mean_x) * ($y[$i] - $mean_y);
            $denominator += pow($x[$i] - $mean_x, 2);
        }

        $slope = $numerator / $denominator;
        $intercept = $mean_y - $slope * $mean_x;

        // Return a closure that predicts the ridership based on the month
        return function($x) use ($slope, $intercept) {
            return $slope * $x + $intercept;
        };
    }

    // Method to fetch ridership data for a specific year via AJAX
    public function getDataForYear($year)
    {
        // Get all the ridership data for the current year, grouped by month
        $monthlyRidershipData = Ridership::select(
            DB::raw("TO_CHAR(created_at, 'MM') as month"),
            DB::raw('count(*) as total'),
            DB::raw("sum(case when LOWER(profession) LIKE '%student%' then 1 else 0 end) as student_count"), 
            DB::raw("sum(case when LOWER(profession) LIKE '%senior%' then 1 else 0 end) as senior_count"),   
            DB::raw("sum(case when gender = 'Male' then 1 else 0 end) as male_count"),
            DB::raw("sum(case when gender = 'Female' then 1 else 0 end) as female_count")
        )
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->get();     

        // Convert the data into an array
        $monthlyData = [];
        $ridershipData = [];
        foreach ($monthlyRidershipData as $data) {
            $monthNumber = ltrim($data->month, '0'); // Remove leading zero
            $monthlyData[] = [
                'month' => Carbon::create(null, $monthNumber, 1)->format('F'),
                'ridership' => $data->total,
                'student' => $data->student_count,
                'senior' => $data->senior_count,
                'male' => $data->male_count,
                'female' => $data->female_count,
            ];
            $ridershipData[] = $data->total;
        }

        return response()->json(['monthlyData' => $monthlyData, 'ridershipData' => $ridershipData]);
    }

    public function fetchPassengerDetails(Request $request)
    {
        $timeRange = $request->query('time_range');
        $station = str_replace('_', ' ', $request->query('station'));
        $direction = $request->query('direction'); // Capture the direction (upstream/downstream)

        // Extract the start and end time from the time range
        [$startTime, $endTime] = explode(' - ', $timeRange);

        // Convert the times to a format compatible with the database query
        $startTime = Carbon::parse($startTime)->format('H:i:s');
        $endTime = Carbon::parse($endTime)->format('H:i:s');

        // Get the selected date from the request or default to today
        $selectedDate = $request->query('date', Carbon::now()->format('Y-m-d'));

        // Station index mapping
        $stationIndexes = [
            'Pinagbuhatan' => 0,
            'Kalawaan' => 1,
            'San Joaquin' => 2,
            'Guadalupe' => 3,
            'Hulo' => 4,
            'Valenzuela' => 5,
            'Lambingan' => 6,
            'Sta.Ana' => 7,
            'PUP' => 8,
            'Quinta' => 9,
            'Lawton' => 10,
            'Escolta' => 11,
        ];

        // Fetch passengers based on the station, date, and time range
        $passengers = Ridership::where('origin', $station)
            ->whereDate('created_at', $selectedDate)
            ->whereTime('created_at', '>=', $startTime)
            ->whereTime('created_at', '<=', $endTime)
            ->get();

        // Debugging: log all passengers fetched before filtering
        Log::info('Passengers fetched before direction filtering', [
            'passengers' => $passengers->toArray(),
            'selectedDate' => $selectedDate,
            'station' => $station,
            'timeRange' => [$startTime, $endTime]
        ]);

        // Filter based on direction and station indices
        $filteredPassengers = $passengers->filter(function ($ridership) use ($direction, $stationIndexes) {
            $originIndex = $stationIndexes[$ridership->origin] ?? null;
            $destinationIndex = $stationIndexes[$ridership->destination] ?? null;

            // Log the indices for debugging
            Log::info('Filtering passenger', [
                'origin' => $ridership->origin,
                'destination' => $ridership->destination,
                'originIndex' => $originIndex,
                'destinationIndex' => $destinationIndex,
                'direction' => $direction
            ]);

            if ($originIndex !== null && $destinationIndex !== null) {
                if ($originIndex < $destinationIndex && $direction === 'downstream') {
                    return true; // Downstream passengers
                } elseif ($originIndex > $destinationIndex && $direction === 'upstream') {
                    return true; // Upstream passengers
                }
            }

            return false;
        });

        // Debugging: log the filtered passengers
        Log::info('Passengers after direction filtering', [
            'filteredPassengers' => $filteredPassengers->toArray()
        ]);

        return response()->json(['passengers' => $filteredPassengers]);
    }

    public function exportDailyCsv(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        // List of stations
        $stations = ['Guadalupe', 'Hulo', 'Valenzuela', 'Lambingan', 'Sta.Ana', 'PUP', 'Quinta', 'Lawton', 'Escolta', 'Maybunga', 'San Joaquin', 'Kalawaan', 'Pinagbuhatan'];

        // Fetch the ridership data
        $dailyRidershipData = Ridership::select(
            DB::raw("TO_CHAR(created_at, 'DD') as day"),
            DB::raw('origin as station'),
            DB::raw('count(*) as total'),
            DB::raw("sum(case when LOWER(profession) LIKE '%student%' then 1 else 0 end) as student_count"),
            DB::raw("sum(case when LOWER(profession) LIKE '%senior%' then 1 else 0 end) as senior_count"),
            DB::raw("sum(case when LOWER(profession) NOT LIKE '%student%' AND LOWER(profession) NOT LIKE '%senior%' then 1 else 0 end) as regular_count"),
            DB::raw("sum(case when gender ILIKE 'male' then 1 else 0 end) as male_count"),
            DB::raw("sum(case when gender ILIKE 'female' then 1 else 0 end) as female_count")
        )
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy(DB::raw("TO_CHAR(created_at, 'DD')"), 'origin')
        ->get();

        $dailyData = [];
        $stationTotals = array_fill_keys($stations, 0);
        $overallTotals = [
            'total' => 0,
            'student' => 0,
            'senior' => 0,
            'regular' => 0,
            'male' => 0,
            'female' => 0,
        ];

        foreach (range(1, 31) as $day) {
            $dayData = [
                'Date' => $day,
                'Guadalupe' => 0,
                'Hulo' => 0,
                'Valenzuela' => 0,
                'Lambingan' => 0,
                'Sta.Ana' => 0,
                'PUP' => 0,
                'Quinta' => 0,
                'Lawton' => 0,
                'Escolta' => 0,
                'Maybunga' => 0,
                'San Joaquin' => 0,
                'Kalawaan' => 0,
                'Pinagbuhatan' => 0,
                'Total' => 0,
                'Student' => 0,
                'Senior' => 0,
                'Regular' => 0,
                'Male' => 0,
                'Female' => 0,
            ];

            foreach ($dailyRidershipData as $data) {
                if ((int)$data->day == $day) {
                    $dayData[$data->station] = $data->total;
                    $dayData['Total'] += $data->total;
                    $dayData['Student'] += $data->student_count;
                    $dayData['Senior'] += $data->senior_count;
                    $dayData['Regular'] += $data->regular_count;
                    $dayData['Male'] += $data->male_count;
                    $dayData['Female'] += $data->female_count;

                    // Add to station totals
                    $stationTotals[$data->station] += $data->total;

                    // Add to overall totals
                    $overallTotals['total'] += $data->total;
                    $overallTotals['student'] += $data->student_count;
                    $overallTotals['senior'] += $data->senior_count;
                    $overallTotals['regular'] += $data->regular_count;
                    $overallTotals['male'] += $data->male_count;
                    $overallTotals['female'] += $data->female_count;
                }
            }
            $dailyData[] = $dayData;
        }

        // Define the CSV header with a dynamic filename
        $fileName = "daily_ridership_report_{$monthName}_{$year}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        // Prepare the CSV content
        $callback = function () use ($dailyData, $stations, $stationTotals, $overallTotals, $monthName, $year) {
            $file = fopen('php://output', 'w');

            // Add a title row (simulating the image)
            fputcsv($file, ['MMDA Pasig River Ferry Ridership Report']);
            
            // Add the month header row
            fputcsv($file, ["$monthName 1-31, $year"]);

            // Add header row for data columns
            fputcsv($file, array_merge(['Date'], $stations, ['Total', 'Student', 'Senior', 'Regular', 'Male', 'Female']));

            // Add the data rows
            foreach ($dailyData as $row) {
                fputcsv($file, array_values($row));
            }

            // Add station totals row
            fputcsv($file, array_merge(
                ['TOTAL'],
                array_values($stationTotals), // Station totals
                [$overallTotals['total'], $overallTotals['student'], $overallTotals['senior'], $overallTotals['regular'], $overallTotals['male'], $overallTotals['female']]
            ));

            // Add remarks section for further totals (to mimic the additional remarks like in the image)
            fputcsv($file, []); // Empty row
            fputcsv($file, ['REMARKS:']);
            fputcsv($file, ['STUDENT - ' . number_format($overallTotals['student'])]);
            fputcsv($file, ['REGULAR - ' . number_format($overallTotals['regular'])]);
            fputcsv($file, ['SENIOR - ' . number_format($overallTotals['senior'])]);
            fputcsv($file, ['MALE - ' . number_format($overallTotals['male'])]);
            fputcsv($file, ['FEMALE - ' . number_format($overallTotals['female'])]);

            fclose($file);
        };

        // Return the CSV file as a download
        return Response::stream($callback, 200, $headers);
    }

    public function exportCsv(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        // Get all the ridership data for the current year, grouped by month
        $monthlyRidershipData = Ridership::select(
            DB::raw("TO_CHAR(created_at, 'MM') as month"),
            DB::raw('count(*) as total'),
            DB::raw("sum(case when LOWER(profession) LIKE '%student%' then 1 else 0 end) as student_count"), 
            DB::raw("sum(case when LOWER(profession) LIKE '%senior%' then 1 else 0 end) as senior_count"),   
            DB::raw("sum(case when gender = 'Male' then 1 else 0 end) as male_count"),
            DB::raw("sum(case when gender = 'Female' then 1 else 0 end) as female_count")
        )
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->get();

        // Prepare CSV data
        $csvData = "Month,Ridership,Student,Senior,Male,Female\n";
        foreach ($monthlyRidershipData as $data) {
            $csvData .= implode(',', [
                Carbon::create(null, ltrim($data->month, '0'), 1)->format('F'),
                $data->total,
                $data->student_count,
                $data->senior_count,
                $data->male_count,
                $data->female_count
            ]) . "\n";
        }

        // Return CSV as a response
        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="monthly_ridership_' . $year . '.csv"',
        ]);
    }

    public function exportWeeklyCsv(Request $request)
    {
        // Define the start and end dates for previous and current weeks
        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        // Get the overall ridership data
        $previousWeekData = Ridership::whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])->count();
        $currentWeekData = Ridership::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count();

        $varianceQuantity = $currentWeekData - $previousWeekData;
        $variancePercentage = $previousWeekData > 0 ? ($varianceQuantity / $previousWeekData) * 100 : 0;

        $previousWeekDateRange = $previousWeekStart->format('M d') . ' - ' . $previousWeekEnd->format('M d, Y');
        $currentWeekDateRange = $currentWeekStart->format('M d') . ' - ' . $currentWeekEnd->format('M d, Y');

        // Prepare CSV data header
        $csvData = [
            ["Previous Week ($previousWeekDateRange)", "Current Week ($currentWeekDateRange)", "Variance Quantity", "Variance Percentage"],
            [$previousWeekData, $currentWeekData, $varianceQuantity, round($variancePercentage, 2) . "%"],
            [],
            ["Day", "Total Ridership", "Male", "Female", "Student", "Senior", "Regular"]
        ];

        // Fetch breakdown for the previous week
        $previousWeekBreakdown = Ridership::selectRaw('TO_CHAR(created_at, \'Day\') as day,
            COUNT(*) as ridership,
            SUM(CASE WHEN gender = \'male\' THEN 1 ELSE 0 END) as male,
            SUM(CASE WHEN gender = \'female\' THEN 1 ELSE 0 END) as female,
            SUM(CASE WHEN profession ILIKE \'%student%\' THEN 1 ELSE 0 END) as student,
            SUM(CASE WHEN profession ILIKE \'%senior citizen%\' THEN 1 ELSE 0 END) as senior,
            SUM(CASE WHEN profession NOT ILIKE \'%student%\' AND profession NOT ILIKE \'%senior citizen%\' THEN 1 ELSE 0 END) as regular')
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->groupByRaw('TO_CHAR(created_at, \'Day\')')
            ->orderByRaw('TO_CHAR(created_at, \'Day\')')
            ->get();

        // Fetch breakdown for the current week
        $currentWeekBreakdown = Ridership::selectRaw('TO_CHAR(created_at, \'Day\') as day,
            COUNT(*) as ridership,
            SUM(CASE WHEN gender = \'male\' THEN 1 ELSE 0 END) as male,
            SUM(CASE WHEN gender = \'female\' THEN 1 ELSE 0 END) as female,
            SUM(CASE WHEN profession ILIKE \'%student%\' THEN 1 ELSE 0 END) as student,
            SUM(CASE WHEN profession ILIKE \'%senior citizen%\' THEN 1 ELSE 0 END) as senior,
            SUM(CASE WHEN profession NOT ILIKE \'%student%\' AND profession NOT ILIKE \'%senior citizen%\' THEN 1 ELSE 0 END) as regular')
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->groupByRaw('TO_CHAR(created_at, \'Day\')')
            ->orderByRaw('TO_CHAR(created_at, \'Day\')')
            ->get();

        // Append Previous Week Breakdown to CSV data
        $csvData[] = ["Previous Week Breakdown"];
        foreach ($previousWeekBreakdown as $data) {
            $csvData[] = [
                $data->day,
                $data->ridership,
                $data->male,
                $data->female,
                $data->student,
                $data->senior,
                $data->regular
            ];
        }

        // Append Current Week Breakdown to CSV data
        $csvData[] = [];
        $csvData[] = ["Current Week Breakdown"];
        foreach ($currentWeekBreakdown as $data) {
            $csvData[] = [
                $data->day,
                $data->ridership,
                $data->male,
                $data->female,
                $data->student,
                $data->senior,
                $data->regular
            ];
        }

        // Return CSV as a response
        return response()->stream(function () use ($csvData) {
            $output = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="weekly_accomplishment_report.csv"',
        ]);
    }

    public function liveSearch(Request $request)
    {
        if ($request->ajax()) {
            $query = $request->get('query');

            // Filter the manifests using case-insensitive search with ILIKE
            $manifests = Ridership::where(function ($subQuery) use ($query) {
                    $subQuery->where('first_name', 'ILIKE', "%{$query}%")
                        ->orWhere('middle_name', 'ILIKE', "%{$query}%")
                        ->orWhere('last_name', 'ILIKE', "%{$query}%")
                        ->orWhere('address', 'ILIKE', "%{$query}%")
                        ->orWhere('contact_number', 'ILIKE', "%{$query}%")
                        ->orWhere('profession', 'ILIKE', "%{$query}%")
                        ->orWhere('origin', 'ILIKE', "%{$query}%")
                        ->orWhere('destination', 'ILIKE', "%{$query}%");
                })
                ->get();

            // If no results are found, return a message to the view
            if ($manifests->isEmpty()) {
                return '<p class="text-red-500 text-center">No results found.</p>';
            }

            // Return the partial view with the filtered results if there are any
            return view('partials.manifest-results', compact('manifests'))->render();
        }
    }

    public function dailyReportPDF(Request $request) 
    {
        $chartImage = $request->input('chartImage');
    
        // Get the current date to limit the `month_to_date` calculation
        $currentDate = Carbon::now()->day;
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
    
        // Get the month name from the month number
        $monthName = Carbon::createFromDate(null, $month, 1)->format('F');
    
        // Get daily ridership and operational data for the current month
        $dailyRidershipData = Ridership::select(
            DB::raw("to_char(created_at, 'DD') as day"),
            DB::raw('count(*) as total'),
            DB::raw("sum(case when gender = 'Male' then 1 else 0 end) as male_count"),
            DB::raw("sum(case when gender = 'Female' then 1 else 0 end) as female_count")
        )
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy('day')
        ->orderBy(DB::raw("to_char(created_at, 'DD')"), 'asc')
        ->get();
    
        // Retrieve active boats and stations data for the current month and year
        $activeBoatsByDay = BoatStatusLog::where('status', 'ACTIVE')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->select(DB::raw('EXTRACT(DAY FROM date) as day'), DB::raw('count(*) as count'))
            ->groupBy('day')
            ->pluck('count', 'day');
    
        $stationsByDay = ActivityLog::whereYear('login_date', $year)
            ->whereMonth('login_date', $month)
            ->where('assigned_station', '!=', 'None')
            ->select(DB::raw('EXTRACT(DAY FROM login_date) as day'), DB::raw('count(DISTINCT assigned_station) as count'))
            ->groupBy('day')
            ->pluck('count', 'day');
    
        // Initialize daily ridership data and calculate cumulative total
        $dailyData = [];
        $cumulativeTotal = 0; // Ensure this is initialized correctly

        // Build daily data and calculate cumulative total for each day
        foreach ($dailyRidershipData as $data) {
            $day = (int) $data->day;

            // Accumulate the total if ridership exists
            if ($data->total > 0) {
                $cumulativeTotal += $data->total;
            }

            $activeBoatsCount = $activeBoatsByDay[$day] ?? 0;
            $stationsCount = $stationsByDay[$day] ?? 0;

            // Populate the daily data for the given day
            $dailyData[$day] = [
                'date' => $day,
                'ridership' => $data->total,
                'boats' => $activeBoatsCount,
                // Always display cumulative total if ridership exists
                'month_to_date' => $cumulativeTotal,
                'stations' => $stationsCount,
                'male_passengers' => $data->male_count,
                'female_passengers' => $data->female_count,
            ];
        }

        ksort($dailyData);

        // Fill in missing days in the month
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            if (!isset($dailyData[$day])) {
                $activeBoatsCount = $activeBoatsByDay[$day] ?? 0;
                $stationsCount = $stationsByDay[$day] ?? 0;

                // Set month_to_date to 0 for missing days with no ridership
                $dailyData[$day] = [
                    'date' => $day,
                    'ridership' => 0,
                    'boats' => $activeBoatsCount,
                    'month_to_date' => 0,
                    'stations' => $stationsCount,
                    'male_passengers' => 0,
                    'female_passengers' => 0,
                ];
            }
        }

        ksort($dailyData);
    
        // Load the view with data and convert it to a PDF
        $pdf = Pdf::loadView('pdf.daily_report_pdf', compact('dailyData', 'month', 'year', 'chartImage'));
    
        // Define a filename with the selected month and year
        $filename = 'daily-report-' . $monthName . '-' . $year . '.pdf';
    
        // Download the PDF with the custom filename
        return $pdf->download($filename);
    }

    public function monthlyReportPDF(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $monthlyChartImage = $request->input('monthlyChartImage'); // Retrieve the base64 chart image

        // Get all the ridership data for the current year, grouped by month
        $monthlyRidershipData = Ridership::select(
            DB::raw("TO_CHAR(created_at, 'MM') as month"),
            DB::raw('count(*) as total'),
            DB::raw("sum(case when LOWER(profession) LIKE '%student%' then 1 else 0 end) as student_count"), 
            DB::raw("sum(case when LOWER(profession) LIKE '%senior%' then 1 else 0 end) as senior_count"),   
            DB::raw("sum(case when gender = 'Male' then 1 else 0 end) as male_count"),
            DB::raw("sum(case when gender = 'Female' then 1 else 0 end) as female_count")
        )
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->get();

        // Prepare monthly data for the PDF view
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = [
                'month' => Carbon::create(null, $i, 1)->format('F'),
                'ridership' => 0,
                'student' => 0,
                'senior' => 0,
                'male' => 0,
                'female' => 0,
            ];
        }

        foreach ($monthlyRidershipData as $data) {
            $monthNumber = ltrim($data->month, '0');
            $monthlyData[$monthNumber] = [
                'month' => Carbon::create(null, $monthNumber, 1)->format('F'),
                'ridership' => $data->total,
                'student' => $data->student_count,
                'senior' => $data->senior_count,
                'male' => $data->male_count,
                'female' => $data->female_count,
            ];
        }

        ksort($monthlyData);

        // Load the PDF view and pass data along with the base64 image
        $pdf = Pdf::loadView('pdf.monthly_report_pdf', compact('monthlyData', 'year', 'monthlyChartImage'));

        // Return the generated PDF as a download
        return $pdf->download('monthly_report_' . $year . '.pdf');
    }

    public function downloadManifest(Request $request)
    {
        $date = $request->input('date');
        $boat_id = $request->input('boat_id');
        $station = $request->input('station');

        if (!$date || !$boat_id || !$station) {
            abort(404, 'Date, Boat, or Station not selected');
        }

        // Fetch boat details
        $boat = Boat::findOrFail($boat_id);

        // Fetch ridership data for the selected date, station, and boat
        $passengers = Ridership::whereDate('created_at', $date)
            ->where('boat_id', $boat_id)
            ->where('origin', $station)
            ->get();

        // Generate the PDF
        $pdf = PDF::loadView('pdf.passenger_manifest_pdf', compact('boat', 'passengers', 'date'));

        return $pdf->download('passenger_manifest_'.$date.'.pdf');
    }

    public function manifestReportPDF(Request $request)
    {
        $selectedDate = $request->get('date') ?? now()->toDateString();

        // List of stations to display in the report
        $stations = [
            'Guadalupe', 'Hulo', 'Valenzuela', 'Lambingan', 
            'Sta.Ana', 'PUP', 'Quinta', 'Lawton', 'Escolta', 
            'Pinagbuhatan', 'San Joaquin', 'Kalawaan', 'Maybunga'
        ];

        // Initialize station data with default values
        $stationData = [];
        foreach ($stations as $station) {
            $stationData[$station] = [
                'total_manifest' => 0,
                'regular' => 0,
                'student' => 0,
                'senior' => 0,
                'pwd' => 0,
                'ticket_sold' => ' ',
                'free_ride' => ' ',
                'cash_collected' => ' ',
                'vessel_trip' => ' ',
            ];
        }

        // Get ridership data for the selected date, grouped by station
        $stationRidershipData = Ridership::select(
            'origin',
            DB::raw('COUNT(*) as total_manifest'),
            DB::raw("SUM(CASE WHEN LOWER(profession) NOT LIKE '%student%' AND LOWER(profession) NOT LIKE '%senior%' AND LOWER(profession) NOT LIKE '%pwd%' THEN 1 ELSE 0 END) as regular"),
            DB::raw("SUM(CASE WHEN LOWER(profession) LIKE '%student%' THEN 1 ELSE 0 END) as student"),
            DB::raw("SUM(CASE WHEN LOWER(profession) LIKE '%senior%' THEN 1 ELSE 0 END) as senior"),
            DB::raw("SUM(CASE WHEN LOWER(profession) LIKE '%pwd%' THEN 1 ELSE 0 END) as pwd")
        )
        ->whereDate('created_at', $selectedDate)  // Filter by selected date
        ->groupBy('origin')
        ->get();

        // Map ridership data to stations
        foreach ($stationRidershipData as $data) {
            if (isset($stationData[$data->origin])) {
                $stationData[$data->origin]['total_manifest'] = $data->total_manifest;
                $stationData[$data->origin]['regular'] = $data->regular;
                $stationData[$data->origin]['student'] = $data->student;
                $stationData[$data->origin]['senior'] = $data->senior;
                $stationData[$data->origin]['pwd'] = $data->pwd;
            }
        }

        // Generate PDF using the Blade view
        $pdf = PDF::loadView('pdf.manifest_report_pdf', compact('stationData', 'stations', 'selectedDate'));

        // Set the paper size to A4 in landscape mode
        $pdf->setPaper('A4', 'landscape');

        // Return the generated PDF
        return $pdf->download('manifest_report_' . $selectedDate . '.pdf');
    }
}
