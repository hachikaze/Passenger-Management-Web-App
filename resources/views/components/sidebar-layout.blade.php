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
    <div class="flex">
    <aside class="sticky top-0 w-64 h-screen px-4 py-8 overflow-y-auto bg-white border-r dark:bg-gray-900 dark:border-gray-700 flex flex-col justify-between rounded-r-md">
        <!-- Logo Section -->
        <div>
            <div class="flex items-center justify-center">
                <img class="w-32 h-32" src="/Images/PRF Logo.png" alt="PRF Logo">
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6">
                <x-nav-link href="/dashboard" :active="request()->is('dashboard')">
                    <img src="/Images/Dashboard Icon.svg" class="w-5 h-5" alt="Dashboard Icon">
                    <span class="mx-4 font-medium">Dashboard</span>
                </x-nav-link>

                <x-nav-link href="/boats" :active="request()->is('boats')">
                    <img src="/Images/Boat Icon.svg" class="w-5 h-5" alt="Boat Icon">
                    <span class="mx-4 font-medium">Boats</span>
                </x-nav-link>

                <x-nav-link href="/map" :active="request()->is('map')">
                    <img src="/Images/Map Icon.svg" class="w-5 h-5" alt="Map Icon">
                    <span class="mx-4 font-medium">Map</span>
                </x-nav-link>

                @can('admin-view')
                    <hr class="my-6 border-gray-200 dark:border-gray-600" />
                    
                    <x-nav-link href="/reports" :active="request()->is('reports')">
                        <img src="/Images/reports.svg" class="w-5 h-5" alt="Reports Icon">
                        <span class="mx-4 font-medium">Reports</span>
                    </x-nav-link>

                    <x-nav-link href="/users" :active="request()->is('users')">
                        <img src="/Images/Add user.svg" class="w-5 h-5" alt="Users Icon">
                        <span class="mx-4 font-medium">Users</span>
                    </x-nav-link>
                @endcan

                <div>
                    <hr class="my-6 border-gray-200 dark:border-gray-600" />

                    <!-- Logout Button -->
                    <form id="logout-form" method="POST" action="/logout">
                        @csrf
                        <button type="button" class="flex items-center pl-2 pr-24 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-300 transform rounded-md" onclick="showLogoutModal()">
                            <img src="/Images/Logout.svg" class="w-5 h-5" alt="Logout Icon">
                            <span class="mx-4 font-medium">Log Out</span>
                        </button>
                    </form>
                </div>
            </nav>
        </div>

        <!-- User Information at Bottom -->
        <a href="/profile" class="flex items-center px-4 -mx-2 mt-6">
            <img class="object-cover mx-2 rounded-full h-9 w-9" src="/Images/User Icon.jpg" alt="User Avatar" />
            <span class="mx-2 font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->first_name }} {{ Auth::user()->middle_name }} {{ Auth::user()->last_name }}</span>
        </a>
    </aside>
        
        <main class="flex-auto bg-white">
            <header class="bg-white shadow-md rounded-b-md">
                <div class="p-8 text-center">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $heading }}</h1>
                </div>
            </header>

            {{ $alert }}
            
            <div>
                {{ $slot }}
            </div>
        </main>
    </div>
    
    <!-- Logout Modal -->
    <x-modal-layout id="logout-modal">
        <h2 class="text-xl font-semibold mb-4">Confirm Logout</h2>
        <p class="mb-4">Are you sure you want to log out?</p>
        <div class="flex justify-end">
            <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded mr-2" onclick="confirmLogout()">Log Out</button>
            <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded" onclick="hideLogoutModal()">Cancel</button>     
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
