<x-alert-layout>
    <div class="max-w-xl mx-auto w-full p-8 rounded-lg shadow-lg bg-white flex flex-col overflow-hidden">
        <div class="flex flex-col items-center mb-6">
            <h2 class="text-2xl text-gray-700 font-bold self-start mb-6">Verify Your Email Address</h2>
            <p class="text-gray-500 self-start">Please click the button below to verify your email address.</p>
        </div>

        <div class="flex mx-auto mb-6">
            <a href="{{ $verificationUrl }}" class="mr-10 px-6 py-2 text-white bg-gray-500 rounded shadow hover:bg-gray-700 transition duration-300">
                Verify Email Address
            </a>
        </div>

        <p class="text-gray-500 self-start mb-6">If you did not request a verification, please disregard this email.</p>

        <p class="text-gray-500 self-start">Regards,</p>
        <p class="text-gray-500 self-start">Pasig River Ferry</p>

        <hr class="my-6">

        <p class="text-gray-500 self-start">If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:</p>
        <p class="break-all"><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>
    </div>
</x-alert-layout>