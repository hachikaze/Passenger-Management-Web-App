<div id="alert" class="w-full text-white bg-rose-500">
    <div class="container flex items-center justify-between px-4 py-2 mx-auto">
        <div class="flex items-center">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <g id="style=linear">
                        <g id="error-box">
                            <path id="vector" d="M2 8C2 4.68629 4.68629 2 8 2H16C19.3137 2 22 4.68629 22 8V16C22 19.3137 19.3137 22 16 22H8C4.68629 22 2 19.3137 2 16V8Z" 
                                stroke="#FFF" stroke-width="1.5"></path>
                            <path id="vector_2" d="M9.00012 9L15.0001 15" stroke="#FFF" stroke-width="1.5" stroke-linecap="round"></path>
                            <path id="vector_3" d="M15 9L9 14.9999" stroke="#FFF" stroke-width="1.5" stroke-linecap="round"></path>
                        </g>
                    </g>
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