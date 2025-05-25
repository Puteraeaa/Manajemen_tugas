<x-filament::page>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $project)
        <div
            class="group bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl border border-gray-200 dark:border-gray-700 transition-transform transform hover:-translate-y-1 hover:scale-[1.02] duration-300 z-10">

            <a href="{{ route('filament.admin.resources.tasks.index', ['project_id' => $project->id ]) }}"

                class="block p-6 hover:bg-primary-50 dark:hover:bg-gray-800 transition-colors duration-300">

                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] text-gray-400 dark:text-gray-500">
                        #ID: {{ $project->id }}
                    </span>
                </div>

                <h2 class="text-xl font-extrabold text-gray-800 dark:text-white mb-2 group-hover:text-primary-600 transition-all">
                    {{ $project->name }}
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ Str::limit($project->description, 100) }}
                </p>

                <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                    @php
                    $tasks = $project->tasks;

                    $doneTasks = $tasks->filter(fn($task) => $task->status === 'Done');
                    $inProgressTasks = $tasks->filter(fn($task) => $task->status === 'In Progress');
                    $toDoTasks = $tasks->filter(fn($task) => $task->status === 'To Do');

                    $overdueTasks = $tasks->filter(fn($task) =>
                    $task->status !== 'Done' &&
                    $task->deadline &&
                    \Carbon\Carbon::parse($task->deadline)->isPast()
                    );

                    $totalTasks = $tasks->count();
                    @endphp



                    <span class="inline-block bg-gray-300 text-gray-900 dark:bg-gray-800 dark:text-gray-200 rounded-full px-3 py-1">
                        📋 Total: {{ $totalTasks }}
                    </span>

                    <span class="inline-block bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-300 rounded-full px-3 py-1">
                        ✅ Done: {{ $doneTasks->count() }}
                    </span>

                    <span class="inline-block bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-300 rounded-full px-3 py-1">
                        🚧 In Progress: {{ $inProgressTasks->count() }}
                    </span>

                    <span class="inline-block bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-blue-300 rounded-full px-3 py-1">
                        📌 To Do: {{ $toDoTasks->count() }}
                    </span>

                  
                    <span class="inline-block bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-300 rounded-full px-3 py-1">
                        ⏰ Deadline: {{ $overdueTasks->count() }}
                    </span>
                   




                </div>


            </a>
                    

            @can('update', $project)
            <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-950 flex justify-between items-center">
                <a href="{{ route('filament.admin.resources.projects.edit', $project->id) }}"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-semibold inline-flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    Edit Project <span class="text-white">&</span> <span class="text-red-800">Delete</span>
                </a>

                
                
                <span class="text-xs text-gray-400 dark:text-gray-600 italic">
                    Last update: {{ $project->updated_at->diffForHumans() }}
                </span>
            </div>
            @endcan
        </div>
        @empty
        <div class="col-span-full text-center text-gray-400 dark:text-gray-600 py-12">
            😴 Belum ada project yang tersedia, istirahat dulu atau buat yang baru 💡
        </div>
        @endforelse
    </div>
</x-filament::page>