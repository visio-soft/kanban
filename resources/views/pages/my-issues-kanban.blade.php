<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Backlog</p>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $this->getIssuesByStatus('backlog')->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">To Do</p>
                        <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $this->getIssuesByStatus('todo')->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">In Progress</p>
                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $this->getIssuesByStatus('in_progress')->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-red-600 dark:text-red-400 font-medium">Overdue</p>
                        <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ $this->getOverdueIssues()->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Overdue Issues Alert --}}
        @if ($this->getOverdueIssues()->count() > 0)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-semibold text-red-900 dark:text-red-100 mb-2">Overdue Issues</h3>
                        <div class="space-y-2">
                            @foreach ($this->getOverdueIssues() as $issue)
                                <div class="bg-white dark:bg-gray-900 rounded p-3 border border-red-200 dark:border-red-800">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $issue->title }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $issue->board->name }} • Due: {{ $issue->due_date->format('M d, Y') }}
                                                ({{ $issue->due_date->diffForHumans() }})
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $issue->priority === 'urgent' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' }}">
                                            {{ ucfirst($issue->priority) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Kanban Board for My Issues --}}
        <div 
            class="grid gap-4 overflow-x-auto pb-4"
            style="grid-template-columns: repeat({{ count($this->getStatuses()) }}, minmax(300px, 1fr));"
            x-data="{ 
                draggedIssue: null,
                draggedFromStatus: null
            }"
        >
            @foreach ($this->getStatuses() as $statusKey => $statusLabel)
                <div 
                    class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 min-h-[500px] border-2 border-transparent transition-colors"
                    x-on:dragover.prevent="$el.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20')"
                    x-on:dragleave="$el.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20')"
                    x-on:drop="
                        $el.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                        if (draggedIssue && draggedFromStatus !== '{{ $statusKey }}') {
                            $wire.updateIssueStatus(draggedIssue, '{{ $statusKey }}');
                        }
                    "
                >
                    {{-- Column Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $statusLabel }}</h3>
                        <span class="text-sm text-gray-500 bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded-full">
                            {{ $this->getIssuesByStatus($statusKey)->count() }}
                        </span>
                    </div>

                    {{-- Issues --}}
                    <div class="space-y-3">
                        @forelse ($this->getIssuesByStatus($statusKey) as $issue)
                            <div 
                                draggable="true"
                                x-on:dragstart="draggedIssue = {{ $issue->id }}; draggedFromStatus = '{{ $statusKey }}'; $el.classList.add('opacity-50')"
                                x-on:dragend="draggedIssue = null; draggedFromStatus = null; $el.classList.remove('opacity-50')"
                                class="bg-white dark:bg-gray-900 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow cursor-move border border-gray-200 dark:border-gray-700"
                            >
                                {{-- Board Name --}}
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full" style="background-color: {{ $issue->board->color }}"></div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $issue->board->name }}</span>
                                </div>

                                {{-- Issue Title --}}
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                                    {{ $issue->title }}
                                </h4>

                                {{-- Issue Meta --}}
                                <div class="flex items-center gap-2 flex-wrap text-xs mb-2">
                                    {{-- Priority Badge --}}
                                    <span class="px-2 py-1 rounded-full {{ $issue->priority === 'urgent' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : ($issue->priority === 'high' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' : ($issue->priority === 'medium' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400')) }}">
                                        {{ ucfirst($issue->priority) }}
                                    </span>

                                    {{-- Due Date --}}
                                    @if ($issue->due_date)
                                        <span class="px-2 py-1 rounded-full {{ $issue->due_date->isPast() && $statusKey !== 'done' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400' }}">
                                            📅 {{ $issue->due_date->format('M d') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Tags --}}
                                @if ($issue->tags && count($issue->tags) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($issue->tags as $tag)
                                            <span class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 dark:text-gray-600">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-sm">No issues</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
