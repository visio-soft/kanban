<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Simple Header --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Görevlerim</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Bana atanan görevler</p>
                </div>
            </div>

            {{-- Stats Cards - Compact Design --}}
            <div class="flex gap-3 overflow-x-auto pb-2">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg px-4 py-3 border-l-4 border-blue-500 shadow-sm hover:shadow-md transition-shadow min-w-[140px] flex-shrink-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Beklemede</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->getIssuesByStatus('backlog')->count() }}</p>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg px-4 py-3 border-l-4 border-yellow-500 shadow-sm hover:shadow-md transition-shadow min-w-[140px] flex-shrink-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Yapılacak</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->getIssuesByStatus('todo')->count() }}</p>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg px-4 py-3 border-l-4 border-purple-500 shadow-sm hover:shadow-md transition-shadow min-w-[140px] flex-shrink-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Devam Ediyor</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->getIssuesByStatus('in_progress')->count() }}</p>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg px-4 py-3 border-l-4 border-red-500 shadow-sm hover:shadow-md transition-shadow min-w-[140px] flex-shrink-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Gecikmiş</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->getOverdueIssues()->count() }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Overdue Issues Alert with Better Design --}}
        @if ($this->getOverdueIssues()->count() > 0)
            <div
                class="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-l-4 border-red-500 rounded-lg p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="bg-red-100 dark:bg-red-900/30 rounded-full p-2 flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-900 dark:text-red-100 text-lg mb-3">⚠️ Gecikmiş Görevler</h3>
                        <div class="space-y-3">
                            @foreach ($this->getOverdueIssues() as $issue)
                                <div
                                    class="bg-white dark:bg-gray-900 rounded-lg p-4 border border-red-200 dark:border-red-800 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2 truncate">
                                                {{ $issue->title }}
                                            </h4>
                                            <div
                                                class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400 flex-wrap">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                        </path>
                                                    </svg>
                                                    {{ $issue->board->name }}
                                                </span>
                                                <span
                                                    class="flex items-center gap-1 text-red-600 dark:text-red-400 font-medium">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    {{ $issue->due_date->format('d.m.Y') }}
                                                    ({{ $issue->due_date->diffForHumans() }})
                                                </span>
                                            </div>
                                        </div>
                                        <span
                                            class="px-3 py-1.5 text-xs font-semibold rounded-full whitespace-nowrap {{ $issue->priority === 'urgent' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' }}">
                                            {{ config('kanban.priorities')[$issue->priority] ?? ucfirst($issue->priority) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Kanban Board with Enhanced Design --}}
        <div class="grid gap-4 overflow-x-auto pb-4"
            style="grid-template-columns: repeat({{ count($this->getStatuses()) }}, minmax(320px, 1fr));" x-data="{ 
                draggedIssue: null,
                draggedFromStatus: null
            }">
            @foreach ($this->getStatuses() as $statusKey => $statusLabel)
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 min-h-[600px] border-2 border-gray-200 dark:border-gray-700 transition-all duration-200"
                    x-on:dragover.prevent="$el.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20', 'scale-[1.02]')"
                    x-on:dragleave="$el.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20', 'scale-[1.02]')"
                    x-on:drop="
                                $el.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20', 'scale-[1.02]');
                                if (draggedIssue && draggedFromStatus !== '{{ $statusKey }}') {
                                    $wire.updateIssueStatus(draggedIssue, '{{ $statusKey }}');
                                }
                            ">
                    {{-- Enhanced Column Header --}}
                    <div
                        class="flex items-center justify-between mb-5 pb-3 border-b-2 border-gray-300 dark:border-gray-600">
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $statusLabel }}</h3>
                        <span
                            class="text-sm font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-full">
                            {{ $this->getIssuesByStatus($statusKey)->count() }}
                        </span>
                    </div>

                    {{-- Issues with Better Cards --}}
                    <div class="space-y-3">
                        @forelse ($this->getIssuesByStatus($statusKey) as $issue)
                            <div draggable="true"
                                x-on:dragstart="draggedIssue = {{ $issue->id }}; draggedFromStatus = '{{ $statusKey }}'; $el.classList.add('opacity-50', 'scale-95')"
                                x-on:dragend="draggedIssue = null; draggedFromStatus = null; $el.classList.remove('opacity-50', 'scale-95')"
                                onclick="window.open('{{ route('filament.admin.resources.issues.edit', $issue) }}', '_blank')"
                                class="bg-white dark:bg-gray-900 rounded-lg p-4 shadow-sm hover:shadow-lg transition-all duration-200 cursor-pointer border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 group">
                                {{-- Board Name with Color Indicator --}}
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-3 h-3 rounded-full shadow-sm"
                                        style="background-color: {{ $issue->board->color }}"></div>
                                    <span
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $issue->board->name }}</span>
                                </div>

                                {{-- Issue Title --}}
                                <h4
                                    class="font-semibold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2">
                                    {{ $issue->title }}
                                </h4>

                                {{-- Issue Meta Information --}}
                                <div class="flex items-center gap-2 flex-wrap text-xs mb-3">
                                    {{-- Priority Badge --}}
                                    <span
                                        class="px-2.5 py-1 rounded-full font-semibold {{ $issue->priority === 'urgent' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : ($issue->priority === 'high' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' : ($issue->priority === 'medium' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400')) }}">
                                        {{ config('kanban.priorities')[$issue->priority] ?? ucfirst($issue->priority) }}
                                    </span>

                                    {{-- Due Date --}}
                                    @if ($issue->due_date)
                                        <span
                                            class="px-2.5 py-1 rounded-full font-medium {{ $issue->due_date->isPast() && $statusKey !== 'done' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400' }}">
                                            📅 {{ $issue->due_date->format('d.m.Y') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Tags --}}
                                @if ($issue->tags && count($issue->tags) > 0)
                                    <div class="flex flex-wrap gap-1.5 pt-2 border-t border-gray-100 dark:border-gray-800">
                                        @foreach ($issue->tags as $tag)
                                            <span
                                                class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-md font-medium">
                                                #{{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-400 dark:text-gray-600">
                                <svg class="w-16 h-16 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                                <p class="text-sm font-medium">Görev yok</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>