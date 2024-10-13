<div class="p-2 bg-white rounded-md shadow-md rounded-md max-h-96 overflow-y-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">#</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Address</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Contact #</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Profession</th>
                <th class="px-3 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Age</th>
                <th class="px-3 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Origin</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Date</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @php $currentNumber = 1; @endphp
            @foreach($manifests as $manifest)
                <tr class="hover:bg-gray-100">
                    <td class="px-6 py-4 text-xs text-gray-900">{{ $currentNumber++ }}</td>
                    <td class="px-6 py-4 text-xs text-gray-900">{{ $manifest->first_name }} {{ $manifest->middle_name }} {{ $manifest->last_name }}</td>
                    <td class="px-6 py-4 text-xs text-gray-900">{{ $manifest->address }}</td>
                    <td class="px-6 py-4 text-xs text-gray-900">{{ $manifest->contact_number }}</td>
                    <td class="px-6 py-4 text-xs text-gray-900">{{ $manifest->profession }}</td>
                    <td class="px-3 py-4 text-xs text-gray-900">{{ $manifest->age }}</td>
                    <td class="px-3 py-4 text-xs text-gray-900">{{ $manifest->gender }}</td>
                    <td class="px-6 py-4 text-xs text-gray-900">{{ $manifest->origin }}</td>
                    <td class="px-6 py-4 text-xs text-gray-900">{{ $manifest->destination }}</td>
                    <td class="px-6 py-4 text-xs text-gray-900">{{ $manifest->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>