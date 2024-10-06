<x-login-layout>
    <x-slot:header>
        Pasig River Ferry
    </x-slot:header>

    <x-slot:description>
        Welcome to the Pasig River Ferry Management System. Access reports, manage boats, and streamline operations with dashboards
    </x-slot:description>   

        <div class="mt-6">
            @if (session('status'))
                <x-alert-success>
                    {{ session('status') }}
                </x-alert-success>
            @endif

            @if (session('success'))
                <x-alert-success>
                    {{ session('success') }}
                </x-alert-success>
            @endif

            <x-slot:title>
                Sign in to access your account
            </x-slot:title>

            <form method="POST" action="/">
                @csrf
                <div>
                    <label for="email" class="block mb-2 text-sm">Email Address</label>

                    <input type="email" 
                           name="email" 
                           id="email" 
                           placeholder="example@example.com" 
                           :value="old('email')"
                           class="block w-full px-4 py-2 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 shadow-lg" />

                    <x-form-error name="email" />
                </div>

                <div class="mt-6">
                    <div class="flex justify-between mb-2">
                        <label for="password" class="text-sm">Password</label>
                        <a href="{{ 'forgot-password' }}" 
                           class="text-sm text-gray-400 focus:text-blue-500 hover:text-blue-500 hover:underline">Forgot password?</a>
                    </div>

                    <input type="password" 
                           name="password" 
                           id="password" 
                           placeholder="Your Password" 
                           class="block w-full px-4 py-2 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40 shadow-lg" />

                    <x-form-error name="password" />
                </div>

                <div class="mt-6">
                    <x-login-button>
                        Sign in
                    </x-login-button>
                </div>

            </form>
        </div>
</x-login-layout>