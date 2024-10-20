<x-sidebar-layout>
    <x-slot:heading>
        Dashboard
    </x-slot:heading>

    <x-slot:alert>
        @if (session('success'))
            <x-alert-bottom-success>
                {{ session('success') }}
            </x-alert-bottom-success>
        @endif
    </x-slot:alert>

    <div class="flex flex-col">
        <!-- Summary Cards -->
        <div class="mt-5 mx-5 p-3 bg-gray-200 shadow-lg rounded">
            <div class="grid gap-3 md:grid-cols-5">
                <x-summary-card>
                    <x-slot:summaryName>Today's Passengers</x-slot:summaryName>
                    {{ $dailyPassengers }}
                    <x-slot:summaryDesc>Total passengers recorded today</x-slot:summaryDesc>
                </x-summary-card>

                <x-summary-card>
                    <x-slot:summaryName>Total Passengers This Month</x-slot:summaryName>
                    {{ $monthToDatePassengers }}
                    <x-slot:summaryDesc>Accumulated passenger count for the current month</x-slot:summaryDesc>
                </x-summary-card>

                <x-summary-card>
                    <x-slot:summaryName>Total Registered Passengers</x-slot:summaryName>
                    {{ $totalRegisteredPassenger }}
                    <x-slot:summaryDesc>Total number of registered passengers in the system</x-slot:summaryDesc>
                </x-summary-card>

                <x-summary-card>
                    <x-slot:summaryName>Active Boats</x-slot:summaryName>
                    {{ $operationalBoats }}
                    <x-slot:summaryDesc>Number of boats currently in operation</x-slot:summaryDesc>
                </x-summary-card>

                <x-summary-card>
                    <x-slot:summaryName>Active Stations</x-slot:summaryName>
                    {{ $activeStationsToday }}
                    <x-slot:summaryDesc>Number of stations currently operational</x-slot:summaryDesc>
                </x-summary-card>
            </div>
        </div>

        <div class="flex flex-col mt-5 mx-auto p-3 bg-gray-200 shadow-lg rounded justify-center">
            <div class="flex space-x-3 items-center">
                <!-- Station Ridership Chart -->
                <div class="w-1/2 max-w-lg"> 
                    <div class="bg-white rounded-md max-h-[340px] h-[270px] w-full">
                        <canvas id="station"></canvas> <!-- Station chart -->
                    </div>
                </div>

                <!-- Pie Charts (Right side) -->
                <div class="w-1/2 flex justify-between space-x-3"> 
                    <div class="bg-white rounded-md p-2">
                        <h2 class="text-center font-bold text-gray-500 text-xs mb-2">Passenger Gender Distribution</h2> <!-- Added chart title -->
                        <canvas id="gender" width="220" height="208"></canvas>
                        <p class="mt-2 text-xs text-center font-normal">
                            Male: {{ $todayMaleCount }} &nbsp; Female: {{ $todayFemaleCount }}
                        </p>
                    </div>
                    <div class="bg-white rounded-md p-2">
                        <h2 class="text-center font-bold text-gray-500 text-xs mb-2">Passenger Category Breakdown</h2>
                        <canvas id="profession" width="220" height="208"></canvas>
                        <p class="mt-2 text-xs text-center font-normal">
                            Student: {{ $studentsCount }} &nbsp; Senior: {{ $seniorsCount }} &nbsp; Others: {{ $othersCount }}
                        </p>
                    </div>
                    <div class="bg-white rounded-md p-2">
                        <h2 class="text-center font-bold text-gray-500 text-xs mb-2">Registered vs Guest Passengers</h2>
                        <canvas id="userType" width="220" height="208"></canvas>
                        <p class="mt-2 text-xs text-center font-normal">
                            Registered: {{ $registeredPassengersCount }} &nbsp; Guest: {{ $guestPassengersCount }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Boat Status Table -->
    <div class="mt-4 mx-5 p-3 bg-gray-200 shadow-lg rounded">
        <h1 class="text-xl font-bold pb-4">Operational Boat Status</h1>
        <div class="p-1 bg-white rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Boat Name</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Occupied Seats</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Available Seats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Boat Destination</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $currentNumber = 1; @endphp
                    @foreach($boats as $boat)
                        @if($boat->status == 'ACTIVE')
                            <tr class="hover:bg-gray-100">
                                <td class="px-6 py-4 text-sm text-gray-900 border-b">{{ $currentNumber++ }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 border-r border-b">{{ $boat->boat_name }}</td>
                                <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-b">{{ $boat->max_capacity }}</td>
                                <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-b">{{ $boat->occupied_seats }}</td>
                                <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-b">{{ $boat->available_seats }}</td>
                                <td class="px-6 py-4 text-left text-sm text-gray-900 border-b">{{ $boat->destination }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Station Status Table -->
<div class="my-6 mx-5 p-3 bg-gray-200 shadow-lg rounded">
    <h1 class="text-xl font-bold pb-4">Station Status</h1>
    <div class="p-1 bg-white rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Station</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Passengers In Station</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Incoming Passengers</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $station = Auth::user()->assigned_station;
                    $today = \Carbon\Carbon::today();

                    // Get all registered (non-cancelled) passengers for the assigned station
                    $validManifests = \App\Models\PassengerManifest::where('origin', $station)
                        ->where('status', '!=', 'CANCELLED') // Exclude cancelled passengers
                        ->pluck('id'); // Get all valid manifest IDs

                    // Passengers still in the station: registered but NOT yet checked in (no ridership entry)
                    $passengersInStation = \App\Models\PassengerManifest::where('origin', $station)
                        ->where('status', 'REGISTERED')
                        ->whereNotIn('id', \App\Models\Ridership::whereIn('ridership_id_key', $validManifests)
                            ->whereDate('created_at', $today) // Checked in today
                            ->pluck('ridership_id_key'))
                        ->count();

                    // Count incoming passengers arriving at this station today, excluding checked-out passengers
                    $incomingPassengers = \App\Models\Ridership::where('destination', $station)
                        ->whereDate('created_at', $today) // Arrived today
                        ->whereNull('updated_at') // Not checked out yet
                        ->count();
                @endphp
                <tr class="hover:bg-gray-100">
                    <td class="px-6 py-4 text-sm text-gray-900 border-r border-b">{{ $station }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-b">{{ $passengersInStation }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900 border-b">{{ $incomingPassengers }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Gender Breakdown Chart -->
        <script>
            const gender = document.getElementById('gender');
            new Chart(gender, {
                type: 'pie',
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        label: 'Gender Breakdown for Today',
                        backgroundColor: ['#519DE9', 'pink'],
                        data: [{{ $todayMaleCount }}, {{ $todayFemaleCount }}],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                color: '#333',
                                font: { family: 'Helvetica', size: 12, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 5,
                            titleFont: { family: 'Helvetica', size: 14, weight: 'bold' },
                            bodyFont: { family: 'Helvetica', size: 12 }
                        }
                    }
                }
            });
        </script>

        <!-- Profession Group Breakdown Chart -->
        <script>
            const profession = document.getElementById('profession');
            new Chart(profession, {
                type: 'pie',
                data: {
                    labels: ['Student', 'Senior', 'Others'],
                    datasets: [{
                        label: 'Profession Breakdown for Today',
                        backgroundColor: ['#F4C145', '#F9E0A2', '#B8BBBE'],
                        data: [{{ $studentsCount }}, {{ $seniorsCount }}, {{ $othersCount }}],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                color: '#333',
                                font: { family: 'Helvetica', size: 12, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 5,
                            titleFont: { family: 'Helvetica', size: 14, weight: 'bold' },
                            bodyFont: { family: 'Helvetica', size: 12 }
                        }
                    }
                }
            });
        </script>

        <!-- Passenger Type Breakdown -->
        <script>
            const userType = document.getElementById('userType');
            new Chart(userType, {
                type: 'pie',
                data: {
                    labels: ['Registered', 'Guest'],
                    datasets: [{
                        label: 'Passenger Type Breakdown for Today',
                        backgroundColor: ['#5752D1', '#B2B0EA'],
                        data: [{{ $registeredPassengersCount }}, {{ $guestPassengersCount }}],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                color: '#333',
                                font: { family: 'Helvetica', size: 12, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 5,
                            titleFont: { family: 'Helvetica', size: 14, weight: 'bold' },
                            bodyFont: { family: 'Helvetica', size: 12 }
                        }
                    }
                }
            });
        </script>

        <!-- Station Ridership Chart -->
        <script>
            const station = document.getElementById('station');
            new Chart(station, {
                type: 'bar',
                data: {
                    labels: ['Pinagbuhatan', 'Kalawaan', 'San Joaquin', 'Guadalupe', 'Hulo', 'Valenzuela', 'Lambingan', 'Sta-Ana', 'PUP', 'Quinta', 'Lawton', 'Escolta'],
                    datasets: [{
                        label: 'Number of Passengers in each station',
                        backgroundColor: '#7CC674',
                        data: @json($stationPassengerCounts),
                        borderWidth: 1,
                        borderRadius: 2,
                        barThickness: 15
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) { 
                                    return Number.isInteger(value) ? value : ''; 
                                },
                                stepSize: 1 
                            }
                        }
                    }
                }
            });
        </script>

        <script>
            setInterval(function() {
                location.reload();
            }, 60000); // Refresh every 60 seconds
        </script>
    @endpush
</x-sidebar-layout>
