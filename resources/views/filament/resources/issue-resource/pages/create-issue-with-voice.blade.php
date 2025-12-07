<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Voice Recording Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            <div class="text-center space-y-6">
                <div class="space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Sesli Görev Oluştur
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">
                        Mikrofona dokunun ve görevi anlatın.
                    </p>
                </div>
                
                {{-- Active Listeners Section --}}
                <div wire:poll.5s class="flex flex-col items-center justify-center space-y-2">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-widest">Aktif Dinleyenler</span>
                    <div class="flex flex-wrap justify-center gap-2">
                        @forelse($this->getActiveListeners() as $user)
                            <div class="flex items-center gap-2 px-3 py-1 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-full">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                </span>
                                <span class="text-xs font-semibold text-green-700 dark:text-green-300">{{ $user->name }}</span>
                            </div>
                        @empty
                            <span class="text-xs text-gray-400 italic">Şu an kimse dinlemiyor</span>
                        @endforelse
                    </div>
                </div>

                <div class="flex flex-col items-center space-y-8 py-10">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-pink-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                        <button
                            id="startRecording"
                            type="button"
                            class="relative flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-red-50 shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95 border-4 border-white dark:border-gray-800 ring-4 ring-red-50 dark:ring-red-900/20"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon" class="w-14 h-14">
                                <path d="M8.25 4.5a3.75 3.75 0 1 1 7.5 0v8.25a3.75 3.75 0 1 1-7.5 0V4.5Z"/>
                                <path d="M6 10.5a.75.75 0 0 1 .75.75v1.5a5.25 5.25 0 1 0 10.5 0v-1.5a.75.75 0 0 1 1.5 0v1.5a6.751 6.751 0 0 1-6 6.709v2.291h3a.75.75 0 0 1 0 1.5h-7.5a.75.75 0 0 1 0-1.5h3v-2.291a6.751 6.751 0 0 1-6-6.709v-1.5A.75.75 0 0 1 6 10.5Z"/>
                            </svg>
                        </button>
                    </div>

                    <button
                        id="recordingIndicator"
                        type="button"
                        style="display: none;"
                        class="relative flex items-center justify-center w-32 h-32 rounded-full bg-green-600 text-white shadow-xl animate-pulse border-4 border-white dark:border-gray-800"
                    >
                        <div class="flex flex-col items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon" class="w-12 h-12 mb-1">
                                <path fill-rule="evenodd" d="M4.5 7.5a3 3 0 0 1 3-3h9a3 3 0 0 1 3 3v9a3 3 0 0 1-3 3h-9a3 3 0 0 1-3-3v-9Z" clip-rule="evenodd"/>
                            </svg>
                            <span id="recordingTimer" class="text-xs font-mono font-medium tracking-wider">00:00</span>
                        </div>
                    </button>

                    <div id="recordingStatus" class="h-6 text-base font-medium text-gray-600 dark:text-gray-300 transition-all duration-300"></div>

                    <div id="loadingIndicator" style="display: none;" class="flex flex-col items-center space-y-3">
                        <div class="relative w-12 h-12">
                            <div class="absolute top-0 left-0 w-full h-full border-4 border-gray-200 dark:border-gray-700 rounded-full"></div>
                            <div class="absolute top-0 left-0 w-full h-full border-4 border-primary-600 rounded-full border-t-transparent animate-spin"></div>
                        </div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 animate-pulse">Ses işleniyor...</p>
                    </div>
                </div>

                <div id="summaryResult" class="hidden max-w-2xl mx-auto mt-6 p-6 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800/50 rounded-xl text-left shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon" class="w-5 h-5 text-green-600 dark:text-green-400">
                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-semibold text-green-800 dark:text-green-300">Anlaşılan Görev:</p>
                            <p id="summaryText" class="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed"></p>
                        </div>
                    </div>
                </div>

                <div id="transcriptionResult" class="hidden max-w-2xl mx-auto mt-4 p-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 rounded-xl text-left">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Transkript</p>
                    <p id="transcriptionText" class="text-sm text-gray-600 dark:text-gray-400 italic"></p>
                </div>

                @if(config('app.debug'))
                <div id="debugLog" class="hidden max-w-2xl mx-auto mt-6 p-4 bg-gray-900 text-green-400 font-mono text-xs rounded-xl overflow-y-auto max-h-64 text-left shadow-lg border border-gray-700">
                    <div class="flex justify-between items-center mb-2 border-b border-gray-700 pb-2">
                        <span class="font-bold">DEBUG LOG</span>
                        <button onclick="document.getElementById('debugLogContent').innerHTML = ''" class="text-gray-400 hover:text-white">Clear</button>
                    </div>
                    <div id="debugLogContent" class="space-y-1"></div>
                </div>
                @endif
            </div>
        </div>

        {{-- Created Issue Link - Controlled by Livewire --}}
        @if($createdIssueUrl)
        <div class="max-w-2xl mx-auto mt-4 p-6 bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-800/50 rounded-xl text-center shadow-sm">
            <div class="space-y-3">
                <p class="text-lg font-semibold text-primary-900 dark:text-primary-100">İş Başarıyla Oluşturuldu!</p>
                <a href="{{ $createdIssueUrl }}"  
                    target="_blank"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                    İşi Görüntüle
                </a>
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        @php
            $voiceJsPath = base_path('packages/visio/kanban/resources/js/voice-issue.js');
            if (!file_exists($voiceJsPath)) {
                $voiceJsPath = base_path('vendor/visio/kanban/resources/js/voice-issue.js');
            }
        @endphp
        {!! file_get_contents($voiceJsPath) !!}

        // Singleton instance to prevent duplicate initialization
        window.voiceManagerInstance = null;

        window.initVoiceManager = function() {
            // Destroy previous instance if exists
            if (window.voiceManagerInstance) {
                console.log('[VoiceManager] Instance already exists, skipping...');
                return;
            }

            if (document.getElementById('startRecording')) {
                console.log('[VoiceManager] Creating new instance...');
                window.voiceManagerInstance = new VoiceIssueManager({
                    openaiKey: '{{ config("services.openai.api_key") }}',
                    debug: {{ config('app.debug') ? 'true' : 'false' }}
                });
            }
        };

        // Initialize once when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', window.initVoiceManager, { once: true });
        } else {
            window.initVoiceManager();
        }
    </script>
    @endpush
</x-filament-panels::page>
