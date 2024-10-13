<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRF Login</title>
    @vite('resources/css/app.css') 
</head>
<body>
    <div class="bg-white font-inter">
        <div class="flex justify-center h-screen">
            <div class="hidden bg-cover lg:block lg:w-2/3" style="background-image: url('Images/PRF Homepage Background.jpg'); background-size: cover; background-position: center; height: 100vh;">
                <div class="flex items-center h-full px-20 bg-gray-900 bg-opacity-40">
                    <div>
                        <h2 class="text-2xl font-bold text-white sm:text-3xl">{{ $header }}</h2>

                        <p class="max-w-xl mt-3 text-gray-100">
                            {{ $description }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center w-full max-w-md px-6 mx-auto lg:w-2/6">
                <div class="flex-1">
                    <div class="text-center">
                        <div class="flex justify-center mx-auto">
                            <a href="{{ route('login') }}">
                                <img class="w-auto h-20 sm:h-32" src="Images/PRF Logo.png" alt="Logo">
                            </a>
                        </div>

                        <p class="mt-3 text-lg">{{ $title }}</p>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>