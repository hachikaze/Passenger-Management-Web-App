<x-alert-layout>
    @if (session('message'))
        <div>{{ session('message') }}</div>
    @endif
    <div class="w-full max-w-lg px-8 py-6 rounded-lg shadow-lg bg-white flex flex-col text-center space-y-6 overflow-hidden">
        <div class="flex flex-col items-center space-y-4">
            <img src="Images/notice.svg" class="w-14 mb-4" alt="Notice">
            <h2 class="text-2xl text-blue-500 font-bold">Complete email verification to sign in</h2>
            <p class="text-sm text-gray-700">Check your email or Click the button below to send the email verification.</p>
        </div>

        <div class="flex flex-col items-center">
            
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-login-button>
                    Resend
                </x-login-button>
            </form>
        </div>
    </div>
</x-alert-layout>