
if (typeof window.VoiceIssueManager === 'undefined') {
    window.VoiceIssueManager = class VoiceIssueManager {
        constructor(config) {
            this.config = Object.assign({
                minRecordingTime: 500, // Reduced for faster initial check
                silenceThreshold: 15,
                silenceDuration: 600, // Significantly reduced for faster stop
                openaiKey: '',
                debug: false,
                endpoints: {
                    transcription: 'https://api.openai.com/v1/audio/transcriptions'
                }
            }, config);

            this.state = {
                isRecording: false,
                isProcessing: false,
                audioContext: null,
                mediaRecorder: null,
                audioChunks: [],
                startTime: null,
                timerInterval: null,
                silenceTimer: null,
                analyser: null,
                dataArray: null,
                animationFrame: null,
            };

            this.elements = {
                startBtn: document.getElementById('startRecording'),
                recordingIndicator: document.getElementById('recordingIndicator'),
                statusText: document.getElementById('recordingStatus'),
                timer: document.getElementById('recordingTimer'),
                loading: document.getElementById('loadingIndicator'),
                summaryResult: document.getElementById('summaryResult'),
                transcriptionResult: document.getElementById('transcriptionResult'),
                transcriptionText: document.getElementById('transcriptionText'),
                summaryText: document.getElementById('summaryText'),
                debugLog: document.getElementById('debugLog'),
                debugLogContent: document.getElementById('debugLogContent'),
            };

            this.init();
        }

        log(message, data = null) {
            if (!this.config.debug) return;

            const timestamp = new Date().toLocaleTimeString();
            const logMessage = `[${timestamp}] ${message} `;

            console.log(logMessage, data || '');

            if (this.elements.debugLog && this.elements.debugLogContent) {
                this.elements.debugLog.classList.remove('hidden');
                const logEntry = document.createElement('div');
                logEntry.className = 'border-b border-gray-800 pb-1 mb-1 last:border-0';
                logEntry.innerHTML = `<span class="text-gray-500 mr-2">[${timestamp}]</span> ${message}`;

                if (data) {
                    const dataPre = document.createElement('pre');
                    dataPre.className = 'text-xs text-gray-500 mt-1 overflow-x-auto';
                    try {
                        dataPre.textContent = JSON.stringify(data, null, 2);
                    } catch (e) {
                        dataPre.textContent = String(data);
                    }
                    logEntry.appendChild(dataPre);
                }

                // Prepend to show newest on top
                this.elements.debugLogContent.prepend(logEntry);
            }
        }

        init() {
            this.log('Initializing VoiceIssueManager', this.config);

            if (this.elements.startBtn) {
                this.elements.startBtn.addEventListener('click', () => {
                    this.log('Start button clicked');
                    this.startRecording();
                });
            } else {
                this.log('Start button not found');
            }

            if (this.elements.recordingIndicator) {
                this.elements.recordingIndicator.addEventListener('click', () => {
                    this.log('Stop button clicked');
                    this.stopRecording();
                });
            }

            // Livewire events
            document.addEventListener('livewire:init', () => {
                this.log('Livewire initialized, listening for events');
                Livewire.on('voice-issue-created', (data) => {
                    this.log('Event received: voice-issue-created', data);
                    this.handleIssueCreated(data);
                });
                Livewire.on('voice-error', (data) => {
                    this.log('Event received: voice-error', data);
                    this.handleError(data);
                });
            });
        }

        async startRecording() {
            if (this.state.isProcessing) return;

            try {
                this.log('Requesting microphone access');
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.log('Microphone access granted');
                this.setupAudioContext(stream);
                this.setupMediaRecorder(stream);

                this.state.mediaRecorder.start();
                this.log('MediaRecorder started');
                this.updateUI('recording');
                this.speak("Dinliyorum...", 0.8); // Slight wait to not overlap with click sound if any

            } catch (error) {
                console.error('Microphone error:', error);
                this.log('Microphone error', error);
                this.showNotification('Hata', 'Mikrofona erişilemedi. Lütfen izinleri kontrol edin.', 'danger');
            }
        }

        setupAudioContext(stream) {
            this.log('Setting up AudioContext');
            this.state.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            this.state.analyser = this.state.audioContext.createAnalyser();
            const source = this.state.audioContext.createMediaStreamSource(stream);
            source.connect(this.state.analyser);
            this.state.analyser.fftSize = 256;
            this.state.dataArray = new Uint8Array(this.state.analyser.frequencyBinCount);
        }

        setupMediaRecorder(stream) {
            this.log('Setting up MediaRecorder');
            this.state.mediaRecorder = new MediaRecorder(stream);
            this.state.audioChunks = [];
            this.state.startTime = Date.now();

            this.state.mediaRecorder.ondataavailable = (e) => {
                if (e.data.size > 0) {
                    this.state.audioChunks.push(e.data);
                }
            };

            this.state.mediaRecorder.onstop = () => {
                this.log('MediaRecorder stopped', { chunks: this.state.audioChunks.length });
                this.processRecording(stream);
            };

            // Start checking for silence after minimum time
            setTimeout(() => {
                this.log('Starting silence detection');
                this.detectSilence();
            }, this.config.minRecordingTime);

            // Start timer
            this.state.timerInterval = setInterval(() => this.updateTimer(), 1000);
        }

        detectSilence() {
            if (!this.state.mediaRecorder || this.state.mediaRecorder.state !== 'recording') return;

            const checkAudio = () => {
                if (this.state.mediaRecorder.state !== 'recording') return;

                this.state.analyser.getByteFrequencyData(this.state.dataArray);
                const average = this.state.dataArray.reduce((a, b) => a + b) / this.state.dataArray.length;

                if (average < this.config.silenceThreshold) {
                    if (!this.state.silenceTimer) {
                        this.log('Silence detected, starting timer');
                        this.state.silenceTimer = setTimeout(() => {
                            this.log('Silence timeout reached, stopping recording');
                            this.stopRecording();
                        }, this.config.silenceDuration);
                    }
                } else {
                    if (this.state.silenceTimer) {
                        // this.log('Sound detected, clearing silence timer'); // Too verbose loop
                        clearTimeout(this.state.silenceTimer);
                        this.state.silenceTimer = null;
                    }
                }
                this.state.animationFrame = requestAnimationFrame(checkAudio);
            };

            checkAudio();
        }

        stopRecording() {
            this.log('stopRecording called');
            if (this.state.mediaRecorder && this.state.mediaRecorder.state === 'recording') {
                this.log('Stopping MediaRecorder');
                this.state.mediaRecorder.stop();
                this.cleanupRecordingState();
                this.updateUI('processing');
            } else {
                this.log('MediaRecorder not recording or null', { state: this.state.mediaRecorder?.state });
            }
        }

        cleanupRecordingState() {
            if (this.state.timerInterval) clearInterval(this.state.timerInterval);
            if (this.state.animationFrame) cancelAnimationFrame(this.state.animationFrame);
            if (this.state.silenceTimer) clearTimeout(this.state.silenceTimer);

            if (this.state.audioContext && this.state.audioContext.state !== 'closed') {
                // Don't close immediately if we might need it, but usually good to close or suspend
                this.state.audioContext.close();
            }
        }

        async processRecording(stream) {
            this.log('Processing recording');
            stream.getTracks().forEach(track => track.stop());

            const audioBlob = new Blob(this.state.audioChunks, { type: 'audio/webm' });
            this.log('Audio blob created', { size: audioBlob.size, type: audioBlob.type });

            try {
                const formData = new FormData();
                formData.append('file', audioBlob, 'audio.webm');
                formData.append('model', 'whisper-1');
                formData.append('language', 'tr');

                this.log('Sending audio to Whisper API');
                const response = await fetch(this.config.endpoints.transcription, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${this.config.openaiKey} ` },
                    body: formData
                });

                if (!response.ok) throw new Error('Transkripsiyon hatası: ' + response.statusText);

                const result = await response.json();
                this.log('Whisper result received', result);

                // UI Update with transcript
                if (this.elements.transcriptionText) {
                    this.elements.transcriptionText.textContent = result.text;
                    this.elements.transcriptionResult?.classList.remove('hidden');
                }

                // Send to Livewire (backend will handle speech feedback)
                this.log('Sending text to backend for processing');
                await this.component.processVoiceInputWithSummary({ text: result.text });

            } catch (error) {
                console.error('Processing error:', error);
                this.log('Processing error', error);
                this.handleError({ message: error.message });
                this.updateUI('idle');
            }
        }

        // --- Interaction Handlers ---

        handleIssueCreated(data) {
            const payload = this.getData(data);

            this.log('Issue created successfully', payload);

            // Display summary
            if (payload.summary) {
                if (this.elements.summaryText) {
                    this.elements.summaryText.textContent = payload.summary;
                    this.elements.summaryResult?.classList.remove('hidden');
                }
            }

            // Speak the message
            const message = payload.spoken_message || "İş başarıyla oluşturuldu.";
            this.speak(message);

            this.updateUI('idle');
        }

        handleError(data) {
            const payload = this.getData(data);
            this.showNotification('Hata', payload.message, 'danger');
            this.speak("Bir hata oluştu.");
            this.updateUI('idle');
        }

        getData(data) {
            return Array.isArray(data) ? data[0] : data;
        }

        // --- Helpers ---

        get component() {
            return Livewire.find(this.elements.startBtn.closest('[wire\\:id]').getAttribute('wire:id'));
        }

        updateUI(state) {
            this.state.isProcessing = state === 'processing';

            const { startBtn, recordingIndicator, loading, statusText } = this.elements;

            switch (state) {
                case 'recording':
                    startBtn.style.display = 'none';
                    recordingIndicator.style.display = 'flex';
                    loading.style.display = 'none';
                    statusText.textContent = 'Dinliyorum...';
                    statusText.className = 'h-6 text-base font-medium text-red-600 dark:text-red-400 animate-pulse';
                    break;
                case 'processing':
                    recordingIndicator.style.display = 'none';
                    loading.style.display = 'flex';
                    statusText.textContent = 'İşleniyor...';
                    statusText.className = 'h-6 text-base font-medium text-gray-600 dark:text-gray-300';
                    break;
                case 'assignment':
                    statusText.textContent = 'Kişi ismi bekleniyor...';
                    statusText.className = 'h-6 text-base font-medium text-primary-600 dark:text-primary-400';
                    break;
                case 'idle':
                default:
                    loading.style.display = 'none';
                    recordingIndicator.style.display = 'none';
                    // Only show start button if form is not present (logic handled by Livewire view mostly)
                    startBtn.style.display = 'flex';
                    statusText.textContent = '';
                    break;
            }
        }

        updateTimer() {
            const elapsed = Math.floor((Date.now() - this.state.startTime) / 1000);
            const minutes = String(Math.floor(elapsed / 60)).padStart(2, '0');
            const seconds = String(elapsed % 60).padStart(2, '0');
            if (this.elements.timer) this.elements.timer.textContent = `${minutes}:${seconds} `;
        }

        speak(text, delay = 0) {
            if (!('speechSynthesis' in window)) return Promise.resolve();

            return new Promise(resolve => {
                setTimeout(() => {
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'tr-TR';
                    utterance.rate = 1.3; // Faster natural talk as requested
                    utterance.onend = resolve;
                    speechSynthesis.speak(utterance);
                }, delay * 1000);
            });
        }

        showNotification(title, body, type) {
            new FilamentNotification()
                .title(title)
                .body(body)
            [type]() // .danger(), .success() etc
                .send();
        }
    };
}
// Initialize
// window.VoiceIssueManager = VoiceIssueManager; // Removed redundant initialization
