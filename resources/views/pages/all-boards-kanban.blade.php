<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Board Selector --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4 overflow-x-auto pb-2">
                @foreach ($this->getBoards() as $board)
                    <button
                        wire:click="selectBoard({{ $board->id }})"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all {{ $selectedBoardId === $board->id ? 'bg-primary-500 text-white shadow-lg scale-105' : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                    >
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $board->color }}"></div>
                        <span class="font-medium whitespace-nowrap">{{ $board->name }}</span>
                        <span class="text-xs opacity-75">({{ $board->issues_count }})</span>
                    </button>
                @endforeach
            </div>
        </div>

        @if ($this->getSelectedBoard())
            {{-- Board Description --}}
            @if ($this->getSelectedBoard()->description)
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $this->getSelectedBoard()->description }}</p>
                </div>
            @endif

            {{-- Kanban Board --}}
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
                                        <div class="flex flex-wrap gap-1 mb-2">
                                            @foreach ($issue->tags as $tag)
                                                <span class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Assigned User --}}
                                    @if ($issue->assignedUser)
                                        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span>{{ $issue->assignedUser->name }}</span>
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
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-gray-600 dark:text-gray-400">No active boards available</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
