<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Voice Recording Section --}}
        @if(!$showForm)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 transition-all duration-300">
            <div class="text-center space-y-6">
                <div class="space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Sesli Görev Oluştur
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">
                        Mikrofona dokunun ve görevi anlatın.
                    </p>
                </div>

                <div class="flex flex-col items-center space-y-8 py-10">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-pink-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                        <button
                            id="startRecording"
                            type="button"
                            class="relative flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95 border-4 border-white dark:border-gray-800 ring-4 ring-red-50 dark:ring-red-900/20"
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
                        class="relative flex items-center justify-center w-32 h-32 rounded-full bg-red-600 text-white shadow-xl animate-pulse border-4 border-white dark:border-gray-800"
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
            </div>
        </div>
        @endif

        {{-- Form Section --}}
        @if($showForm)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" class="w-5 h-5 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                    </svg>
                    Görev Detayları
                </h3>
                @if($transcribedText)
                <button 
                    type="button" 
                    onclick="document.getElementById('originalTextDetails').classList.toggle('hidden')"
                    class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium"
                >
                    Orijinal Metni Göster
                </button>
                @endif
            </div>

            @if($transcribedText)
            <div id="originalTextDetails" class="hidden mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/30 rounded-lg">
                <p class="text-sm text-blue-800 dark:text-blue-200 leading-relaxed">
                    <span class="font-semibold">Söylenen:</span> {{ $transcribedText }}
                </p>
            </div>
            @endif

            <form wire:submit="createIssue">
                {{ $this->form }}

                <div class="mt-8 flex gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <x-filament::button
                        type="submit"
                        color="primary"
                        size="lg"
                        class="w-full sm:w-auto"
                    >
                        İşi Kaydet
                    </x-filament::button>

                    <x-filament::button
                        type="button"
                        color="gray"
                        size="lg"
                        wire:click="resetForm"
                        class="w-full sm:w-auto"
                    >
                        İptal
                    </x-filament::button>
                </div>
            </form>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        let mediaRecorder;
        let audioChunks = [];
        let recordingTimer;
        let recordingStartTime;
        let silenceDetectionTimer;
        let audioContext;
        let analyser;
        let dataArray;
        let silenceTimeout;
        const OPENAI_API_KEY = '{{ config("services.openai.api_key") }}';
        const MIN_RECORDING_TIME = 2000; // 2 saniye minimum
        const SILENCE_THRESHOLD = 20; // Ses seviyesi eşiği
        const SILENCE_DURATION = 1500; // 1.5 saniye sessizlik
        let isProcessing = false;

        document.getElementById('startRecording')?.addEventListener('click', async () => {
            if (isProcessing) return;

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

                // Audio analysis setup
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                analyser = audioContext.createAnalyser();
                const source = audioContext.createMediaStreamSource(stream);
                source.connect(analyser);
                analyser.fftSize = 256;
                const bufferLength = analyser.frequencyBinCount;
                dataArray = new Uint8Array(bufferLength);

                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                recordingStartTime = Date.now();

                mediaRecorder.ondataavailable = (event) => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = async () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    await processAudio(audioBlob);
                    stream.getTracks().forEach(track => track.stop());
                    if (audioContext && audioContext.state !== 'closed') {
                        audioContext.close();
                    }
                };

                mediaRecorder.start();

                document.getElementById('startRecording').style.display = 'none';
                document.getElementById('recordingIndicator').style.display = 'flex';
                document.getElementById('recordingStatus').textContent = 'Dinliyorum...';
                document.getElementById('recordingStatus').className = 'h-6 text-base font-medium text-red-600 dark:text-red-400 animate-pulse transition-all duration-300';

                // Timer start
                recordingTimer = setInterval(updateTimer, 1000);

                // Silence detection start
                setTimeout(() => {
                    startSilenceDetection();
                }, MIN_RECORDING_TIME);

            } catch (error) {
                console.error('Mikrofon erişim hatası:', error);
                new FilamentNotification()
                    .title('Hata')
                    .body('Mikrofona erişilemedi. Lütfen izinleri kontrol edin.')
                    .danger()
                    .send();
            }
        });

        // Manual stop
        document.getElementById('recordingIndicator')?.addEventListener('click', () => {
            stopRecording();
        });

        function updateTimer() {
            const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            const timerElement = document.getElementById('recordingTimer');
            if (timerElement) {
                timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
        }

        function startSilenceDetection() {
            function checkSilence() {
                if (!mediaRecorder || mediaRecorder.state !== 'recording') return;

                analyser.getByteFrequencyData(dataArray);
                const average = dataArray.reduce((a, b) => a + b) / dataArray.length;

                if (average < SILENCE_THRESHOLD) {
                    if (!silenceTimeout) {
                        silenceTimeout = setTimeout(() => {
                            if (mediaRecorder && mediaRecorder.state === 'recording') {
                                stopRecording();
                            }
                        }, SILENCE_DURATION);
                    }
                } else {
                    if (silenceTimeout) {
                        clearTimeout(silenceTimeout);
                        silenceTimeout = null;
                    }
                }

                silenceDetectionTimer = requestAnimationFrame(checkSilence);
            }

            checkSilence();
        }

        function stopRecording() {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();

                if (recordingTimer) clearInterval(recordingTimer);
                if (silenceDetectionTimer) cancelAnimationFrame(silenceDetectionTimer);
                if (silenceTimeout) clearTimeout(silenceTimeout);

                const recordingIndicator = document.getElementById('recordingIndicator');
                const startRecording = document.getElementById('startRecording');
                const recordingStatus = document.getElementById('recordingStatus');
                const loadingIndicator = document.getElementById('loadingIndicator');

                if (recordingIndicator) recordingIndicator.style.display = 'none';
                // Don't show start button immediately, show loading
                if (recordingStatus) {
                    recordingStatus.textContent = '';
                    recordingStatus.className = 'h-6 text-base font-medium text-gray-600 dark:text-gray-300 transition-all duration-300';
                }
                if (loadingIndicator) loadingIndicator.style.display = 'flex';
            }
        }

        async function processAudio(audioBlob) {
            if (isProcessing) return;
            isProcessing = true;

            try {
                // Whisper transcription
                const formData = new FormData();
                formData.append('file', audioBlob, 'audio.webm');
                formData.append('model', 'whisper-1');
                formData.append('language', 'tr');

                const response = await fetch('https://api.openai.com/v1/audio/transcriptions', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${OPENAI_API_KEY}`
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Transkripsiyon hatası: ' + response.statusText);
                }

                const result = await response.json();
                const transcribedText = result.text;

                const transcriptionText = document.getElementById('transcriptionText');
                const transcriptionResult = document.getElementById('transcriptionResult');

                if (transcriptionText) transcriptionText.textContent = transcribedText;
                if (transcriptionResult) transcriptionResult.classList.remove('hidden');

                // Send to backend
                const processResult = await @this.call('processVoiceInputWithSummary', { text: transcribedText });

                if (processResult && processResult.summary) {
                    const summaryText = document.getElementById('summaryText');
                    const summaryResult = document.getElementById('summaryResult');

                    if (summaryText) summaryText.textContent = processResult.summary;
                    if (summaryResult) summaryResult.classList.remove('hidden');
                }

                if (processResult && processResult.needsAssignment) {
                    await askForAssignment();
                } else {
                    resetUIState();
                }

            } catch (error) {
                console.error('Ses işleme hatası:', error);
                new FilamentNotification()
                    .title('Hata')
                    .body('Ses işlenirken bir hata oluştu: ' + error.message)
                    .danger()
                    .send();
                
                resetUIState();
            }
        }

        function resetUIState() {
            const loadingIndicator = document.getElementById('loadingIndicator');
            const startRecording = document.getElementById('startRecording');
            
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            // Only show start button if form is not shown (handled by Livewire re-render usually, but safe to toggle)
            if (startRecording && !@this.showForm) startRecording.style.display = 'flex';
            
            isProcessing = false;
        }

        async function askForAssignment() {
            const recordingStatus = document.getElementById('recordingStatus');
            if (recordingStatus) {
                recordingStatus.textContent = 'Bu görevi kime atamak istersiniz?';
                recordingStatus.className = 'h-6 text-base font-medium text-primary-600 dark:text-primary-400 transition-all duration-300';
            }

            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance('Bu görevi kime atamak istersiniz?');
                utterance.lang = 'tr-TR';
                speechSynthesis.speak(utterance);
                await new Promise(resolve => setTimeout(resolve, 3000));
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = (event) => audioChunks.push(event.data);

                mediaRecorder.onstop = async () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    stream.getTracks().forEach(track => track.stop());

                    const formData = new FormData();
                    formData.append('file', audioBlob, 'audio.webm');
                    formData.append('model', 'whisper-1');
                    formData.append('language', 'tr');

                    const response = await fetch('https://api.openai.com/v1/audio/transcriptions', {
                        method: 'POST',
                        headers: { 'Authorization': `Bearer ${OPENAI_API_KEY}` },
                        body: formData
                    });

                    const result = await response.json();
                    await @this.call('assignTaskToUser', { name: result.text });
                    resetUIState();
                };

                mediaRecorder.start();
                if (recordingStatus) recordingStatus.textContent = 'Kişi ismini söyleyin...';

                setTimeout(() => {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                    }
                }, 4000);

            } catch (error) {
                console.error('Atama kaydı hatası:', error);
                resetUIState();
            }
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('userAssigned', (event) => {
                const userName = event.name;
                if (userName && 'speechSynthesis' in window) {
                    const utterance = new SpeechSynthesisUtterance(`Görev ${userName} kişisine atandı.`);
                    utterance.lang = 'tr-TR';
                    speechSynthesis.speak(utterance);
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
