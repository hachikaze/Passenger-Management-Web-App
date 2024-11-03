<x-sidebar-layout>
    <x-slot:heading>
        Reports
    </x-slot:heading>

    <x-slot:alert>
        @if (session('success'))
            <x-alert-bottom-success>
                {{ session('success') }}
            </x-alert-bottom-success>
        @endif
    </x-slot:alert>

    <div class="mt-4 mx-5">
        <h1 class="text-xl font-bold pb-3">Passenger Manifest</h1>

        <form method="GET" action="{{ route('reports') }}">
            <div class="flex items-center justify-between">
                <!-- Search passengers input (kept in original position) -->
                <input type="text" id="search-input" name="search" value="{{ request('search') }}" class="w-1/4 p-2 border-2 border-gray-600 rounded-md" placeholder="Search passengers...">

               
            </div>
        </form>

        <!-- The table that will hold the search results -->
        <div id="search-results">
            <!-- This will be dynamically updated with the search results -->
        </div>
    </div>

    <div class="my-6 mx-5 p-2 bg-gray-200 shadow-md rounded-md flex flex-col">
        <div class="flex justify-center">
            <h2 class="text-lg font-bold mb-2">PASSENGER COUNTS BY SCHEDULE</h2>
        </div>

        <!-- Form Container -->
        <div class="flex-1 p-1 bg-white rounded-md shadow-md">
            <div class="p-3">
                <form method="GET" action="{{ route('reports') }}" class="flex items-center space-x-4">
                    <!-- Date Picker -->
                    <div>
                        <label for="datePicker" class="block text-sm font-medium text-gray-700">Select Date:</label>
                        <input type="date" id="datePicker" name="date" value="{{ $selectedDate }}" class="mt-1 block w-full pl-3 pr-10 py-2 bg-gray-100 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" onchange="this.form.submit()">
                    </div>

                    <!-- Station Dropdown -->
                    <div>
                        <label for="stationDropdown" class="block text-sm font-medium text-gray-700">Select Station:</label>
                        <select id="stationDropdown" name="station" class="mt-1 block w-full pl-3 pr-10 py-2 bg-gray-100 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="" selected>Select a Station</option>
                            @foreach(array_keys($passengerCounts) as $station)
                                <option value="{{ str_replace(' ', '_', $station) }}">{{ $station }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Boat Dropdown -->
                    <div>
                        <label for="boatDropdown" class="block text-sm font-medium text-gray-700">Select Boat:</label>
                        <select id="boatDropdown" name="boat_id" class="mt-1 block w-full pl-3 pr-10 py-2 bg-gray-100 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Select Boat</option>
                            @foreach($boats as $boat)
                                <option value="{{ $boat->id }}">{{ $boat->boat_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Download PDF Button -->
                    <div>
                        <label for="downloadButton" class="block text-sm font-medium text-gray-700 invisible">Download</label>
                        <a id="download-link" 
                        href="#"
                        class="bg-blue-500 hover:bg-blue-700 transition duration-300 rounded-md text-sm text-white font-bold py-2 px-3 cursor-pointer">
                            Download Manifest
                        </a>
                    </div>
                </form>
            </div>

            <!-- Display Data for Selected Station -->
            <div id="stationData">
                @foreach($passengerCounts as $station => $directions)
                    <div class="station-info" id="{{ str_replace(' ', '_', $station) }}" style="display: none;">

                        <!-- Downstream Table -->
                        @if(isset($directions['downstream']))
                            <h2 class="text-lg font-bold mb-3">Downstream</h2>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Range</th>
                                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passenger Count</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($directions['downstream'] as $range)
                                        <tr class="hover:bg-gray-100 cursor-pointer" 
                                            data-time-range="{{ $range['time_range'] }}" 
                                            data-station="{{ str_replace(' ', '_', $station) }}"
                                            data-direction="downstream"
                                            onclick="showPassengerDetails(this)">
                                            <td class="px-5 py-3 text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse(explode(' - ', $range['time_range'])[0])->format('g:i:s A') }} - 
                                                {{ \Carbon\Carbon::parse(explode(' - ', $range['time_range'])[1])->format('g:i:s A') }}
                                            </td>
                                            <td class="px-5 py-3 text-sm text-gray-900">{{ $range['count'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        <!-- Upstream Table -->
                        @if(isset($directions['upstream']))
                            <h2 class="text-lg font-bold mb-3">Upstream</h2>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Range</th>
                                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passenger Count</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($directions['upstream'] as $range)
                                        <tr class="hover:bg-gray-100 cursor-pointer" 
                                            data-time-range="{{ $range['time_range'] }}" 
                                            data-station="{{ str_replace(' ', '_', $station) }}"
                                            data-direction="upstream"
                                            onclick="showPassengerDetails(this)">
                                            <td class="px-5 py-3 text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse(explode(' - ', $range['time_range'])[0])->format('g:i:s A') }} - 
                                                {{ \Carbon\Carbon::parse(explode(' - ', $range['time_range'])[1])->format('g:i:s A') }}
                                            </td>
                                            <td class="px-5 py-3 text-sm text-gray-900">{{ $range['count'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Passenger Details Modal -->
    <div id="passengerDetailsModal" class="fixed z-10 inset-0 overflow-y-auto hidden bg-gray-800 bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full">
                <h2 id="modalTitle" class="text-lg font-semibold text-gray-900 mb-4">Passenger Details</h2>
                <div id="passengerDetailsContent" class="max-h-[80vh] overflow-auto">
                    <!-- Passenger details will be dynamically inserted here -->
                </div>
                <button onclick="closePassengerDetailsModal()" 
                        class="mt-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Close
                </button>
            </div>
        </div>
    </div>

    <div class="items-center my-6 mx-5 p-2 bg-gray-200 shadow-md rounded-md">
        <div class="flex justify-center">
            <h2 class="text-lg font-bold mb-2">BOAT PASSENGER SUMMARY</h2>
        </div>

        <p class="text-xs text-gray-600 mb-4 text-center">
            This table provides an overview of various boats, including the total number of passengers for each and the date of their last recorded trip.
        </p>

        <div class="flex-1 p-1 bg-white rounded-md shadow-md">
            <div class="max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr class="bg-gray-100">
                            <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Boat Name</th>
                            <th class="px-3 py-1 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">Total Passengers</th>
                            <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Last Trip Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($boats as $boat)
                            <tr class="hover:bg-gray-100">
                                <td class="px-3 py-2 text-xs text-gray-900 border-r border-gray-200">{{ $boat->boat_name }}</td>
                                <td class="px-3 py-2 text-center text-xs text-gray-900 border-r border-gray-200">{{ $boat->total_passengers }}</td>
                                <td class="px-3 py-2 text-xs text-gray-900 border-r border-gray-200">{{ $boat->last_trip_date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mx-5 p-2 my-6 bg-gray-200 shadow-md rounded-md">
        <div class="flex justify-center">
            <h2 class="text-lg font-bold mb-2">DAILY REPORT</h2>
        </div>

        <p class="text-xs text-gray-600 mb-4 text-center">
            This report provides a detailed view of ridership and related statistics for each day of the selected month.
        </p>

        <div class="flex space-x-2">
            <div class="flex-1 p-1 bg-white rounded-md shadow-md">
                <div class="flex justify-between">
                        <form action="{{ route('reports') }}" method="GET" class="">
                            <div class="flex items-center space-x-4">
                                <select name="month" class="form-select px-2 py-1 text-sm border rounded-md">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $i == $month ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create()->month($i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>

                                <select name="year" class="form-select px-2 py-1 text-sm border rounded-md">
                                    @for ($i = Carbon\Carbon::now()->year; $i >= 2020; $i--)
                                        <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>

                                <button type="submit" class="px-2 py-1 bg-blue-500 hover:bg-blue-700 transition duration-300 text-sm text-white rounded-md">
                                    Filter
                                </button>
                            </div>
                        </form>

                        <div class="flex items-center space-x-3">
                            <p class="text-sm">Download:</p>
                            <a href="{{ route('export.csv', ['month' => $month, 'year' => $year]) }}" class="px-2 py-1 bg-green-500 hover:bg-green-700 transition duration-300 text-sm text-white rounded-md">
                                CSV
                            </a>
                            <a href="{{ route('download.dailyreport', ['month' => $month, 'year' => $year]) }}" class="px-2 py-1 bg-red-500 hover:bg-red-700 transition duration-300 text-sm text-white rounded-md">
                                PDF
                            </a>
                        </div>
                    </div>

                    <div class="max-h-80 mt-2 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="w-1/7 px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase">Day</th>
                                    <th class="w-1/7 px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase">Ridership</th>
                                    <th class="w-1/7 px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase">Boats</th>
                                    <th class="w-1/7 px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase">Month to Date</th>
                                    <th class="w-1/7 px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase">Stations</th>
                                    <th class="w-1/7 px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase">Male</th>
                                    <th class="w-1/7 px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase">Female</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($dailyData as $dayData)
                                <tr class="hover:bg-gray-100">
                                    <td class="px-3 py-2 text-sm text-center text-gray-900 border-r">{{ $dayData['date'] }}</td>
                                    <td class="px-3 py-2 text-sm text-center text-gray-900 border-r">{{ $dayData['ridership'] }}</td>
                                    <td class="px-3 py-2 text-sm text-center text-gray-900 border-r">{{ $dayData['boats'] }}</td>
                                    <td class="px-3 py-2 text-sm text-center text-gray-900 border-r">{{ $dayData['month_to_date'] }}</td>
                                    <td class="px-3 py-2 text-sm text-center text-gray-900 border-r">{{ $dayData['stations'] }}</td>
                                    <td class="px-3 py-2 text-sm text-center text-gray-900 border-r">{{ $dayData['male_passengers'] }}</td>
                                    <td class="px-3 py-2 text-sm text-center text-gray-900 border-r">{{ $dayData['female_passengers'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex-1 p-1 bg-white rounded-md shadow-md">
                    <canvas id="dailyRidership" ></canvas>
                </div>

                <!-- Add hidden form to submit base64 image -->
                <form id="exportDailyPdfForm" method="POST" action="{{ route('download.dailyreport', ['month' => $month, 'year' => $year]) }}">
                    @csrf
                    <input type="hidden" name="chartImage" id="chartImage">
                </form>
        </div>
    </div>

    <div class="flex flex-col items-center my-6 mx-5 p-2 bg-gray-200 shadow-md rounded-md">
        <h2 class="text-lg font-bold mb-4">WEEKLY ACCOMPLISHMENT REPORT</h2>

        <p class="text-xs text-gray-600 mb-4">
            This report provides a comparison of accomplishments between the previous and current weeks, highlighting changes in quantity and percentage variance.
        </p>

        <table class="min-w-full bg-white shadow-md rounded-md mb-6">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Week ({{ $previousWeekDateRange }})</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Week ({{ $currentWeekDateRange }})</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variance Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variance Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-white border-b hover:bg-gray-100">
                    <td class="px-6 py-4 text-sm text-center text-gray-900 border-r border-gray-200">{{ $previousWeekData }}</td>
                    <td class="px-6 py-4 text-sm text-center text-gray-900 border-r border-gray-200">{{ $currentWeekData }}</td>
                    <td class="px-6 py-4 text-sm text-center text-gray-900 border-r border-gray-200">{{ $varianceQuantity }}</td>
                    <td class="px-6 py-4 text-sm text-center text-gray-900 border-r border-gray-200">{{ $variancePercentage }}%</td>
                </tr>
            </tbody>
        </table>

        <div class="grid grid-cols-2 gap-4">
            <!-- Previous Week Breakdown Table -->
            <div class="bg-white shadow-md rounded-md p-2">
                <h3 class="text-sm font-bold mb-4 text-gray-700">Previous Week Breakdown</h3>
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ridership</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Male</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Female</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Senior</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Regular</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previousWeekBreakdown as $day)
                        <tr class="border-b">
                            <td class="px-3 py-2 text-sm text-gray-700">{{ \Carbon\Carbon::parse($day->date)->format('l') }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->ridership }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->male }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->female }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->student }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->senior }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->other }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Current Week Breakdown Table -->
            <div class="bg-white shadow-md rounded-md p-2">
                <h3 class="text-sm font-bold mb-4 text-gray-700">Current Week Breakdown</h3>
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ridership</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Male</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Female</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Senior</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Regular</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currentWeekBreakdown as $day)
                        <tr class="border-b">
                            <td class="px-3 py-2 text-sm text-gray-700">{{ \Carbon\Carbon::parse($day->date)->format('l') }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->ridership }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->male }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->female }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->student }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->senior }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700">{{ $day->other }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <button id="exportWeeklyCsvBtn" class="mt-4 bg-green-500 hover:bg-green-700 transition duration-300 text-sm text-white px-2 py-1 rounded-md">
            Export CSV
        </button>
    </div>

    <div class="items-center my-6 mx-5 p-2 bg-gray-200 shadow-md rounded-md">
        <div class="flex justify-center">
            <h2 class="text-lg font-bold mb-2">MONTHLY REPORT</h2>
        </div>

        <p class="text-xs text-gray-600 mb-4 text-center">
            This report summarizes ridership trends for each month of the selected year.
        </p>
        
        <div class="flex space-x-2">
            <div class="flex-1 p-1 bg-white rounded-md shadow-md">
                <div class="bg-white rounded-md">
                    <!-- Year Filter Dropdown -->
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <label for="yearSelect" class="mr-2 text-sm">Select Year:</label>
                            <select id="yearSelect" class="border rounded p-1 text-sm">
                                @foreach(range(Carbon\Carbon::now()->year, 2020) as $year)
                                    <option value="{{ $year }}" {{ $year == Carbon\Carbon::now()->year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center space-x-3">
                            <p class="text-sm">Download:</p>
                            <button id="exportCsvBtn" class="bg-green-500 hover:bg-green-700 transition duration-300 text-white px-2 py-1 text-sm rounded-md ml-4">
                                CSV
                            </button>
                            <!-- Updated PDF Download Link -->
                            <a id="pdfDownloadLink" href="#" class="px-2 py-1 bg-red-500 hover:bg-red-700 transition duration-300 text-sm text-white rounded-md">
                                PDF
                            </a>
                        </div>
                    </div>

                    <!-- Rest of your table code -->
                    <div class="max-h-72 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Month</th>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Total Ridership</th>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Senior</th>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Male</th>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Female</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="ridershipTableBody">
                                @foreach($monthlyData as $monthData)
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-3 py-2 text-xs text-gray-900 border-r border-gray-200">{{ $monthData['month'] }}</td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-900 border-r border-gray-200">{{ $monthData['ridership'] }}</td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-900 border-r border-gray-200">{{ $monthData['student'] }}</td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-900 border-r border-gray-200">{{ $monthData['senior'] }}</td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-900 border-r border-gray-200">{{ $monthData['male'] }}</td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-900 border-r border-gray-200">{{ $monthData['female'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex-1 p-2 bg-white rounded-md shadow-md">
                <canvas id="monthlyRidership"></canvas>
            </div>

            <form id="pdfDownloadForm" method="POST" action="{{ route('download.monthlyreport') }}">
                @csrf
                <input type="hidden" name="year" id="pdfYearInput" value="">
                <input type="hidden" name="monthlyChartImage" id="chartImageDownload" value="">
            </form>
        </div>
    </div>

    <div class="items-center my-6 mx-5 p-2 bg-gray-200 shadow-md rounded-md">
        <div class="flex justify-center">
            <h2 class="text-lg font-bold mb-2">YEARLY REPORT</h2>
        </div>

        <p class="text-xs text-gray-600 mb-4 text-center">
            This report presents yearly ridership data with breakdowns by students, seniors, and gender.
        </p>


        <div class="flex space-x-2">
            <div class="flex-1 p-1 bg-white rounded-md shadow-md">
                <div class="bg-white rounded-md">
                <!--<div class="flex items-center justify-end mb-2">
                        <div class="flex items-center space-x-3">
                            <p class="text-sm">Download:</p>
                            <button id="exportCsvBtn" class="bg-green-500 hover:bg-green-700 transition duration-300 text-white px-2 py-1 text-sm rounded-md ml-4">
                                CSV
                            </button>

                            <a id="pdfDownloadLink" href="#" class="px-2 py-1 bg-red-500 hover:bg-red-700 transition duration-300 text-sm text-white rounded-md">
                                PDF
                            </a>
                        </div>
                    </div>-->

                    <!-- Rest of your table code -->
                    <div class="max-h-72 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Year</th>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Total Ridership</th>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Senior</th>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Male</th>
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">Female</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="">
                                @foreach($yearlyRidership as $yearData)
                                    <tr class="hover:bg-gray-100 border-gray-200">
                                        <td class="px-3 py-2 text-xs text-center text-gray-900 border-r border-b border-gray-200">{{ $yearData->year  }}</td>
                                        <td class="px-3 py-2 text-xs text-center text-gray-900 border-r border-b border-gray-200">{{ $yearData->total }}</td>
                                        <td class="px-3 py-2 text-xs text-center text-gray-900 border-r border-b border-gray-200">{{ $yearData->student_count }}</td>
                                        <td class="px-3 py-2 text-xs text-center text-gray-900 border-r border-b border-gray-200">{{ $yearData->senior_count }}</td>
                                        <td class="px-3 py-2 text-xs text-center text-gray-900 border-r border-b border-gray-200">{{ $yearData->male_count }}</td>
                                        <td class="px-3 py-2 text-xs text-center text-gray-900 border-r border-b border-gray-200">{{ $yearData->female_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex-1 p-2 bg-white rounded-md shadow-md">
                <canvas id="yearlyRidership"></canvas>
            </div>
        </div>
    </div>

    <div class="items-center my-6 mx-5 p-2 bg-gray-200 shadow-md rounded-md">
        <div class="flex justify-center">
            <h2 class="text-lg font-bold mb-2">MANIFEST REPORT</h2>
        </div>

        <p class="text-xs text-gray-600 mb-4 text-center">
            This table shows daily ridership data by station, passenger type, tickets sold, free rides, cash collected, and vessel trips.
        </p>

        <div class="flex justify-start items-center mb-2">
        <form action="{{ route('reports') }}" method="GET" class="inline">
            <label for="date" class="mr-2 text-sm font-medium text-gray-700">Select Date:</label>
            <input type="date" name="date" id="date" 
                class="p-1 text-sm border border-gray-300 rounded-md"
                value="{{ request('date') ?? \Carbon\Carbon::now()->toDateString() }}">
            
            <button type="submit" 
                    class="ml-3 px-3 py-1 bg-blue-500 hover:bg-blue-700 transition duration-300 
                        text-sm text-white rounded-md">
                Filter
            </button>
        </form>

        <form action="{{ route('download.manifestreport') }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="date" value="{{ request('date') ?? \Carbon\Carbon::now()->toDateString() }}">
            
            <button type="submit" 
                    class="ml-3 px-3 py-1 bg-green-500 hover:bg-green-700 transition duration-300 
                        text-sm text-white rounded-md">
                Download PDF
            </button>
        </form>
        </div>

        <div class="flex-1 p-1 bg-white rounded-md shadow-md">
            <div class="bg-white rounded-md">
                <div class="max-h-96 overflow-y-auto">
                    @php
                        $categories = [
                            'total_manifest' => 'Total Manifest',
                            'regular' => 'Regular',
                            'student' => 'Student',
                            'senior' => 'Senior',
                            'pwd' => 'PWD',
                            'ticket_sold' => 'Ticket Sold',
                            'free_ride' => 'Free Ride',
                            'cash_collected' => 'Cash Collected',
                            'vessel_trip' => 'Vessel Trip'
                        ];
                        $stations = ['Guadalupe', 'Hulo', 'Valenzuela', 'Lambingan', 'Sta.Ana', 'PUP', 'Quinta', 'Lawton', 'Escolta', 'Pinagbuhatan', 'San Joaquin', 'Kalawaan', 'Maybunga'];
                    @endphp

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider "></th>
                                @foreach($stations as $station)
                                    <th class="px-3 py-1 text-left text-xs font-normal text-gray-500 uppercase tracking-wider">{{ $station }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categories as $key => $category)
                                <tr class="hover:bg-gray-100">
                                    <td class="px-3 py-2 text-xs text-gray-900 border-r border-gray-200">{{ $category }}</td>
                                    @foreach($stations as $station)
                                        <td class="px-3 py-2 text-xs text-center text-gray-900 border-r border-gray-200">
                                            {{ $stationData[$station][strtolower($key)] ?? '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-2 w-full flex justify-center items-center h-full">
            <div style="width: 100%; height: 400px; border: 2px solid #ccc;" class="flex justify-center p-2 bg-white rounded-md shadow-md">
                <canvas id="manifestBarChart" class="border-solid border-2 border-black p-1 rounded-md"></canvas>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Daily Ridership -->
    <script>
        const ctx = document.getElementById('dailyRidership').getContext('2d');
        
        // Get the correct number of days for the selected month/year
        const daysInMonth = Array.from({ length: new Date({{ $year }}, {{ $month }}, 0).getDate() }, (_, i) => i + 1);
        
        // Get ridership data from the backend
        const dailyData = @json(array_column($dailyData, 'ridership'));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: daysInMonth,
                datasets: [{
                    label: 'Daily Ridership',
                    data: dailyData,
                    borderColor: '#73C5C5',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true },
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            color: '#333',
                            font: {
                                family: 'Helvetica',
                                size: 12,
                                weight: 'bold',
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 5,
                        titleFont: {
                            family: 'Helvetica',
                            size: 14,
                            weight: 'bold',
                        },
                        bodyFont: {
                            family: 'Helvetica',
                            size: 12,
                        }
                    }
                }
            }
        });

        document.querySelector('.bg-red-500').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default link action

            const chart = Chart.getChart('dailyRidership'); // Retrieve the chart instance
            if (chart) {
                const chartImage = chart.toBase64Image(); // Convert chart to base64 image
                document.getElementById('chartImage').value = chartImage; // Set the hidden input value
                document.getElementById('exportDailyPdfForm').submit(); // Submit the form with the image
            } else {
                console.error("Chart instance not found.");
            }
        });
    </script>   

    <!-- This Year Ridership -->
    <script>
        // Initialize the chart
        const newctx = document.getElementById('monthlyRidership');
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        const ridershipData = @json($ridershipData)

        const ridershipChart = new Chart(newctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'This Month Ridership',
                    backgroundColor: [
                        '#8BC1F7', '#519DE9', '#BDE2B9', '#7CC674', '#A2D9D9', '#73C5C5', 
                        '#B2B0EA', '#5752D1', '#F4B678', '#EF9234', '#3C3D99', '#C9190B'
                    ], // Different color for each month
                    data: ridershipData,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'x',
                scales: {
                    x: { beginAtZero: true }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            color: '#333',
                            font: {
                                family: 'Helvetica',
                                size: 12,
                                weight: 'bold',
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 5,
                        titleFont: {
                            family: 'Helvetica',
                            size: 14,
                            weight: 'bold',
                        },
                        bodyFont: {
                            family: 'Helvetica',
                            size: 12,
                        }
                    }
                }
            }
        });

        document.getElementById('pdfDownloadLink').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default action (following the link)

            const selectedYear = document.getElementById('yearSelect').value; // Get the selected year
            const chart = Chart.getChart('monthlyRidership'); // Retrieve the chart instance

            if (chart) {
                const chartImage = chart.toBase64Image(); // Convert chart to base64 image

                // Populate hidden form inputs with the necessary data
                document.getElementById('pdfYearInput').value = selectedYear;
                document.getElementById('chartImageDownload').value = chartImage;

                // Submit the form
                document.getElementById('pdfDownloadForm').submit();
            } else {
                console.error("Chart instance not found.");
            }
        });

        document.getElementById('exportCsvBtn').addEventListener('click', function() {
            window.location.href = `/reports/export-csv?year=${document.getElementById('yearSelect').value}`;
        });

        // Handle Year Change
        document.getElementById('yearSelect').addEventListener('change', function () {
            let selectedYear = this.value;

            // Update the PDF download link with the selected year
            document.getElementById('pdfDownloadLink').href = `/reports/download/monthlypdf?year=${selectedYear}`;

            // Fetch and update the table and chart data
            fetch(`/reports/data/${selectedYear}`)
                .then(response => response.json())
                .then(data => {
                    // Update Table Data
                    const tableBody = document.getElementById('ridershipTableBody');
                    tableBody.innerHTML = ''; // Clear existing rows

                    months.forEach((monthName, index) => {
                        const monthData = data.monthlyData.find(m => m.month === monthName);
                        if (monthData) {
                            // If there's data for this month
                            tableBody.innerHTML += `
                                <tr class="hover:bg-gray-100">
                                    <td class="px-3 py-2 text-xs text-gray-900">${monthData.month}</td>
                                    <td class="px-3 py-2 text-xs text-gray-900">${monthData.ridership}</td>
                                    <td class="px-3 py-2 text-xs text-gray-900">${monthData.student}</td>
                                    <td class="px-3 py-2 text-xs text-gray-900">${monthData.senior}</td>
                                    <td class="px-3 py-2 text-xs text-gray-900">${monthData.male}</td>
                                    <td class="px-3 py-2 text-xs text-gray-900">${monthData.female}</td>
                                </tr>`;
                        } else {
                            // If no data for this month, insert empty row
                            tableBody.innerHTML += `
                                <tr class="hover:bg-gray-100">
                                    <td class="px-3 py-2 text-xs text-gray-900">${monthName}</td>
                                    <td class="px-3 py-2 text-xs text-gray-900">0</td>
                                    <td class="px-3 py-2 text-xs text-gray-900">0</td>
                                    <td class="px-3 py-2 text-xs text-gray-900">0</td>
                                    <td class="px-3 py-2 text-xs text-gray-900">0</td>
                                    <td class="px-3 py-2 text-xs text-gray-900">0</td>
                                </tr>`;
                        }
                    });

                    // Reset chart data to zeros for all months
                    let alignedRidershipData = new Array(12).fill(0); // Array for 12 months (January to December)

                    // Place the ridership data in the correct month position
                    data.monthlyData.forEach(monthData => {
                        // Use the month index (January = 0, December = 11)
                        let monthIndex = months.indexOf(monthData.month);
                        if (monthIndex >= 0) {
                            alignedRidershipData[monthIndex] = monthData.ridership;
                        }
                    });

                    // Update the chart data
                    ridershipChart.data.datasets[0].data = alignedRidershipData;
                    ridershipChart.update();
                })
                .catch(error => console.error('Error:', error));
        });

        // Set initial PDF download link when page loads
        document.addEventListener('DOMContentLoaded', function () {
            let initialYear = document.getElementById('yearSelect').value;
            document.getElementById('pdfDownloadLink').href = `/reports/download/monthlypdf?year=${selectedYear}`;
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const newctx1 = document.getElementById('yearlyRidership').getContext('2d');

            // Data variables defined correctly for the chart
            const years = @json($years);  // Labels for the X-axis (years)
            const yearlyRidershipData = @json($totals);  // Ridership values

            const yearlyChart = new Chart(newctx1, {
                type: 'bar',  // Type of chart
                data: {
                    labels: years,  // Use `years` as the labels
                    datasets: [{
                        label: 'Yearly Ridership',
                        backgroundColor: '#3C3D99',
                        data: yearlyRidershipData,   // Use `data` for the ridership values
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'x',
                    scales: {
                        x: { beginAtZero: true }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                color: '#333',
                                font: {
                                    family: 'Helvetica',
                                    size: 12,
                                    weight: 'bold',
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 5,
                            titleFont: {
                                family: 'Helvetica',
                                size: 14,
                                weight: 'bold',
                            },
                            bodyFont: {
                                family: 'Helvetica',
                                size: 12,
                            }
                        }
                    }
                }
            });
        });
    </script>

    <script>
        document.getElementById('stationDropdown').addEventListener('change', function() {
        // Hide all station info sections
        document.querySelectorAll('.station-info').forEach(function(el) {
            el.style.display = 'none';
        });

        // Show the selected station's info
        const selectedStation = this.value;
        const selectedStationId = selectedStation.replace(/\s+/g, '_');
        document.getElementById(selectedStationId).style.display = 'block';
    });

    const datePicker = document.getElementById('datePicker');

    function showPassengerDetails(row) {
        const timeRange = row.getAttribute('data-time-range');
        const station = row.getAttribute('data-station');
        const direction = row.getAttribute('data-direction'); // Capture direction (upstream/downstream)

        const selectedDate = datePicker.value;

        // Update modal title to include the direction
        const modalTitle = document.getElementById('modalTitle');
        modalTitle.textContent = `Passenger Details (${direction.charAt(0).toUpperCase() + direction.slice(1)})`;

        // Pass direction to the fetch request
        fetchPassengerDetails(selectedDate, timeRange, station, direction);
    }

    function fetchPassengerDetails(selectedDate, timeRange, station, direction) {
        console.log('Fetching passenger details for:', { selectedDate, timeRange, station, direction });

        // Clear previous modal content and hide the modal initially
        document.getElementById('passengerDetailsContent').innerHTML = 'Loading...'; // Add loading state
        document.getElementById('passengerDetailsModal').classList.add('hidden');

        // Fetch passenger details from the server with direction as an additional parameter
        fetch(`/fetch-passenger-details?date=${encodeURIComponent(selectedDate)}&time_range=${encodeURIComponent(timeRange)}&station=${encodeURIComponent(station)}&direction=${encodeURIComponent(direction)}`)
            .then(response => response.json())
            .then(data => {
                console.log('Fetched Data:', data); // Log fetched data

                // Check if passengers are an object or array and convert to array if necessary
                let passengers = data.passengers;
                if (passengers && typeof passengers === 'object' && !Array.isArray(passengers)) {
                    // If passengers is an object, convert it to an array
                    passengers = Object.values(passengers);
                }

                if (passengers && passengers.length > 0) {
                    console.log(`Passengers found for ${direction}:`, passengers);

                    const passengerDetails = `
                        <div class="max-h-[80vh] overflow-auto rounded-lg shadow-lg">
                            <table class="w-full table-auto divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profession</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origin</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    ${passengers.map(passenger => `
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">${passenger.first_name} ${passenger.middle_name} ${passenger.last_name}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">${passenger.gender}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">${passenger.age}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">${passenger.profession}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">${passenger.origin}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">${passenger.destination}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                    document.getElementById('passengerDetailsContent').innerHTML = passengerDetails;
                    document.getElementById('passengerDetailsModal').classList.remove('hidden'); // Show modal
                } else {
                    console.log(`No passengers found for ${direction}`);
                    document.getElementById('passengerDetailsContent').innerHTML = 'No passengers found for the selected date and direction.';
                    document.getElementById('passengerDetailsModal').classList.remove('hidden'); // Show modal even if no passengers are found
                }
            })
            .catch(error => {
                console.error('Error fetching passenger details:', error);
                document.getElementById('passengerDetailsContent').innerHTML = 'Error fetching data.';
                document.getElementById('passengerDetailsModal').classList.remove('hidden'); // Show modal even on error
            });
    }

    function closePassengerDetailsModal() {
        document.getElementById('passengerDetailsModal').classList.add('hidden');
        document.getElementById('passengerDetailsContent').innerHTML = ''; // Clear modal content on close
    }
    </script>

    <script>
        document.getElementById('exportWeeklyCsvBtn').addEventListener('click', function() {
            window.location.href = '/reports/export-weekly-csv';
        });
    </script>

    <script>
        document.getElementById('search-input').addEventListener('keyup', function() {
            let query = this.value;

            if (query.length > 0) {
                fetch(`/reports/search?query=${query}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    // Update the table or display the "No results" message
                    document.getElementById('search-results').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
            } else {
                // Clear the results if input is empty
                document.getElementById('search-results').innerHTML = '';
            }
        });
    </script>

    <script>
        document.getElementById('download-link').addEventListener('click', function(event) {
            var date = document.getElementById('datePicker').value;
            var boatId = document.getElementById('boatDropdown').value;
            var station = document.getElementById('stationDropdown').value;

            if (!date || !boatId || !station) {
                event.preventDefault(); // Prevent download if no date, boat, or station is selected
                alert('Please select a date, boat, and station.');
            } else {
                var url = "{{ route('downloadManifest') }}?date=" + date + "&boat_id=" + boatId + "&station=" + station;
                this.href = url;
            }
        });
    </script>

    <script>
        // Initialize the chart
        const ctxnew = document.getElementById('manifestBarChart');

        // Extract the station names and total manifest data from the server
        const stations = @json(array_keys($stationData));
        const totalManifest = @json(array_column($stationData, 'total_manifest'));

        const manifestChart = new Chart(ctxnew, {
            type: 'bar',
            data: {
                labels: stations, // Station names
                datasets: [{
                    label: 'Total Manifest',
                    backgroundColor: [
                        '#8BC1F7', '#519DE9', '#BDE2B9', '#7CC674', '#A2D9D9', '#73C5C5',
                        '#B2B0EA', '#5752D1', '#F4B678', '#EF9234', '#3C3D99', '#C9190B'
                    ],
                    data: totalManifest, // Total manifest count per station
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'x', // Horizontal bar chart
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true } // Ensure it starts at 0
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            color: '#333',
                            font: {
                                family: 'Helvetica',
                                size: 12,
                                weight: 'bold',
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 5,
                        titleFont: {
                            family: 'Helvetica',
                            size: 14,
                            weight: 'bold',
                        },
                        bodyFont: {
                            family: 'Helvetica',
                            size: 12,
                        }
                    }
                }
            }
        });
    </script>

    @endpush
</x-sidebar-layout>