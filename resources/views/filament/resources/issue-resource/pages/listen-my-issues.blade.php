<x-filament-panels::page>
    {{-- Polling container - checks every 3 seconds --}}
    <div wire:poll.3s="checkForNewIssues">
        <div 
            x-data="issueListener"
            @new-issue-arrived.window="handleNewIssue($event.detail.issue)"
            class="min-h-[70vh] flex flex-col"
        >
            {{-- Connection Status --}}
            <div class="mb-6">
                <div class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="relative">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Bağlantı aktif - Yeni iş bekleniyor...
                    </span>
                    <div class="ml-auto flex items-center gap-2">
                        <button 
                            @click="toggleSound()"
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            :title="soundEnabled ? 'Sesi kapat' : 'Sesi aç'"
                        >
                            <template x-if="soundEnabled">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                                </svg>
                            </template>
                            <template x-if="!soundEnabled">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75 19.5 12m0 0 2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6 4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                                </svg>
                            </template>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Current Issue Alert --}}
            @if($hasNewIssue && !empty($currentIssue))
            <div 
                x-show="showAlert"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="mb-6"
            >
                <div class="relative overflow-hidden bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl shadow-2xl border-2 border-primary-400">
                    <div class="absolute inset-0 bg-gradient-to-r from-primary-400/20 to-transparent animate-pulse"></div>
                    
                    <div class="relative p-8">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-white">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                    </svg>
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-2xl font-bold text-white truncate">
                                        {{ $currentIssue['title'] ?? 'Yeni İş' }}
                                    </h3>
                                    @if(!empty($currentIssue['priority']))
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                        @if($currentIssue['priority'] === 'urgent') bg-red-500 text-white
                                        @elseif($currentIssue['priority'] === 'high') bg-orange-500 text-white
                                        @elseif($currentIssue['priority'] === 'medium') bg-yellow-500 text-gray-900
                                        @else bg-gray-200 text-gray-700
                                        @endif">
                                        {{ $currentIssue['priority_label'] ?? $currentIssue['priority'] }}
                                    </span>
                                    @endif
                                </div>

                                @if(!empty($currentIssue['description']))
                                <p class="text-white/90 text-lg mb-4 line-clamp-2">
                                    {{ Str::limit($currentIssue['description'], 200) }}
                                </p>
                                @endif

                                <div class="flex flex-wrap items-center gap-4 text-white/80 text-sm">
                                    @if(!empty($currentIssue['board_name']))
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6Z" />
                                        </svg>
                                        {{ $currentIssue['board_name'] }}
                                    </span>
                                    @endif
                                    @if(!empty($currentIssue['due_date']))
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                        {{ $currentIssue['due_date'] }}
                                    </span>
                                    @endif
                                    @if(!empty($currentIssue['created_at']))
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        {{ $currentIssue['created_at'] }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-shrink-0 flex flex-col gap-2">
                                <a 
                                    href="{{ \Visiosoft\Kanban\Resources\IssueResource::getUrl('edit', ['record' => $currentIssue['id']]) }}"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors shadow-lg"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                    İşe Git
                                </a>
                                <button 
                                    wire:click="dismissIssue"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-colors text-sm"
                                >
                                    Kapat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Waiting State --}}
            @if(!$hasNewIssue)
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center space-y-6">
                    <div class="relative inline-flex">
                        <div class="absolute inset-0 w-40 h-40 bg-primary-500/20 rounded-full animate-ping"></div>
                        <div class="absolute inset-4 w-32 h-32 bg-primary-500/30 rounded-full animate-pulse"></div>
                        
                        <div class="relative w-40 h-40 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center shadow-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Yeni İş Bekleniyor
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                            Bu sayfayı açık tutun. Size atanan yeni işler anında sesli olarak bildirilecek.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Issue History --}}
            @if(count($issueHistory) > 0)
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Son Gelen İşler</h3>
                <div class="space-y-3">
                    @foreach($issueHistory as $issue)
                    <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">
                                {{ $issue['title'] }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $issue['board_name'] ?? '' }} • {{ $issue['created_at'] ?? '' }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @if($issue['priority'] === 'urgent') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                            @elseif($issue['priority'] === 'high') bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400
                            @elseif($issue['priority'] === 'medium') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                            @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400
                            @endif">
                            {{ $issue['priority_label'] ?? $issue['priority'] }}
                        </span>
                        <a 
                            href="{{ \Visiosoft\Kanban\Resources\IssueResource::getUrl('edit', ['record' => $issue['id']]) }}"
                            class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Overlay inside x-data scope --}}
            <div 
                x-show="!hasInteracted"
                style="display: none;"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                <button 
                    @click="startListeningSESSION"
                    class="flex flex-col items-center gap-4 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl transform transition hover:scale-105"
                >
                    <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-green-600 dark:text-green-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                        </svg>
                    </div>
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Dinlemeyi Başlat</h3>
                        <p class="text-gray-500 dark:text-gray-400">Sesli bildirimleri almak için tıklayın</p>
                    </div>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('issueListener', () => ({
                soundEnabled: true,
                showAlert: true,
                hasInteracted: false,
                audioContext: null,

                init() {
                    // Load sound preference
                    const saved = localStorage.getItem('issue-listener-sound');
                    if (saved !== null) {
                        this.soundEnabled = saved === 'true';
                    }
                },

                startListeningSESSION() {
                    this.hasInteracted = true;
                    
                    // Initialize AudioContext
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    this.audioContext = new AudioContext();
                    
                    // Resume if suspended (common browser policy)
                    if (this.audioContext.state === 'suspended') {
                        this.audioContext.resume();
                    }

                    // Play a silent buffer to fully unlock iOS/Chrome
                    const buffer = this.audioContext.createBuffer(1, 1, 22050);
                    const source = this.audioContext.createBufferSource();
                    source.buffer = buffer;
                    source.connect(this.audioContext.destination);
                    source.start(0);

                    // Also trigger speech synthesis silently to unlock it
                    if ('speechSynthesis' in window) {
                        const utterance = new SpeechSynthesisUtterance('');
                        utterance.volume = 0;
                        speechSynthesis.speak(utterance);
                    }
                },

                toggleSound() {
                    this.soundEnabled = !this.soundEnabled;
                    localStorage.setItem('issue-listener-sound', this.soundEnabled);
                },

                handleNewIssue(issue) {
                    this.showAlert = true;

                    if (this.soundEnabled) {
                        // Play notification sound
                        this.playNotificationSound();

                        // Speak the issue
                        // Delay slightly to let the ding sound finish
                        setTimeout(() => {
                            this.speakIssue(issue);
                        }, 800);
                    }
                },

                playNotificationSound() {
                    if (!this.hasInteracted) return;

                    try {
                        // Use existing context if available and running
                        if (!this.audioContext) {
                            const AudioContext = window.AudioContext || window.webkitAudioContext;
                            this.audioContext = new AudioContext();
                        }
                        
                        if (this.audioContext.state === 'suspended') {
                            this.audioContext.resume();
                        }

                        const oscillator = this.audioContext.createOscillator();
                        const gainNode = this.audioContext.createGain();

                        oscillator.connect(gainNode);
                        gainNode.connect(this.audioContext.destination);

                        oscillator.frequency.value = 800;
                        oscillator.type = 'sine';
                        
                        gainNode.gain.setValueAtTime(0.3, this.audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.5);

                        oscillator.start(this.audioContext.currentTime);
                        oscillator.stop(this.audioContext.currentTime + 0.5);
                    } catch (e) {
                        console.log('Could not play notification sound:', e);
                    }
                },

                speakIssue(issue) {
                    if (!('speechSynthesis' in window)) {
                        console.log('Speech synthesis not supported');
                        return;
                    }

                    // Ensure we can speak
                    speechSynthesis.cancel();
                    
                    // Helper to finding TR voice
                    const getTrVoice = () => {
                        const voices = window.speechSynthesis.getVoices();
                        // Try exact match first, then prefix
                        return voices.find(v => v.lang === 'tr-TR') || 
                               voices.find(v => v.lang.startsWith('tr'));
                    };

                    if (issue.voice_text) {
                        // Use the server-provided custom message
                        const utterance = new SpeechSynthesisUtterance(issue.voice_text);
                        const trVoice = getTrVoice();
                        if (trVoice) utterance.voice = trVoice;
                        utterance.lang = 'tr-TR';
                        utterance.rate = 1.0; 
                        utterance.pitch = 1;
                        utterance.volume = 1;
                        speechSynthesis.speak(utterance);
                        return;
                    }

                    let message = '';
                    
                    // Priority Logic
                    if (['urgent', 'high'].includes(issue.priority)) {
                        message += 'Öncelikli bir ' + issue.title + ' işi size atandı. ';
                    } else if (issue.priority === 'low') {
                        message += 'Öncelikli olmayan ' + issue.title + ' işi size atandı. ';
                    } else {
                        // Medium or undefined
                        message += 'Yeni bir ' + issue.title + ' işi size atandı. ';
                    }

                    // Date Logic
                    if (issue.due_date_raw) {
                        const today = new Date();
                        const due = new Date(issue.due_date_raw);
                        
                        // Reset time parts for accurate day comparison
                        today.setHours(0, 0, 0, 0);
                        due.setHours(0, 0, 0, 0);
                        
                        const diffTime = due - today;
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        if (diffDays === 1) {
                            message += 'Yarına kadar bitmesi gerekiyor. ';
                        } else if (diffDays === 2) {
                            message += 'Öbürsü gün bitmesi gerekiyor. ';
                        } else if (diffDays > 0) {
                            // Convert YYYY-MM-DD to DD Month YYYY for better pronunciation
                            const options = { year: 'numeric', month: 'long', day: 'numeric' };
                            const formattedDate = due.toLocaleDateString('tr-TR', options);
                            message += formattedDate + ' tarihinde bitmesi gerekiyor. ';
                        }
                    }

                    if (issue.description) {
                        const shortDesc = issue.description.substring(0, 150);
                        message += 'Detaylar şöyle: ' + shortDesc;
                    }

                    const utterance = new SpeechSynthesisUtterance(message);
                    const trVoice = getTrVoice();
                    if (trVoice) utterance.voice = trVoice;
                    utterance.lang = 'tr-TR';
                    utterance.rate = 1.0;
                    utterance.pitch = 1;
                    utterance.volume = 1;

                    speechSynthesis.speak(utterance);
                }
            }));
        });
    </script>
    @endpush
</x-filament-panels::page>
