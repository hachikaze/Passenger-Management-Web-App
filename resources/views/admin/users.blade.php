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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Contact Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Verified</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">User Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Assigned Station</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th> <!-- New Status Column -->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->contact_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email_verified_at ? 'Yes' : 'No' }}</td>
                        
                        <!-- User Type Dropdown with Save button -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <form action="{{ route('update-user-type') }}" method="POST" class="inline-block" id="updateUserType-{{ $user->id }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <select name="user_type" id="user-type-{{ $user->id }}" class="form-select" disabled data-original-value="{{ $user->user_type }}">
                                    <option value="Admin" {{ $user->user_type == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="Aide" {{ $user->user_type == 'Aide' ? 'selected' : '' }}>Aide</option>
                                    <option value="Operator" {{ $user->user_type == 'Operator' ? 'selected' : '' }}>Operator</option>
                                </select>
                                <button type="button" class="ml-2 p-2 bg-green-500 rounded-md text-white save-user-type-button" style="display: none;" onclick="showUserTypeConfirmation('{{ $user->id }}')">Save</button>
                            </form>
                        </td>

                        <!-- Assigned Station Dropdown with Save button -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <form action="{{ route('update-assigned-station') }}" method="POST" class="inline-block" id="updateAssignedStation-{{ $user->id }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <select name="assigned_station" id="assigned-station-{{ $user->id }}" class="form-select" disabled data-original-value="{{ $user->assigned_station }}">
                                    <option value="None">None</option>
                                    @foreach($stations as $station_name)
                                        <option value="{{ $station_name }}" {{ $user->assigned_station == $station_name ? 'selected' : '' }}>{{ $station_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="ml-2 p-2 bg-green-500 rounded-md text-white save-station-button" style="display: none;" onclick="showAssignedStationConfirmation('{{ $user->id }}')">Save</button>
                            </form>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                                $session = $sessions->firstWhere('user_id', $user->id);
                            @endphp
                            @if ($session && $session->is_online)
                                <span class="text-green-500">Online</span>
                            @else
                                <span class="text-red-500">Offline</span>
                            @endif
                        </td>   

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <!-- Edit Button -->
                            <button class="p-2 bg-white rounded-md hover:bg-gray-300 transition duration-300 edit-button" onclick="toggleEdit(this, '{{ $user->id }}')">Edit</button>
                            
                            <!-- Cancel Button -->
                            <button class="ml-2 p-2 bg-gray-300 rounded-md text-white cancel-button" onclick="cancelEdit(this, '{{ $user->id }}')" style="display: none;">Cancel</button>

                            <!-- Delete Button (Initially Hidden) -->
                            <form id="deleteForm-{{ $user->id }}" action="{{ route('delete-user', $user->id) }}" method="POST" class="inline-block delete-form" style="display: none;">
                                @csrf
                                @method('DELETE')
                                <button class="ml-2 p-2 bg-red-500 rounded-md text-white delete-button" onclick="showDeleteConfirmation('{{ $user->id }}', event)" style="display: none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
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