<x-sidebar-layout>
    <x-slot:heading>
        Boats
    </x-slot:heading>

    <x-slot:alert>
        @if (session('success'))
            <x-alert-bottom-success>
                {{ session('success') }}
            </x-alert-bottom-success>
        @endif

        @if (session('error'))
            <x-alert-bottom-error>
                {{ session('error') }}
            </x-alert-bottom-error>
        @endif
    </x-slot:alert>

    <div class="my-4 mx-40 p-3 bg-gray-200 shadow-md rounded">
        <h1 class="text-xl font-bold pb-4">Operational Boat Status</h1>
        <div class="p-1 bg-white rounded-md shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Boat Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($boats as $boat)
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 text-xs text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-xs text-gray-900">{{ $boat->boat_name }}</td>
                            <td class="px-6 py-4 text-xs text-gray-900">{{ $boat->max_capacity }}</td>
                            <td class="px-6 py-4 text-xs text-gray-900">
                                <form action="{{ route('update-status') }}" method="POST" class="inline-block" id="updateStatusForm-{{ $boat->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="id" value="{{ $boat->id }}">
                                    <select name="status" id="status-{{ $boat->id }}" class="form-select" disabled data-original-value="{{ $boat->status }}" style="background-color: {{ $boat->status === 'ACTIVE' ? '#d1fae5' : ($boat->status === 'INACTIVE' ? '#fee2e2' : '#fef9c3') }}">
                                        <option value="ACTIVE" {{ $boat->status == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                                        <option value="INACTIVE" {{ $boat->status == 'INACTIVE' ? 'selected' : '' }}>INACTIVE</option>
                                        <option value="MAINTENANCE" {{ $boat->status == 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE</option>
                                    </select>
                                    <button type="button" class="ml-2 p-2 bg-green-500 rounded-md text-white save-button" style="display: none;" onclick="showUpdateConfirmation('{{ $boat->id }}')">Save</button>
                                </form>
                            </td>
                            <td class="py-4 text-sm text-gray-900">
                                <button class="px-2 py-1 bg-white rounded-md hover:bg-gray-300 transition duration-300 edit-button" onclick="toggleEdit(this, '{{ $boat->id }}')">Edit</button>
                                <button class="px-2 py-1 bg-gray-500 rounded-md text-white cancel-button" onclick="cancelEdit(this, '{{ $boat->id }}')" style="display: none;">Cancel</button>
                                <form id="deleteForm-{{ $boat->id }}" action="{{ route('delete-boat', $boat->id) }}" method="POST" class="inline-block delete-form" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="px-2 py-1 bg-red-500 rounded-md text-white delete-button" onclick="showDeleteConfirmation('{{ $boat->id }}')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    <form action="{{ route('add-boat') }}" method="POST" id="addBoatForm">
                        @csrf
                        <tr id="placeholderRow" style="display: none;">
                            <td colspan="6" class="px-6 py-4"></td>
                        </tr>
                        <tr id="addBoatRow" style="display: none;">
                            <td class="px-6 py-3 text-xs text-gray-900">#</td>
                            <td class="px-6 py-3 text-xs text-gray-900">
                                <input type="text" class="form-control" name="boat_name" placeholder="Boat Name">
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-900">
                                <input type="number" class="form-control w-24" name="max_capacity" placeholder="Max Cap.">
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-900">
                                <select name="status" class="form-select w-32">
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                    <option value="MAINTENANCE">MAINTENANCE</option>
                                </select>
                            </td>
                            <td class="py-3 text-sm text-gray-900">
                                <button type="button" class="px-2 py-1 text-sm bg-green-500 rounded-md text-white save-button" onclick="showSaveConfirmation()">Save</button>
                            </td>
                        </tr>
                    </form>
                </tbody>
            </table>
        </div>

        <x-form-error name="boat_name" />
        <x-form-error name="max_capacity" />
        <x-form-error name="status" />
        <button class="px-2 py-1 mt-3 text-sm bg-white rounded-md hover:bg-green-500 transition duration-300" onclick="toggleAddBoatRow()" id="addNewBoatButton">New Boat</button>
    </div>

    <!-- Confirmation Modals -->
    @foreach($boats as $boat)
        <!-- Update Confirmation Modal -->
        <x-modal-layout id="updateConfirmationModal-{{ $boat->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <h2 class="text-xl font-bold mb-4">Confirm Update</h2>
            <p class="mb-4">Are you sure you want to update the status of this boat?</p>
            <div class="flex justify-end">
                <button class="p-2 bg-green-500 rounded-md text-white mr-2 hover:bg-green-700" onclick="confirmUpdate('{{ $boat->id }}')">Yes</button>
                <button class="p-2 bg-gray-500 rounded-md text-white hover:bg-gray-700" onclick="hideModal('updateConfirmationModal-{{ $boat->id }}')">No</button>
            </div>
        </x-modal-layout>

        <!-- Delete Confirmation Modal -->
        <x-modal-layout id="deleteConfirmationModal-{{ $boat->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <h2 class="text-xl font-bold mb-4">Confirm Delete</h2>
            <p class="mb-4">Are you sure you want to delete this boat?</p>
            <div class="flex justify-end">
                <button class="p-2 bg-red-500 rounded-md text-white mr-2 hover:bg-red-700" onclick="confirmDelete('{{ $boat->id }}')">Yes</button>
                <button class="p-2 bg-gray-500 rounded-md text-white hover:bg-gray-700" onclick="hideModal('deleteConfirmationModal-{{ $boat->id }}')">No</button>
            </div>
        </x-modal-layout>
    @endforeach

    <!-- Save Confirmation Modal -->
    <x-modal-layout id="saveConfirmationModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <h2 class="text-xl font-bold mb-4">Confirm Add</h2>
        <p class="mb-4">Are you sure you want to add this boat?</p>
        <div class="flex justify-end">
            <button id="confirmSaveButton" class="p-2 bg-green-500 rounded-md text-white mr-2 hover:bg-green-700">Yes</button>
            <button id="cancelSaveButton" class="p-2 bg-gray-500 rounded-md text-white hover:bg-gray-700">No</button>
        </div>
    </x-modal-layout>

    <script>
        function toggleEdit(button, id) {
            let row = button.closest('tr');
            let select = row.querySelector('select');
            let editButton = row.querySelector('.edit-button');
            let saveButton = row.querySelector('form .save-button');
            let cancelButton = row.querySelector('.cancel-button');
            let deleteForm = row.querySelector('.delete-form');

            select.disabled = false;
            saveButton.style.display = 'inline-block';
            editButton.style.display = 'none';
            cancelButton.style.display = 'inline-block';
            deleteForm.style.display = 'inline-block';
        }

        function cancelEdit(button, id) {
            let row = button.closest('tr');
            let select = row.querySelector('select');
            let editButton = row.querySelector('.edit-button');
            let saveButton = row.querySelector('form .save-button');
            let cancelButton = row.querySelector('.cancel-button');
            let deleteForm = row.querySelector('.delete-form');

            select.value = select.getAttribute('data-original-value');
            select.disabled = true;
            saveButton.style.display = 'none';
            editButton.style.display = 'inline-block';
            cancelButton.style.display = 'none';
            deleteForm.style.display = 'none';
        }

        function toggleAddBoatRow() {
            var addBoatRow = document.getElementById("addBoatRow");
            var placeholderRow = document.getElementById("placeholderRow");
            var addNewBoatButton = document.getElementById("addNewBoatButton");

            if (addBoatRow.style.display === "none") {
                addBoatRow.style.display = "table-row";
                placeholderRow.style.display = "none";
                addNewBoatButton.textContent = 'Cancel';
            } else {
                addBoatRow.style.display = "none";
                placeholderRow.style.display = "table-row";
                addNewBoatButton.textContent = 'New Boat';
            }
        }

        function showModal(modalId) {
            let modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
        }

        function hideModal(modalId) {
            let modal = document.getElementById(modalId);
            modal.classList.add('hidden');
        }

        function showUpdateConfirmation(id) {
            showModal('updateConfirmationModal-' + id);
        }

        function showDeleteConfirmation(id) {
            showModal('deleteConfirmationModal-' + id);
        }

        function confirmUpdate(id) {
            let form = document.getElementById('updateStatusForm-' + id);
            form.submit();
        }

        function showSaveConfirmation() {
            showModal('saveConfirmationModal');
        }

        document.getElementById('addBoatForm').addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById('saveConfirmationModal').classList.remove('hidden');
        });

        document.getElementById('confirmSaveButton').addEventListener('click', function() {
            document.getElementById('addBoatForm').submit();
        });

        document.getElementById('cancelSaveButton').addEventListener('click', function() {
            document.getElementById('saveConfirmationModal').classList.add('hidden');
        });
        
        function confirmDelete(id) {
            let form = document.getElementById('deleteForm-' + id);
            form.submit();
        }

        // Add this function to handle status color change
        function updateDropdownColor(select) {
            const status = select.value;
            if (status === 'ACTIVE') {
                select.classList.add('bg-green-200');
                select.classList.remove('bg-red-200', 'bg-yellow-200');
            } else if (status === 'INACTIVE') {
                select.classList.add('bg-red-200');
                select.classList.remove('bg-green-200', 'bg-yellow-200');
            } else if (status === 'MAINTENANCE') {
                select.classList.add('bg-yellow-200');
                select.classList.remove('bg-green-200', 'bg-red-200');
            }
        }

        // Apply initial color to all dropdowns on page load
        document.querySelectorAll('select[name="status"]').forEach(select => {
            updateDropdownColor(select);
        });

        // Add event listener for status change
        document.querySelectorAll('select[name="status"]').forEach(select => {
            select.addEventListener('change', function() {
                updateDropdownColor(this);
            });
        });
    </script>
</x-sidebar-layout>
