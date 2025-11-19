<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Board Selector - Simple Border Style --}}
        <div class="flex items-center gap-3 overflow-x-auto pb-2">
            @foreach ($this->getBoards() as $board)
                <button wire:click="selectBoard({{ $board->id }})"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all whitespace-nowrap bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 {{ $selectedBoardId === $board->id ? 'border-2 border-blue-600 shadow-md' : 'border border-gray-200 dark:border-gray-700' }}">
                    <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $board->color }}"></div>
                    <span class="font-medium text-sm">{{ $board->name }}</span>
                    <span
                        class="text-xs px-1.5 py-0.5 rounded {{ $selectedBoardId === $board->id ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        {{ $board->issues_count }}
                    </span>
                </button>
            @endforeach
        </div>

        {{-- Filters Section --}}
        <div
            class="flex items-center gap-3 flex-wrap bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                    </path>
                </svg>
                <span class="font-medium">Filtreler:</span>
            </div>

            {{-- Assigned To Filter --}}
            <select wire:model.live="selectedUserId"
                class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Tüm Kullanıcılar</option>
                @foreach($this->getUsers() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

            {{-- Priority Filter --}}
            <select wire:model.live="selectedPriority"
                class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Tüm Öncelikler</option>
                @foreach($this->getPriorities() as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            {{-- Clear Filters Button --}}
            @if($selectedUserId || $selectedPriority)
                <button wire:click="clearFilters"
                    class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    Filtreleri Temizle
                </button>
            @endif
        </div>

        @if ($this->getSelectedBoard())
            {{-- Board Description --}}
            @if ($this->getSelectedBoard()->description)
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ $this->getSelectedBoard()->description }}
                        </p>
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
                                        <div class="flex flex-wrap gap-1.5 mb-3">
                                            @foreach ($issue->tags as $tag)
                                                <span
                                                    class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-md font-medium">
                                                    #{{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Assigned User --}}
                                    @if ($issue->assignedUser)
                                        <div
                                            class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 pt-3 border-t border-gray-100 dark:border-gray-800">
                                            <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <span class="font-medium">{{ $issue->assignedUser->name }}</span>
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
        @else
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div
                    class="bg-gray-100 dark:bg-gray-700 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aktif Pano Bulunamadı</h3>
                <p class="text-gray-600 dark:text-gray-400">Görüntülenecek aktif pano bulunmamaktadır.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>