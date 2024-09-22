<div>
    <button {{ $attributes->merge(['class' => 'w-full px-4 py-2 tracking-wide text-white transition-colors duration-300 transform bg-blue-500 rounded-lg hover:bg-blue-400 focus:outline-none focus:bg-blue-400 focus:ring focus:ring-blue-300 focus:ring-opacity-50']) }}>{{ $slot }}</button>
</div>