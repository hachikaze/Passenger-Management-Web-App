<div id="alert" class="w-full text-white bg-emerald-500">
    <div class="container flex items-center justify-between px-4 py-2 mx-auto">
        <div class="flex">
            <img src="Images/check.svg" class="w-6 h-6 fill-current">
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