<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasig River Ferry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<body class="font-inter">
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar for desktop, collapsible on mobile -->
        <aside class="fixed top-0 left-0 w-52 h-screen px-3 py-6 bg-white border-r dark:bg-gray-900 dark:border-gray-700 flex flex-col justify-between">
            <!-- Logo Section -->
            <div>
                <div class="flex items-center justify-center mb-4">
                    <img class="w-28 h-28" src="/Images/PRF Logo.png" alt="PRF Logo">
                </div>

                <!-- Navigation Links -->
                <nav class="mt-4">
                    {{-- Dashboard --}}
                    @can('view-dashboard')
                        <x-nav-link href="/dashboard" :active="request()->is('dashboard')" class="flex items-center">
                            <img src="/Images/Dashboard Icon.svg" class="w-4 h-4" alt="Dashboard Icon">
                            <span class="mx-3 font-medium">Dashboard</span>
                        </x-nav-link>
                    @endcan

                    {{-- Boats --}}
                    @can('view-boats')
                        <x-nav-link href="/boats" :active="request()->is('boats')" class="flex items-center">
                            <img src="/Images/Boat Icon.svg" class="w-4 h-4" alt="Boat Icon">
                            <span class="mx-3 font-medium">Boats</span>
                        </x-nav-link>
                    @endcan

                    {{-- Map --}}
                    @can('view-map')
                        <x-nav-link href="/map" :active="request()->is('map')" class="flex items-center">
                            <img src="/Images/Map Icon.svg" class="w-4 h-4" alt="Map Icon">
                            <span class="mx-3 font-medium">Map</span>
                        </x-nav-link>
                    @endcan

                    {{-- Reports --}}
                    @can('view-reports')
                        <hr class="my-4 border-gray-200 dark:border-gray-600" />
                        <x-nav-link href="/reports" :active="request()->is('reports')" class="flex items-center">
                            <img src="/Images/reports.svg" class="w-4 h-4" alt="Reports Icon">
                            <span class="mx-3 font-medium">Reports</span>
                        </x-nav-link>
                    @endcan

                    {{-- Users (Super Admin Only) --}}
                    @can('view-users')
                        <x-nav-link href="/users" :active="request()->is('users')" class="flex items-center">
                            <img src="/Images/Add user.svg" class="w-4 h-4" alt="Users Icon">
                            <span class="mx-3 font-medium">Users</span>
                        </x-nav-link>
                    @endcan

                    {{-- Logout --}}
                    <div>
                        <hr class="my-4 border-gray-200 dark:border-gray-600" />
                        <form id="logout-form" method="POST" action="/logout">
                            @csrf
                            <button type="button" class="flex items-center pl-3.5 pr-16 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-300 transform rounded-md" onclick="showLogoutModal()">
                                <img src="/Images/Logout.svg" class="w-4 h-4" alt="Logout Icon">
                                <span class="mx-3 font-medium">Log Out</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </div>

            <!-- User Information at Bottom -->
            <a href="/profile" class="flex items-center px-1 mx-4 mt-4">
                <img class="object-cover rounded-full h-8 w-8" src="/Images/User Icon.jpg" alt="User Avatar" />
                <span class="mx-2 font-medium text-sm text-gray-800 dark:text-gray-200">{{ Auth::user()->first_name }} {{ Auth::user()->middle_name }} {{ Auth::user()->last_name }}</span>
            </a>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-auto bg-white ml-52">
            <header class="bg-white shadow-md rounded-b-md">
                <div class="p-4 md:p-6 text-center">
                    <h1 class="text-lg md:text-2xl font-bold tracking-tight text-gray-900">{{ $heading }}</h1>
                </div>
            </header>

            {{ $alert }}
            
            <div>
                {{ $slot }}
            </div>
        </main>
    </div>
    
    <!-- Logout Modal -->
    <x-modal-layout id="logout-modal" class="hidden">
        <h2 class="text-lg font-semibold mb-3">Confirm Logout</h2>
        <p class="mb-3">Are you sure you want to log out?</p>
        <div class="flex justify-end">
            <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-3 rounded mr-2" onclick="confirmLogout()">Log Out</button>
            <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-3 rounded" onclick="hideLogoutModal()">Cancel</button>     
        </div>
    </x-modal-layout>
    
    <script>
        function showLogoutModal() {
            document.getElementById('logout-modal').classList.remove('hidden');
        }
        
        function hideLogoutModal() {
            document.getElementById('logout-modal').classList.add('hidden');
        }
        
        function confirmLogout() {
            document.getElementById('logout-form').submit();
        }
    </script>
    
    @stack('scripts')
</body>
</html>
