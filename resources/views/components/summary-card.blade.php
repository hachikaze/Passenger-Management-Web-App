<div class="relative p-4 rounded-md bg-white">
    <div class="space-y-2">
        <div class="flex items-center space-x-2 rtl:space-x-reverse text-sm font-medium text-gray-700 dark:text-gray-700">
            <span>{{ $summaryName }}</span>
        </div>

        <div class="text-3xl">
            {{ $slot }}
        </div>

        <div class="flex items-center space-x-1 rtl:space-x-reverse text-xs font-medium text-green-500">

            <span>{{ $summaryDesc }}</span>

        </div>
    </div>
</div>