<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Tasks Management
        </x-slot>

        {{-- Tab Navigation --}}
        <div class="mb-4">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button
                    wire:click="switchTab('project')"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'project' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <x-heroicon-o-folder class="w-4 h-4 inline mr-1" />
                    Project Tasks
                </button>
                <button
                    wire:click="switchTab('personal')"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'personal' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <x-heroicon-o-user class="w-4 h-4 inline mr-1" />
                    Personal Tasks
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        @if($activeTab === 'project')
        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->getProjectTasks() as $task)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $task->project->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $task->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $task->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $task->priority === 'High' ? 'bg-red-100 text-red-800' : ($task->priority === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $task->priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $task->deadline ? $task->deadline->format('Y-m-d') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $task->status === 'Done' ? 'bg-green-100 text-green-800' : ($task->status === 'In Progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $task->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No project tasks found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->getPersonalTasks() as $task)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $task->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $task->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $task->priority === 'High' ? 'bg-red-100 text-red-800' : ($task->priority === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $task->priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $task->status === 'Done' ? 'bg-green-100 text-green-800' : ($task->status === 'In Progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $task->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No personal tasks found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>