<!-- resources/views/mail/password-reset-link.blade.php -->
<x-alert-layout>
    <div class="max-w-xl mx-auto w-full p-8 rounded-lg shadow-lg bg-white flex flex-col text-center overflow-hidden">
        <div class="flex flex-col items-center mb-6">
            <h2 class="text-2xl text-gray-700 font-bold self-start mb-6">Hello!</h2>
            <p class="text-gray-500 self-start">Please click the button below to reset your password.</p>
        </div>

        <div class="flex mx-auto mb-6">
            <a href="{{ route('reset-password', ['token' => $token]) }}" class="mr-10 px-6 py-2 text-white bg-gray-500 rounded shadow hover:bg-gray-700 transition duration-300">
                Reset Password
            </a>
        </div>

        <p class="text-gray-500 self-start mb-6">If you did not request a password reset, please disregard this email.</p>

        <p class="text-gray-500 self-start">Regards,</p>
        <p class="text-gray-500 self-start">Pasig River Ferry</p>
    </div>
</x-alert-layout>