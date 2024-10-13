<x-login-layout>
    <x-slot:header>
        Pasig River Ferry
    </x-slot:header>

    <x-slot:description>
        Welcome to the Pasig River Ferry Management System. Access reports, manage boats, and streamline operations with dashboards
    </x-slot:description>   

        <div class="mt-4">
            @if (session('success'))
                <x-alert-success>
                    {{ session('success') }}
                </x-alert-success>
            @endif

            @if (session('error'))
                <x-alert-error>
                    {{ session('error') }}
                </x-alert-error>
            @endif

            <x-slot:title>
                Reset Password
            </x-slot:title>

            <form method="POST" action="{{ 'forgot-password-post' }}">
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
                    <x-login-button>
                        Submit
                    </x-login-button>
                </div>

            </form>
        </div>
</x-login-layout>