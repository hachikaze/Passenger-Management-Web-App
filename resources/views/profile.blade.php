<x-sidebar-layout>

<x-slot:heading>
    Profile
</x-slot:heading>

<x-slot:alert>
    @if (session('success'))
        <x-alert-bottom-success>
            {{ session('success') }}
        </x-alert-bottom-success>
    @endif

    @if (session('error'))
        <x-alert-bottom-error>
            {{ session('error') }}
        </x-alert-bottom-error>
    @endif
</x-slot:alert>

<section class="my-8">
    <div class="p-6 bg-white rounded-lg shadow-2xl max-w-screen-xl mx-auto">
        <header class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Profile Information
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form method="POST" action="/profile/update" class="space-y-6">
            @csrf
            @method('patch')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <x-form-label>First Name</x-form-label>
                    <x-form-input id="first_name" 
                                  name="first_name" 
                                  value="{{ $user->first_name }}" 
                                  placeholder="First Name" />
                    <x-form-error name="first_name" />
                </div>

                <div>
                    <x-form-label>Middle Name</x-form-label>
                    <x-form-input id="middle_name" 
                                  name="middle_name" 
                                  value="{{ $user->middle_name }}" 
                                  placeholder="Middle Name" />
                    <x-form-error name="middle_name" />
                </div>

                <div>
                    <x-form-label>Last Name</x-form-label>
                    <x-form-input id="last_name" 
                                  name="last_name"
                                  value="{{ $user->last_name }}" 
                                  placeholder="Last Name" />
                    <x-form-error name="last_name" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <x-form-button class="px-8 py-2 bg-green-500 text-white hover:bg-green-600 transition duration-300">
                    Save
                </x-form-button>
            </div>
        </form>
    </div>
</section>

<section class="my-8">
    <div class="p-6 bg-white rounded-lg shadow-2xl max-w-screen-xl mx-auto">
        <header class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Contact Information
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Update your account's contact information.
            </p>
        </header>

        <form method="POST" action="/profile/contact" id="contact" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-form-label>Email</x-form-label>
                    <x-form-input id="email" 
                                  name="email"
                                  value="{{ $user->email }}"
                                  readonly 
                                  class="bg-gray-200" />
                    <x-form-error name="email" />
                </div>

                <div>
                    <x-form-label>Contact Number</x-form-label>
                    <x-form-input id="contact_number" 
                                  name="contact_number"
                                  value="{{ $user->contact_number }}" 
                                  placeholder="Contact Number" />
                    <x-form-error name="contact_number" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <x-form-button class="px-8 py-2 bg-green-500 text-white hover:bg-green-600 transition duration-300">
                    Save
                </x-form-button>
            </div>
        </form>
    </div>
</section>

<section class="mb-8">
    <div class="p-6 bg-white rounded-lg shadow-2xl max-w-screen-xl mx-auto">
        <header class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Update Password
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </header>

        <form method="POST" action="/profile/password" class="space-y-6">
            @csrf
            @method('patch')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-form-label>Current Password</x-form-label>
                    <x-form-input id="current_password" name="current_password" type="password" placeholder="Enter current password" />
                    <x-form-error name="current_password" />
                </div>

                <div>
                    <x-form-label>New Password</x-form-label>
                    <x-form-input id="password" name="password" type="password" placeholder="Enter new password" />
                    <x-form-error name="password" />
                </div>

                <div>
                    <x-form-label>Confirm Password</x-form-label>
                    <x-form-input id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm new password" />
                    <x-form-error name="password_confirmation" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <x-form-button class="px-8 py-2 bg-green-500 text-white hover:bg-green-600 transition duration-300">
                    Save
                </x-form-button>
            </div>
        </form>
    </div>
</section>

</x-sidebar-layout>