@if (session('success'))
    <x-alert-bottom-success>
        {{ session('success') }}
    </x-alert-bottom-success>
@endif
<x-alert-layout>
    <div class="w-full max-w-lg px-8 py-6 rounded-lg shadow-lg bg-white flex flex-col text-center space-y-6 overflow-hidden">
        <div class="flex flex-col items-center space-y-4">
            <svg fill="#0D9488" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" class="w-14 mb-4">
                <path d="M15.5 3c-7.456 0-13.5 6.044-13.5 13.5s6.044 13.5 13.5 13.5 13.5-6.044 13.5-13.5-6.044-13.5-13.5-13.5zM15.5 27c-5.799 0-10.5-4.701-10.5-10.5s4.701-10.5 10.5-10.5 10.5 4.701 10.5 10.5-4.701 10.5-10.5 10.5zM15.5 10c-0.828 0-1.5 0.671-1.5 1.5v5.062c0 0.828 0.672 1.5 1.5 1.5s1.5-0.672 1.5-1.5v-5.062c0-0.829-0.672-1.5-1.5-1.5zM15.5 20c-0.828 0-1.5 0.672-1.5 1.5s0.672 1.5 1.5 1.5 1.5-0.672 1.5-1.5-0.672-1.5-1.5-1.5z"/>
            </svg>
            <h2 class="text-2xl text-teal-700 font-bold">Complete email verification to sign in</h2>
            <p class="text-sm text-gray-500">Click the button below to send the email verification.</p>
        </div>

        <div class="flex flex-col items-center">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-login-button class="bg-teal-400 hover:bg-teal-600 text-white px-4 py-1 rounded-md">
                    Verify
                </x-login-button>
            </form>
        </div>
    </div>
</x-alert-layout>
