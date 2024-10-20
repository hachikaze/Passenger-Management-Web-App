<x-sidebar-layout>
    <x-slot:heading>
        User Management
    </x-slot:heading>

    <x-slot:alert>
        @if (session('success'))
            <x-alert-bottom-success>
                {{ session('success') }}
            </x-alert-bottom-success>
        @endif
    </x-slot:alert>

    <div class="my-8 mx-4 p-2 bg-gray-200 shadow-md rounded">
        <h1 class="text-xl font-bold">PRF Users</h1>

        <div class="my-4 bg-white shadow-md overflow-x-auto">
            <table class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Contact Number</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Verified</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">User Type</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Assigned Station</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->id }}</td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->contact_number }}</td>
                        <td class="px-5 py-4 text-center whitespace-nowrap text-sm text-gray-900">{{ $user->email_verified_at ? 'Yes' : 'No' }}</td>
                        
                        <!-- User Type Dropdown with Save button -->
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">
                            <form action="{{ route('update-user-type') }}" method="POST" class="inline-block flex items-center" id="updateUserType-{{ $user->id }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <select name="user_type" id="user-type-{{ $user->id }}" class="form-select p-1 rounded-md border" disabled data-original-value="{{ $user->user_type }}">
                                    <option value="Admin" {{ $user->user_type == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="Aide" {{ $user->user_type == 'Aide' ? 'selected' : '' }}>Ferry Aide</option>
                                    <option value="Operator" {{ $user->user_type == 'Operator' ? 'selected' : '' }}>Ferry Operator</option>
                                    <option value="Boat" {{ $user->user_type == 'Boat' ? 'selected' : '' }}>Boat Manager</option>
                                </select>
                                <button type="button" class="ml-2 px-2 py-1 bg-white rounded-md text-white save-user-type-button" style="display: none;" onclick="showUserTypeConfirmation('{{ $user->id }}')">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1716 1C18.702 1 19.2107 1.21071 19.5858 1.58579L22.4142 4.41421C22.7893 4.78929 23 5.29799 23 5.82843V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H18.1716ZM4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21L5 21L5 15C5 13.3431 6.34315 12 8 12L16 12C17.6569 12 19 13.3431 19 15V21H20C20.5523 21 21 20.5523 21 20V6.82843C21 6.29799 20.7893 5.78929 20.4142 5.41421L18.5858 3.58579C18.2107 3.21071 17.702 3 17.1716 3H17V5C17 6.65685 15.6569 8 14 8H10C8.34315 8 7 6.65685 7 5V3H4ZM17 21V15C17 14.4477 16.5523 14 16 14L8 14C7.44772 14 7 14.4477 7 15L7 21L17 21ZM9 3H15V5C15 5.55228 14.5523 6 14 6H10C9.44772 6 9 5.55228 9 5V3Z" fill="#37e65a"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>

                        <!-- Assigned Station Dropdown with Save button -->
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">
                            <form action="{{ route('update-assigned-station') }}" method="POST" class="inline-block flex items-center space-x-2" id="updateAssignedStation-{{ $user->id }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                
                                <!-- Dropdown -->
                                <select name="assigned_station" id="assigned-station-{{ $user->id }}" class="form-select p-1 rounded-md border" disabled data-original-value="{{ $user->assigned_station }}">
                                    <option value="None">None</option>
                                    @foreach($stations as $station_name)
                                        <option value="{{ $station_name }}" {{ $user->assigned_station == $station_name ? 'selected' : '' }}>{{ $station_name }}</option>
                                    @endforeach
                                </select>
                                
                                <!-- Save button -->
                                <button type="button" class="px-2 py-1 bg-white rounded-md text-white save-station-button hidden" onclick="showAssignedStationConfirmation('{{ $user->id }}')">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1716 1C18.702 1 19.2107 1.21071 19.5858 1.58579L22.4142 4.41421C22.7893 4.78929 23 5.29799 23 5.82843V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H18.1716ZM4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21L5 21L5 15C5 13.3431 6.34315 12 8 12L16 12C17.6569 12 19 13.3431 19 15V21H20C20.5523 21 21 20.5523 21 20V6.82843C21 6.29799 20.7893 5.78929 20.4142 5.41421L18.5858 3.58579C18.2107 3.21071 17.702 3 17.1716 3H17V5C17 6.65685 15.6569 8 14 8H10C8.34315 8 7 6.65685 7 5V3H4ZM17 21V15C17 14.4477 16.5523 14 16 14L8 14C7.44772 14 7 14.4477 7 15L7 21L17 21ZM9 3H15V5C15 5.55228 14.5523 6 14 6H10C9.44772 6 9 5.55228 9 5V3Z" fill="#37e65a"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>

                        <!-- Status -->
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                                $session = $sessions->firstWhere('user_id', $user->id);
                            @endphp
                            @if ($session && $session->is_online)
                                <span class="text-green-500">Online</span>
                            @else
                                <span class="text-red-500">Offline</span>
                            @endif
                        </td>   

                        <td class="px-5 py-4 text-center whitespace-nowrap text-sm font-medium">
                            <!-- Edit Button -->
                            <button class="px-2 py-1 bg-white rounded-md hover:bg-gray-100 transition duration-300 edit-button" onclick="toggleEdit(this, '{{ $user->id }}')">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#9ca3af" class="w-4 h-4">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <g id="Complete">
                                            <g id="edit">
                                                <g>
                                                    <path d="M20,16v4a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V6A2,2,0,0,1,4,4H8" fill="none" stroke="#9ca3af" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                                    <polygon fill="none" points="12.5 15.8 22 6.2 17.8 2 8.3 11.5 8 16 12.5 15.8" stroke="#9ca3af" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polygon>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            </button>
                            
                            <!-- Cancel Button -->
                            <button class="ml-2 px-2 py-1 bg-white rounded-md text-white cancel-button" onclick="cancelEdit(this, '{{ $user->id }}')" style="display: none;">
                                <svg viewBox="0 0 512 512" class="w-5 h-5" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <title>cancel</title>
                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g id="work-case" fill="#9ca3af" transform="translate(91.520000, 91.520000)">
                                                <polygon id="Close" points="328.96 30.2933333 298.666667 1.42108547e-14 164.48 134.4 30.2933333 1.42108547e-14 1.42108547e-14 30.2933333 134.4 164.48 1.42108547e-14 298.666667 30.2933333 328.96 164.48 194.56 298.666667 328.96 328.96 298.666667 194.56 164.48">
                                                </polygon>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            </button>

                            <!-- Delete Button (Initially Hidden) -->
                            <form id="deleteForm-{{ $user->id }}" action="{{ route('delete-user', $user->id) }}" method="POST" class="inline-block delete-form" style="display: none;">
                                @csrf
                                @method('DELETE')
                                <button class="ml-2 px-2 py-1 bg-white rounded-md text-white delete-button" onclick="showDeleteConfirmation('{{ $user->id }}', event)" style="display: none;">
                                    <svg viewBox="0 0 1024 1024" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path fill="#ff0033" d="M160 256H96a32 32 0 0 1 0-64h256V95.936a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32V192h256a32 32 0 1 1 0 64h-64v672a32 32 0 0 1-32 32H192a32 32 0 0 1-32-32V256zm448-64v-64H416v64h192zM224 896h576V256H224v640zm192-128a32 32 0 0 1-32-32V416a32 32 0 0 1 64 0v320a32 32 0 0 1-32 32zm192 0a32 32 0 0 1-32-32V416a32 32 0 0 1 64 0v320a32 32 0 0 1-32 32z"></path>
                                        </g>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mb-2">
            {{ $users->links('pagination::tailwind') }}
        </div>
        
        <div class="flex justify-end">
            <a href="{{ route('register') }}" class="px-3 py-2 bg-emerald-500 hover:bg-emerald-600 transition duration-300 text-sm text-white rounded-md">Add New User</a>
        </div>
        
    </div>

    <div class="my-8 mx-5 p-2 bg-gray-200 shadow-md rounded">
        <h1 class="text-xl font-bold pb-4">User Activity Log</h1>

        <!-- Date Filter Form -->
        <form method="GET" action="{{ route('users.index') }}" class="mb-4">
            <label for="date" class="text-gray-700">Filter by Date:</label>
            <input type="date" id="date" name="date" value="{{ $filterDate }}" class="ml-2 p-2 border rounded-md">
            <button type="submit" class="ml-2 px-3 py-2 bg-blue-500 hover:bg-blue-600 transition duration-300 text-sm text-white rounded-md">Filter</button>
        </form>

        <div class="mb-4 bg-white rounded-md shadow-md mt-4 overflow-auto" style="max-height: 500px;">
            <table class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-100 sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Log ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">User ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Assigned Station</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Login Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Login Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($activity_logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->user_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->assigned_station }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->login_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->created_at->format('H:i:s') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="my-8 mx-4 p-2 bg-gray-200 shadow-md rounded">
        <h1 class="text-xl font-bold">Registered Passengers</h1>

        <div class="my-4 bg-white shadow-md">
            <table class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Contact Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Age</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Profession</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($registeredPassenger as $registeredUser)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $registeredUser->user_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registeredUser->first_name }} {{ $registeredUser->middle_name }} {{ $registeredUser->last_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registeredUser->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registeredUser->phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registeredUser->age }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registeredUser->gender }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registeredUser->profession }}</td>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    </div>

    <!-- Confirmation Modals for Users -->
    @foreach($users as $user)
        <!-- Update Confirmation Modal -->
        <x-modal-layout id="updateUserTypeModal-{{ $user->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <h2 class="text-xl font-bold mb-4">Confirm Update</h2>
            <p class="mb-4">Are you sure you want to update the user type for {{ $user->first_name }} {{ $user->last_name }}?</p>
            <div class="flex justify-end">
                <button class="p-2 bg-green-500 rounded-md text-white mr-2 hover:bg-green-700" onclick="confirmUserTypeUpdate('{{ $user->id }}')">Yes</button>
                <button class="p-2 bg-gray-500 rounded-md text-white hover:bg-gray-700" onclick="hideModal('updateUserTypeModal-{{ $user->id }}')">No</button>
            </div>
        </x-modal-layout>

        <x-modal-layout id="updateAssignedStationModal-{{ $user->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <h2 class="text-xl font-bold mb-4">Confirm Update</h2>
            <p class="mb-4">Are you sure you want to update the assigned station for {{ $user->first_name }} {{ $user->last_name }}?</p>
            <div class="flex justify-end">
                <button class="p-2 bg-green-500 rounded-md text-white mr-2 hover:bg-green-700" onclick="confirmAssignedStationUpdate('{{ $user->id }}')">Yes</button>
                <button class="p-2 bg-gray-500 rounded-md text-white hover:bg-gray-700" onclick="hideModal('updateAssignedStationModal-{{ $user->id }}')">No</button>
            </div>
        </x-modal-layout>

        <!-- Delete Confirmation Modal -->
        <x-modal-layout id="deleteConfirmationModal-{{ $user->id }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <h2 class="text-xl font-bold mb-4">Confirm Delete</h2>
            <p class="mb-4">Are you sure you want to delete the user {{ $user->first_name }} {{ $user->last_name }}?</p>
            <div class="flex justify-end">
                <button class="p-2 bg-red-500 rounded-md text-white mr-2 hover:bg-red-700" onclick="confirmDelete('{{ $user->id }}', event)">Yes</button>
                <button class="p-2 bg-gray-500 rounded-md text-white hover:bg-gray-700" onclick="hideModal('deleteConfirmationModal-{{ $user->id }}')">No</button>
            </div>
        </x-modal-layout>
    @endforeach

    <script>
        let userIdToUpdate = null;

        // Toggle edit mode for a specific user
        function toggleEdit(button, userId) {
            const row = button.closest('tr');
            const userTypeSelect = row.querySelector(`#user-type-${userId}`);
            const stationSelect = row.querySelector(`#assigned-station-${userId}`);
            const saveUserTypeButton = row.querySelector('.save-user-type-button');
            const saveStationButton = row.querySelector('.save-station-button');
            const cancelButton = row.querySelector('.cancel-button');
            const deleteForm = row.querySelector('.delete-form'); // Get the delete form
            const deleteButton = row.querySelector('.delete-button'); // Get the delete button

            // Enable fields and show respective save button
            if (userTypeSelect.disabled) {
                userTypeSelect.disabled = false;
                saveUserTypeButton.style.display = 'inline-block';
            }
            if (stationSelect.disabled) {
                stationSelect.disabled = false;
                saveStationButton.style.display = 'inline-block';
            }

            cancelButton.style.display = 'inline-block';
            deleteForm.style.display = 'inline-block'; // Show the delete form
            deleteButton.style.display = 'inline-block'; // Show the delete button
            button.style.display = 'none'; // Hide the edit button
        }

        // Cancel edit mode and reset values
        function cancelEdit(button, userId) {
            const row = button.closest('tr');
            const userTypeSelect = row.querySelector(`#user-type-${userId}`);
            const stationSelect = row.querySelector(`#assigned-station-${userId}`);
            const saveUserTypeButton = row.querySelector('.save-user-type-button');
            const saveStationButton = row.querySelector('.save-station-button');
            const editButton = row.querySelector('.edit-button');
            const deleteButton = row.querySelector('.delete-button'); // Get the delete button

            // Reset to original values
            userTypeSelect.value = userTypeSelect.getAttribute('data-original-value');
            stationSelect.value = stationSelect.getAttribute('data-original-value');
            
            // Disable fields
            userTypeSelect.disabled = true;
            stationSelect.disabled = true;

            // Hide save and cancel buttons, show edit button
            saveUserTypeButton.style.display = 'none';
            saveStationButton.style.display = 'none';
            button.style.display = 'none'; // Hide the cancel button
            editButton.style.display = 'inline-block'; // Show the edit button
            deleteButton.style.display = 'none'; // Hide the delete button after cancel
        }

        // Show the update confirmation modal for a specific user
        function showUserTypeConfirmation(id) {
            userIdToUpdate = id; // Store the user ID to update

            // Show the confirmation modal for updating the user
            const modal = document.getElementById('updateUserTypeModal-' + id);
            modal.classList.remove('hidden');
        }

        // Show the update confirmation modal for a specific user
        function showAssignedStationConfirmation(id) {
            userIdToUpdate = id; // Store the user ID to update

            // Show the confirmation modal for updating the user
            const modal = document.getElementById('updateAssignedStationModal-' + id);
            modal.classList.remove('hidden');
        }

        // Show the delete confirmation modal for a specific user
        function showDeleteConfirmation(id) {
            userIdToUpdate = id; // Store the user ID to delete

            // Prevent default form submission
            event.preventDefault(); 

            // Show the confirmation modal for deleting the user
            const modal = document.getElementById('deleteConfirmationModal-' + id);
            modal.classList.remove('hidden');
        }

        // Hide the confirmation modal
        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            userIdToUpdate = null; // Reset the user ID
        }

        // Confirm and submit the update action
        function confirmUserTypeUpdate(id) {
            let form = document.getElementById('updateUserType-' + id);
            form.submit();
        }

        function confirmAssignedStationUpdate(id) {
            let form = document.getElementById('updateAssignedStation-' + id);
    
            if (form) {
                form.submit();  // Submit the form
            } else {
                console.error('Form not found for assigned station update: ', id);
            }
        }

        // Confirm and submit the delete action
        function confirmDelete(id, event) {
            event.preventDefault(); // Prevent default form submission behavior
            let form = document.getElementById('deleteForm-' + id);
            form.submit(); // Manually submit the form after confirmation
        }
    </script>
</x-sidebar-layout>