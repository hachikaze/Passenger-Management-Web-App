<x-sidebar-layout>
    <x-slot:heading>
        Schedules
    </x-slot:heading>

    <x-slot:alert>
        @if (session('success'))
            <x-alert-bottom-success>
                {{ session('success') }}
            </x-alert-bottom-success>
        @endif
    </x-slot:alert>

    <div class="my-6 mx-40 p-2 bg-gray-200 shadow-md rounded-md flex flex-col">
        <!-- Dropdown to select station -->
        <div class="mb-4">
            <label for="station" class="block text-sm font-medium text-gray-700">Select Station</label>
            <select 
                id="station" 
                name="station" 
                class="mt-1 block w-40 pl-3 pr-10 py-2 bg-gray-100 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
                <option value="">Select Station</option>
                @foreach($stations as $station)
                    <option value="{{ $station->station_name }}">{{ $station->station_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Display downstream schedules -->
        <div id="downstreamSchedules" class="hidden flex-1 p-1 bg-white rounded-md shadow-md">
            <h3 class="text-lg font-medium text-gray-900">Downstream Schedules</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="bg-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="downstreamTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Downstream time ranges will be populated here dynamically -->
                </tbody>
            </table>
        </div>

        <!-- Display upstream schedules -->
        <div id="upstreamSchedules" class="hidden mt-6 flex-1 p-1 bg-white rounded-md shadow-md">
            <h3 class="text-lg font-medium text-gray-900">Upstream Schedules</h3>
            <table class="min-w-full divide-y divide-gray-200 mt-2">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="upstreamTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Upstream time ranges will be populated here dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Editing Time Range -->
    <div id="editTimeModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-96 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Time Range</h3>
            <form action="{{ route('schedules.updateTimeRange') }}" method="POST">
                @csrf
                <input type="hidden" id="stationName" name="station_name" value="">
                <input type="hidden" id="timeRangeId" name="time_range_id" value="">

                <div class="mb-4">
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="time" id="start_time" name="start_time" class="mt-1 block w-full border border-gray-300 rounded-md">
                </div>

                <div class="mb-4">
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="time" id="end_time" name="end_time" class="mt-1 block w-full border border-gray-300 rounded-md">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-sidebar-layout>

<script>
    // Function to fetch schedules based on the selected station
    function fetchTimeRanges() {
        const station = document.getElementById('station').value;

        if (station) {
            fetch(`/schedules/time-ranges/${station}`)
                .then(response => response.json())
                .then(data => {
                    const downstreamTableBody = document.getElementById('downstreamTableBody');
                    const upstreamTableBody = document.getElementById('upstreamTableBody');
                    downstreamTableBody.innerHTML = '';
                    upstreamTableBody.innerHTML = '';

                    const downstreamSchedules = data.filter(schedule => schedule.direction === 'downstream');
                    const upstreamSchedules = data.filter(schedule => schedule.direction === 'upstream');

                    downstreamSchedules.forEach((schedule) => {
                        const row = document.createElement('tr');
                        row.className = "hover:bg-gray-50";
                        row.innerHTML = `
                            <td class="px-6 py-4 text-sm text-gray-500">${schedule.start_time}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">${schedule.end_time}</td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <button class="px-2 py-1 bg-white rounded-md edit-button" 
                                        onclick="editTimeRange(${schedule.id}, '${schedule.start_time}', '${schedule.end_time}')">
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#9ca3af" class="w-4 h-4">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <g id="Complete">
                                                <g id="edit">
                                                    <g>
                                                        <path d="M20,16v4a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V6A2,2,0,0,1,4,4H8" fill="none" 
                                                            stroke="#9ca3af" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                        </path>
                                                        <polygon fill="none" points="12.5 15.8 22 6.2 17.8 2 8.3 11.5 8 16 12.5 15.8" 
                                                                stroke="#9ca3af" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                        </polygon>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </button>
                            </td>
                        `;
                        downstreamTableBody.appendChild(row);
                    });

                    upstreamSchedules.forEach((schedule) => {
                        const row = document.createElement('tr');
                        row.className = "hover:bg-gray-50";
                        row.innerHTML = `
                            <td class="px-6 py-4 text-sm text-gray-500">${schedule.start_time}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">${schedule.end_time}</td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <button class="px-2 py-1 bg-white rounded-md edit-button" 
                                        onclick="editTimeRange(${schedule.id}, '${schedule.start_time}', '${schedule.end_time}')">
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#9ca3af" class="w-4 h-4">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <g id="Complete">
                                                <g id="edit">
                                                    <g>
                                                        <path d="M20,16v4a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V6A2,2,0,0,1,4,4H8" fill="none" 
                                                            stroke="#9ca3af" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                        </path>
                                                        <polygon fill="none" points="12.5 15.8 22 6.2 17.8 2 8.3 11.5 8 16 12.5 15.8" 
                                                                stroke="#9ca3af" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                        </polygon>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </button>
                            </td>
                        `;
                        upstreamTableBody.appendChild(row);
                    });

                    if (downstreamSchedules.length) document.getElementById('downstreamSchedules').classList.remove('hidden');
                    if (upstreamSchedules.length) document.getElementById('upstreamSchedules').classList.remove('hidden');

                    document.getElementById('stationName').value = station;
                });
        }
    }

    // Function to open the edit modal with selected time range data
    function editTimeRange(id, startTime, endTime) {
        const modal = document.getElementById('editTimeModal');
        modal.classList.remove('hidden');
        document.getElementById('timeRangeId').value = id;
        document.getElementById('start_time').value = startTime;
        document.getElementById('end_time').value = endTime;
    }

    // Function to close the edit modal
    function closeModal() {
        document.getElementById('editTimeModal').classList.add('hidden');
    }

    // JavaScript to persist the station selection across page reloads
    document.addEventListener('DOMContentLoaded', () => {
        const stationSelect = document.getElementById('station');
        const selectedStation = localStorage.getItem('selectedStation');

        // Set the previously selected station, if any
        if (selectedStation) {
            stationSelect.value = selectedStation;
            fetchTimeRanges(); // Load schedules for the selected station
        }

        // Save new station selection to localStorage on change
        stationSelect.addEventListener('change', () => {
            const selectedValue = stationSelect.value;
            localStorage.setItem('selectedStation', selectedValue);
            fetchTimeRanges(); // Fetch schedules for the selected station
        });
    });
</script>

