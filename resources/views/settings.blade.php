<x-sidebar-layout>
    <x-slot:heading>
        Settings
    </x-slot:heading>

    <h1>This is the Settings page.</h1>
    <x-slot:alert>
        @if (session('success'))
            <x-alert-bottom-success>
                {{ session('success') }}
            </x-alert-bottom-success>
        @endif
    </x-slot:alert>
</x-sidebar-layout>