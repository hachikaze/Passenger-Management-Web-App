<x-sidebar-layout>
    <x-slot:heading>
        Register
    </x-slot:heading>
    
    <form method="POST" action="/register">
        @csrf

        <div class="border-solid container mx-auto bg-white shadow-lg rounded-lg p-6 h-100">
            <div class="space-y-12">
                <div class="border-b border-gray-900/10 pb-12">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <x-form-field>
                            <x-form-label for="first_name">First Name</x-form-label>
                            <div class="mt-2">
                                <x-form-input name="first_name" id="first_name" />
                                <x-form-error name="first_name" />
                            </div>
                        </x-form-field>

                        <x-form-field>
                            <x-form-label for="last_name">Last Name</x-form-label>
                            <div class="mt-2">
                                <x-form-input name="last_name" id="last_name" />
                                <x-form-error name="last_name" />
                            </div>
                        </x-form-field>

                        <x-form-field>
                            <x-form-label for="email">Email</x-form-label>
                            <div class="mt-2">
                                <x-form-input name="email" id="email" type="email" />
                                <x-form-error name="email" />
                            </div>
                        </x-form-field>

                        <x-form-field>
                            <x-form-label for="password">Password</x-form-label>
                            <div class="mt-2">
                                <x-form-input name="password" id="password" type="password" />
                                <x-form-error name="password" />
                            </div>
                        </x-form-field>

                        <x-form-field>
                            <x-form-label for="password_confirmation">Confirm Password</x-form-label>
                            <div class="mt-2">
                                <x-form-input name="password_confirmation" id="password_confirmation" type="password" />
                                <x-form-error name="password_confirmation" />
                            </div>
                        </x-form-field>

                        <x-form-field>
                            <x-form-label for="user_type">User Type</x-form-label>
                            <div class="mt-2">
                                <x-dropdown-input name="user_type" id="user_type" onchange="handleUserTypeChange()">
                                    <option value=""></option>
                                    <option value="Admin">Admin</option>
                                    <option value="Operator">Operator</option>
                                    <option value="Aide">Aide</option>
                                </x-dropdown-input>
                                <x-form-error name="user_type" />
                            </div>
                        </x-form-field>

                        <!-- This is the dropdown for assigned_station, initially hidden -->
                        <x-form-field id="assigned_station_field" class="hidden">
                            <x-form-label for="assigned_station">Assigned Station</x-form-label>
                            <div class="mt-2">
                                <x-dropdown-input name="assigned_station" id="assigned_station">
                                    <!-- Populate with your available stations -->
                                    <option value="None">None</option>
                                    @foreach($stations as $station_name)
                                        <option value="{{ $station_name }}">{{ $station_name }}</option>
                                    @endforeach
                                </x-dropdown-input>
                                <x-form-error name="assigned_station" />
                            </div>
                        </x-form-field>

                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <x-form-button>Register</x-form-button>
            </div>
        </div>
        
    </form>

    <x-slot:alert>
        @if (session('success'))
            <x-alert-bottom-success>
                {{ session('success') }}
            </x-alert-bottom-success>
        @endif
    </x-slot:alert>

    <!-- Script to handle the showing and hiding of the assigned station dropdown -->
    <script>
        function handleUserTypeChange() {
            var userType = document.getElementById('user_type').value;
            var assignedStationField = document.getElementById('assigned_station_field');

            // Show the "Assigned Station" dropdown if the selected user type is 'Aide'
            if (userType === 'Operator') {
                assignedStationField.classList.remove('hidden');
            } else {
                assignedStationField.classList.add('hidden');
            }
        }

        // Run on page load in case the form is pre-filled
        document.addEventListener('DOMContentLoaded', function() {
            handleUserTypeChange(); // Check user_type on page load
        });
    </script>
</x-sidebar-layout>
