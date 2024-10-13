<x-sidebar-layout>
    <x-slot:heading>
        <h1 class="text-3xl font-bold text-gray-800 text-center">Register a New User</h1>
    </x-slot:heading>

    <form id="registerForm" method="POST" action="/register" class="flex justify-center">
        @csrf

        <!-- Main Container for Form -->
        <div class="my-8 p-6 bg-gray-200 shadow-lg rounded w-full max-w-lg"> <!-- Set max-width to lg (around 32rem) and center -->

            <div class="mb-6 text-center">
                <p class="text-gray-500">Please fill out the form to register a new user in the system.</p>
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 gap-6 bg-white p-4 rounded-md">
                <!-- First Name -->
                <x-form-field>
                    <x-form-label for="first_name">First Name</x-form-label>
                    <div class="mt-2">
                        <x-form-input name="first_name" id="first_name" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full" />
                        <x-form-error name="first_name" />
                    </div>
                </x-form-field>

                <!-- Last Name -->
                <x-form-field>
                    <x-form-label for="last_name">Last Name</x-form-label>
                    <div class="mt-2">
                        <x-form-input name="last_name" id="last_name" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full" />
                        <x-form-error name="last_name" />
                    </div>
                </x-form-field>

                <!-- Email -->
                <x-form-field>
                    <x-form-label for="email">Email</x-form-label>
                    <div class="mt-2">
                        <x-form-input name="email" id="email" type="email" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full" />
                        <x-form-error name="email" />
                    </div>
                </x-form-field>

                <!-- Password -->
                <x-form-field>
                    <x-form-label for="password">Password</x-form-label>
                    <div class="mt-2">
                        <x-form-input name="password" id="password" type="password" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full" />
                        <x-form-error name="password" />
                    </div>
                </x-form-field>

                <!-- Confirm Password -->
                <x-form-field>
                    <x-form-label for="password_confirmation">Confirm Password</x-form-label>
                    <div class="mt-2">
                        <x-form-input name="password_confirmation" id="password_confirmation" type="password" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full" />
                        <x-form-error name="password_confirmation" />
                    </div>
                </x-form-field>

                <!-- User Type -->
                <x-form-field>
                    <x-form-label for="user_type">User Type</x-form-label>
                    <div class="mt-2">
                        <x-dropdown-input name="user_type" id="user_type" onchange="handleUserTypeChange()" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                            <option value=""></option>
                            <option value="Admin">Admin</option>
                            <option value="Operator">Ferry Operator</option>
                            <option value="Aide">Ferry Aide</option>
                            <option value="Boat">Boat Manager</option>
                        </x-dropdown-input>
                        <x-form-error name="user_type" />
                    </div>
                </x-form-field>

                <!-- Assigned Station (visible only if 'Operator' is selected) -->
                <x-form-field id="assigned_station_field" class="hidden">
                    <x-form-label for="assigned_station">Assigned Station</x-form-label>
                    <div class="mt-2">
                        <x-dropdown-input name="assigned_station" id="assigned_station" class="rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                            <option value="None">None</option>
                            @foreach($stations as $station_name)
                                <option value="{{ $station_name }}">{{ $station_name }}</option>
                            @endforeach
                        </x-dropdown-input>
                        <x-form-error name="assigned_station" />
                    </div>
                </x-form-field>
            </div>

            <!-- Register Button -->
            <div class="mt-8 flex justify-between">
                <button type="button" onclick="window.history.back()" class="px-4 py-2 bg-gray-500 text-sm text-white rounded-lg shadow hover:bg-gray-700 transition duration-300">
                    Cancel
                </button>

                <button type="button" onclick="showModal('registerConfirmationModal')" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-500 text-sm text-white rounded-lg shadow hover:from-blue-600 hover:to-indigo-600 transition duration-300">
                    Add User
                </button>
            </div>
        </div>
    </form>

    <!-- Confirmation Modal -->
    <x-modal-layout id="registerConfirmationModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <h2 class="text-xl font-bold mb-4 text-gray-700 text-center">Confirm Registration</h2>
        <p class="mb-6 text-gray-500 text-center">Are you sure you want to register this user?</p>
        
        <!-- Button Group -->
        <div class="flex justify-center space-x-4">
            <button class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-700 transition duration-300" onclick="submitForm()">Yes</button> <!-- Increased button padding for better sizing -->
            <button class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700 transition duration-300" onclick="hideModal('registerConfirmationModal')">No</button>
        </div>
    </x-modal-layout>

    <x-slot:alert>
        @if (session('success'))
            <x-alert-bottom-success>
                {{ session('success') }}
            </x-alert-bottom-success>
        @endif
    </x-slot:alert>

    <!-- Script to handle user type changes and modal functionality -->
    <script>
        function handleUserTypeChange() {
            var userType = document.getElementById('user_type').value;
            var assignedStationField = document.getElementById('assigned_station_field');
            assignedStationField.classList.toggle('hidden', userType !== 'Operator');
        }

        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function submitForm() {
            document.getElementById('registerForm').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            handleUserTypeChange();
        });
    </script>
</x-sidebar-layout>
