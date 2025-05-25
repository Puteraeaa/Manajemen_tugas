<x-filament::page>
    <div class="bg-gradient-to-r from-primary-500 to-primary-700 p-6 md:p-10 rounded-2xl mb-2 text-white flex flex-col md:flex-row md:justify-between md:items-center gap-6">
        <div class="flex-1 min-w-0">
            <h1 class="text-3xl md:text-4xl font-extrabold mb-2 flex items-center gap-3 truncate">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122" />
                </svg>

                <span class="truncate">{{ $project->name }}</span>
            </h1>
            <p class="text-sm md:text-base opacity-80 italic truncate">
                {{ $project->description }}
            </p>
        </div>
        <div class="flex flex-wrap md:flex-nowrap items-center gap-4 md:gap-6">
            @foreach($project->users as $user)
            <div class="flex flex-col items-center w-16 md:w-20">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-white bg-opacity-30 hover:bg-opacity-50 transition rounded-full flex items-center justify-center shadow-lg">
                    <span class="font-bold text-lg md:text-xl text-white select-none">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <span class="mt-2 text-xs md:text-sm font-medium text-center truncate" title="{{ $user->name }}">{{ $user->name }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="overflow-x-auto bg-primary-50 dark:bg-gray-800 rounded-2xl shadow-lg p-4">
        {{ $this->table }}
    </div>
</x-filament::page>