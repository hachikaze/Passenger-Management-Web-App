<div id="alert" class="w-full text-white bg-emerald-500">
    <div class="container flex items-center justify-between px-4 py-2 mx-auto">
        <div class="flex items-center">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <path d="M8.5 12.5L10.5 14.5L15.5 9.5" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C21.5093 4.43821 21.8356 5.80655 21.9449 8" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"></path>
                </g>
            </svg>
            <p class="mx-3">{{ $slot }}</p>
        </div>

        <button id="closeButton" class="px-2 transition-colors duration-300 transform rounded-lg hover:bg-opacity-25 hover:bg-gray-600 focus:outline-none">
            X
        </button>
    </div>
</div>

<script>
    document.getElementById('closeButton').addEventListener('click', function() {
        document.getElementById('alert').style.display = 'none';
    });
</script>