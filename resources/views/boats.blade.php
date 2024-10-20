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
                        <th class="px-6 py-3 text-left text-xs text-center font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs text-center font-medium text-gray-500 uppercase tracking-wider">Boat Name</th>
                        <th class="px-6 py-3 text-left text-xs text-center font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs text-center font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 text-left text-xs text-center font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($boats as $boat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-xs text-center text-gray-900 border-r border-gray-200">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-xs text-center text-gray-900 border-r border-gray-200">{{ $boat->boat_name }}</td>
                            <td class="px-6 py-4 text-xs text-center text-gray-900 border-r border-gray-200">{{ $boat->max_capacity }}</td>
                            <td class="px-6 py-4 text-xs text-center text-gray-900 border-r border-gray-2000">
                                <form action="{{ route('update-status') }}" method="POST" class="inline-block flex justify-center items-center" id="updateStatusForm-{{ $boat->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="id" value="{{ $boat->id }}">
                                    <select name="status" id="status-{{ $boat->id }}" class="rounded-md p-1 form-select" disabled data-original-value="{{ $boat->status }}" style="background-color: {{ $boat->status === 'ACTIVE' ? '#d1fae5' : ($boat->status === 'INACTIVE' ? '#fee2e2' : '#fef9c3') }}">
                                        <option value="ACTIVE" {{ $boat->status == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                                        <option value="INACTIVE" {{ $boat->status == 'INACTIVE' ? 'selected' : '' }}>INACTIVE</option>
                                        <option value="MAINTENANCE" {{ $boat->status == 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE</option>
                                    </select>
                                    <button type="button" class="ml-2 px-2 py-1 bg-white rounded-md text-white save-button" style="display: none;" onclick="showUpdateConfirmation('{{ $boat->id }}')">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1716 1C18.702 1 19.2107 1.21071 19.5858 1.58579L22.4142 4.41421C22.7893 4.78929 23 5.29799 23 5.82843V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H18.1716ZM4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21L5 21L5 15C5 13.3431 6.34315 12 8 12L16 12C17.6569 12 19 13.3431 19 15V21H20C20.5523 21 21 20.5523 21 20V6.82843C21 6.29799 20.7893 5.78929 20.4142 5.41421L18.5858 3.58579C18.2107 3.21071 17.702 3 17.1716 3H17V5C17 6.65685 15.6569 8 14 8H10C8.34315 8 7 6.65685 7 5V3H4ZM17 21V15C17 14.4477 16.5523 14 16 14L8 14C7.44772 14 7 14.4477 7 15L7 21L17 21ZM9 3H15V5C15 5.55228 14.5523 6 14 6H10C9.44772 6 9 5.55228 9 5V3Z" fill="#37e65a"></path>
                                            </g>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 text-sm text-center text-gray-900 border-r border-gray-200">
                                <button class="px-2 py-1 bg-white rounded-md edit-button" onclick="toggleEdit(this, '{{ $boat->id }}')">
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
                                <button class="px-2 py-1 bg-white rounded-md text-white cancel-button" onclick="cancelEdit(this, '{{ $boat->id }}')" style="display: none;">
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
                                <form id="deleteForm-{{ $boat->id }}" action="{{ route('delete-boat', $boat->id) }}" method="POST" class="inline-block delete-form" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="px-2 py-1 bg-white rounded-md text-white delete-button" onclick="showDeleteConfirmation('{{ $boat->id }}')">
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

                    <form action="{{ route('add-boat') }}" method="POST" id="addBoatForm">
                        @csrf
                        <tr id="placeholderRow" style="display: none;">
                            <td colspan="6" class="px-6 py-4"></td>
                        </tr>
                        <tr id="addBoatRow" style="display: none;">
                            <td class="px-6 py-3 text-center text-xs text-gray-900 border-r border-gray-200">#</td>
                            <td class="px-6 py-3 text-center text-xs text-gray-900 border-r border-gray-200">
                                <input type="text" class="form-control" name="boat_name" placeholder="Boat Name">
                            </td>
                            <td class="px-6 py-3 text-center text-xs text-gray-900 border-r border-gray-200">
                                <input type="number" class="form-control w-24" name="max_capacity" placeholder="Max Cap.">
                            </td>
                            <td class="px-6 py-3 text-center text-xs text-gray-900 border-r border-gray-200">
                                <select name="status" class="form-select rounded-md p-1">
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                    <option value="MAINTENANCE">MAINTENANCE</option>
                                </select>
                            </td>
                            <td class="py-3 text-center text-sm text-gray-900 border-r border-gray-200">
                                <button type="button" class="px-2 py-1 text-sm bg-white rounded-md text-white save-button" onclick="showSaveConfirmation()">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1716 1C18.702 1 19.2107 1.21071 19.5858 1.58579L22.4142 4.41421C22.7893 4.78929 23 5.29799 23 5.82843V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H18.1716ZM4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21L5 21L5 15C5 13.3431 6.34315 12 8 12L16 12C17.6569 12 19 13.3431 19 15V21H20C20.5523 21 21 20.5523 21 20V6.82843C21 6.29799 20.7893 5.78929 20.4142 5.41421L18.5858 3.58579C18.2107 3.21071 17.702 3 17.1716 3H17V5C17 6.65685 15.6569 8 14 8H10C8.34315 8 7 6.65685 7 5V3H4ZM17 21V15C17 14.4477 16.5523 14 16 14L8 14C7.44772 14 7 14.4477 7 15L7 21L17 21ZM9 3H15V5C15 5.55228 14.5523 6 14 6H10C9.44772 6 9 5.55228 9 5V3Z" fill="#37e65a"></path>
                                        </g>
                                    </svg>
                                </button>
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

    <div class="container mx-auto">

        <div class="my-4 mx-40 p-3 bg-gray-200 shadow-md rounded">
            <h2 class="text-xl font-bold mb-4">
                Daily Boat Status Counts - {{ $year }}/{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}
            </h2>

            <div class="flex-1 p-1 bg-white rounded-md shadow-md">
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="px-6 py-3 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">Day</th>
                                <th class="px-6 py-3 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">Active Boats</th>
                                <th class="px-6 py-3 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">Inactive Boats</th>
                                <th class="px-6 py-3 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">Under Repair Boats</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($dailyData as $data)
                                <tr class="hover:bg-gray-50">
                                    <td class="text-sm text-center text-gray-900 border-r border-gray-200 px-6 py-3">{{ $data['day'] }}</td>
                                    <td class="text-sm text-center text-gray-900 border-r border-gray-200 px-6 py-3">{{ $data['active_count'] }}</td>
                                    <td class="text-sm text-center text-gray-900 border-r border-gray-200 px-6 py-3">{{ $data['inactive_count'] }}</td>
                                    <td class="text-sm text-center text-gray-900 border-r border-gray-200 px-6 py-3">{{ $data['maintenance_count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mb-4 mx-40 p-3 bg-gray-200 shadow-md rounded">
            <h2 class="text-xl font-bold mb-4">
                Boat Status Logs - {{ $year }}/{{ $month }}
            </h2>

            <div class="flex-1 p-1 bg-white rounded-md shadow-md">
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="px-6 underline underline-offset-2 py-3 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                                        ID
                                        @if(request('sort') == 'id')
                                            @if(request('direction') == 'asc')
                                                ↑
                                            @else
                                                ↓
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 underline underline-offset-2 py-3 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'boat_id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                                        Boat ID
                                        @if(request('sort') == 'boat_id')
                                            @if(request('direction') == 'asc')
                                                ↑
                                            @else
                                                ↓
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 underline underline-offset-2 py-3 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                                        Date
                                        @if(request('sort') == 'date')
                                            @if(request('direction') == 'asc')
                                                ↑
                                            @else
                                                ↓
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">
                                    Created At
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-normal text-gray-500 uppercase tracking-wider">
                                    Updated At
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($statusLogs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="text-sm text-center text-gray-900 border-r border-gray-200 px-6 py-3">{{ $log->id }}</td>
                                    <td class="text-sm text-center text-gray-900 border-r border-gray-200 px-6 py-3">{{ $log->boat_id }}</td>
                                    <td class="text-sm text-center text-gray-900 border-r border-gray-200 px-6 py-3">{{ $log->status }}</td>
                                    <td class="text-sm text-center text-gray-900 border-r border-gray-200 px-6 py-3">{{ $log->date }}</td>
                                    <td class="text-sm text-center text-gray-900 border-r border-gray-200 px-6 py-3">{{ $log->created_at->format('H:i:s') }}</td>
                                    <td class="text-sm text-center text-gray-900 border-r border-gray-200 px-6 py-3">{{ $log->updated_at->format('H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
