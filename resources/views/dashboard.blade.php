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
            <div class="grid gap-3 md:grid-cols-4">
                <x-summary-card>
                    <x-slot:summaryName>Daily Passengers</x-slot:summaryName>
                    {{ $dailyPassengers }}
                    <x-slot:summaryDesc>Current count of passengers today</x-slot:summaryDesc>
                </x-summary-card>

                <x-summary-card>
                    <x-slot:summaryName>Month to Date Passengers</x-slot:summaryName>
                    {{ $monthToDatePassengers }}
                    <x-slot:summaryDesc>Total passengers this month</x-slot:summaryDesc>
                </x-summary-card>

                <x-summary-card>
                    <x-slot:summaryName>Registered Passenger</x-slot:summaryName>
                    {{ $totalRegisteredPassenger }}
                    <x-slot:summaryDesc></x-slot:summaryDesc>
                </x-summary-card>

                <x-summary-card>
                    <x-slot:summaryName>Operational Boats</x-slot:summaryName>
                    {{ $operationalBoats }}
                    <x-slot:summaryDesc>Current active boats</x-slot:summaryDesc>
                </x-summary-card>
            </div>
        </div>

        <div class="flex flex-col mt-5 mx-4 p-3 bg-gray-200 shadow-lg rounded justify-center">
            <div class="flex space-x-3 items-center">
                <!-- Station Ridership Chart -->
                <div class="w-1/2 max-w-lg"> 
                    <div class="bg-white rounded-md max-h-64 w-full">
                        <canvas id="station"></canvas> <!-- Station chart -->
                    </div>
                </div>

                <!-- Pie Charts (Right side) -->
                <div class="w-1/2 flex justify-between space-x-3"> 
                    <div class="bg-white rounded-md p-2">
                        <canvas id="gender" width="220" height="208"></canvas>
                        <p class="mt-4 text-xs text-center font-bold">
                            Male: {{ $monthToDateMale }} &nbsp; Female: {{ $monthToDateFemale }}
                        </p>
                    </div>
                    <div class="bg-white rounded-md p-2">
                        <canvas id="profession" width="220" height="208"></canvas>
                        <p class="mt-4 text-xs text-center font-bold">
                            Student: {{ $studentsCount }} &nbsp; Senior: {{ $seniorsCount }} &nbsp; Others: {{ $othersCount }}
                        </p>
                    </div>
                    <div class="bg-white rounded-md p-2">
                        <canvas id="userType" width="220" height="208"></canvas>
                        <p class="mt-4 text-xs text-center font-bold">
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
        <div class="p-4 bg-white rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Boat Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupied Seats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available Seats</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $currentNumber = 1; @endphp
                    @foreach($boats as $boat)
                        @if($boat->status == 'ACTIVE')
                            <tr class="hover:bg-gray-100">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $currentNumber++ }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $boat->boat_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $boat->max_capacity }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $boat->occupied_seats }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $boat->available_seats }}</td>
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
        <div class="p-4 bg-white rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Station</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passengers In Station</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Incoming Passengers</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $station = Auth::user()->assigned_station;
                        $today = \Carbon\Carbon::today();

                        // Count passengers where the station is the origin for today
                        $passengerOrigin = \App\Models\Ridership::where('origin', $station)
                            ->whereDate('created_at', $today)
                            ->count();

                        // Count passengers where the station is the destination for today
                        $passengerDestination = \App\Models\Ridership::where('destination', $station)
                            ->whereDate('created_at', $today)
                            ->count();
                    @endphp
                    <tr class="hover:bg-gray-100">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $station }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $passengerOrigin }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $passengerDestination }}</td>
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
                        label: 'Gender Breakdown',
                        backgroundColor: ['lightblue', 'lightpink'],
                        data: [{{ $monthToDateMale }}, {{ $monthToDateFemale }}],
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
                    labels: ['Student', 'Senior', 'Others'], // Add 'Others' label
                    datasets: [{
                        label: 'Profession Breakdown',
                        backgroundColor: ['gold', 'wheat', 'lightgray'], // Add color for 'Others'
                        data: [{{ $studentsCount }}, {{ $seniorsCount }}, {{ $othersCount }}], // Add 'othersCount' data
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
                    labels: ['Registered', 'Guest'], // Add 'Others' label
                    datasets: [{
                        label: 'Passenger Type Breakdown',
                        backgroundColor: ['lightblue', 'navy'], // Add color for 'Others'
                        data: [{{ $registeredPassengersCount }}, {{ $guestPassengersCount }}], // Add 'othersCount' data
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
                        backgroundColor: 'lightgreen',
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
    @endpush
</x-sidebar-layout>
