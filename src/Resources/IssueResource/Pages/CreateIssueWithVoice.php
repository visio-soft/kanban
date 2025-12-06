<?php

namespace Visiosoft\Kanban\Resources\IssueResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Http;
use Visiosoft\Kanban\Models\Board;
use Visiosoft\Kanban\Models\Issue;
use Visiosoft\Kanban\Resources\IssueResource;
use App\Models\User;

class CreateIssueWithVoice extends Page
{
    protected static string $resource = IssueResource::class;

    protected static string $view = 'kanban::filament.resources.issue-resource.pages.create-issue-with-voice';

    protected static ?string $title = 'Sesli İş Oluştur';

    protected static ?string $navigationLabel = 'Sesli İş Oluştur';

    public string $transcribedText = '';
    
    public ?int $createdIssueId = null;
    
    public ?string $createdIssueUrl = null;
    
    public ?string $createdIssueSummary = null;

    /**
     * Get users who are currently on the ListenMyIssues page.
     * Checks for the cache key set by the listener page heartbeat.
     */
    public function getActiveListeners(): \Illuminate\Database\Eloquent\Collection
    {
        // Get all users (optimize if needed for large user bases)
        $users = User::all();
        
        return $users->filter(function ($user) {
            // Exclude self
            if ($user->id === auth()->id()) {
                return false;
            }
            $key = 'user_listening:' . $user->id;
            return \Illuminate\Support\Facades\Cache::has($key);
        })->values();
    }





    public function processVoiceInputWithSummary(array $voiceData): void
    {
        try {
            $transcribedText = $voiceData['text'] ?? '';
            $this->transcribedText = $transcribedText;

            // Normalize text for checking
            $normalizedText = mb_strtolower(trim($transcribedText));
            // Classic system prompts
            $systemPhrases = ['dinliyorum', 'tamam', 'hazır', 'dinleniyor', 'dinliyorum.'];
            // Whisper hallucinations (silence artifacts)
            $hallucinationPhrases = [
                'altyazı m.k.', 'altyazı', 'alt yazı', 'subtitle', 'mbc', 
                'destek olun', 'izlediğiniz için teşekkürler', 'amara.org'
            ];

            // 1. Check for silence/empty
            if (empty($transcribedText)) {
                $this->dispatch('voice-retry', message: 'Ses algılanmadı, tekrar dinleniyor...');
                return;
            }

            // 2. Check for hallucinations (treat as silence)
            foreach ($hallucinationPhrases as $phrase) {
                if (str_contains($normalizedText, $phrase)) {
                    $this->dispatch('voice-retry', message: 'Ses algılanmadı (Gürültü filtrelendi), tekrar dinleniyor...');
                    return;
                }
            }

            // 3. Check for system prompts (treat as invalid input)
            if (in_array($normalizedText, $systemPhrases)) {
                $this->dispatch('voice-retry', message: 'Anlaşılmadı, tekrar dinleniyor...');
                return;
            }

            // Get assignable users
            $users = User::select('id', 'name', 'email')
                ->orderBy('name')
                ->get();

            // Process with OpenAI to extract task details
            $taskDetails = $this->extractTaskDetailsWithOpenAI($transcribedText, $users->toArray());

            // Check if OpenAI marked it as invalid
            if (($taskDetails['title'] ?? '') === 'INVALID_INPUT') {
                 $this->dispatch('voice-retry', message: 'Geçersiz giriş veya sistem mesajı algılandı, tekrar dinleniyor...');
                 return;
            }

            // Create the issue directly
            $issue = Issue::create([
                'board_id' => $taskDetails['board_id'] ?? Board::query()->where('is_active', true)->first()?->id,
                'title' => $taskDetails['title'] ?? $transcribedText,
                'description' => $taskDetails['description'] ?? null,
                'status' => $taskDetails['status'] ?? 'todo',
                'priority' => $taskDetails['priority'] ?? 'medium',
                'assigned_to' => $taskDetails['assigned_to'] ?? null,
                'start_at' => $taskDetails['start_at'] ?? null,
                'due_date' => $taskDetails['due_date'] ?? null,
                'tags' => $taskDetails['tags'] ?? null,
            ]);

            // Create summary
            $summary = $this->createTaskSummary($taskDetails, $transcribedText);

            // Build spoken message
            $assignedUser = $taskDetails['assigned_to'] ? User::find($taskDetails['assigned_to']) : null;
            $firstName = $assignedUser ? explode(' ', trim($assignedUser->name))[0] : '';
            $taskTitle = $taskDetails['title'] ?? 'yeni görevi';
            
            if ($assignedUser) {
                $spokenMessage = "Tamam, {$taskTitle} işini {$firstName} atadım.";
            } else {
                $spokenMessage = "Tamam, {$taskTitle} işini oluşturdum.";
            }

            // Get issue URL
            $issueUrl = IssueResource::getUrl('edit', ['record' => $issue->id]);

            // Store issue data in component properties
            $this->createdIssueId = $issue->id;
            $this->createdIssueUrl = $issueUrl;
            $this->createdIssueSummary = $summary;

            // Dispatch success event with issue link
            $this->dispatch('voice-issue-created', [
                'summary' => $summary,
                'spoken_message' => $spokenMessage,
                'issue_url' => $issueUrl,
                'issue_id' => $issue->id,
            ]);


        } catch (\Exception $e) {
            $this->dispatch('voice-error', message: 'Görev işlenirken bir hata oluştu: '.$e->getMessage());
        }
    }



    protected function createTaskSummary(array $taskDetails, string $originalText): string
    {
        $summary = [];

        $summary[] = '📋 Görev: '.$taskDetails['title'];

        if (! empty($taskDetails['start_at'])) {
            $startDate = \Carbon\Carbon::parse($taskDetails['start_at']);
            $summary[] = '📅 Başlangıç: '.$startDate->format('d.m.Y H:i');
        }

        if (! empty($taskDetails['due_date'])) {
            $dueDate = \Carbon\Carbon::parse($taskDetails['due_date']);
            $summary[] = '⏰ Bitiş: '.$dueDate->format('d.m.Y H:i');
        }

        if (! empty($taskDetails['assigned_to'])) {
            $user = User::find($taskDetails['assigned_to']);
            if ($user) {
                $summary[] = '👤 Atanan: '.$user->name;
            }
        }

        $priorityLabels = config('kanban.priorities', [
            'low' => 'Düşük',
            'medium' => 'Orta',
            'high' => 'Yüksek',
            'urgent' => 'Acil',
        ]);
        
        $summary[] = '⚡ Öncelik: '.($priorityLabels[$taskDetails['priority']] ?? 'Orta');

        return implode("\n", $summary);
    }

    protected function extractTaskDetailsWithOpenAI(string $text, array $users): array
    {
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            throw new \Exception('OpenAI API key is not configured');
        }

        $usersList = collect($users)->map(fn ($user) => [
            'id' => $user['id'],
            'name' => $user['name'],
        ])->toArray();

        $now = now();
        $tomorrow = now()->addDay();
        $currentDateTime = $now->format('Y-m-d H:i:s');
        $tomorrowDate = $tomorrow->format('Y-m-d');

        $systemPrompt = "You are an AI assistant that extracts task information from Turkish text.\n";
        $systemPrompt .= "Extract the following information and return as JSON:\n";
        $systemPrompt .= "- title: The main task description (required)\n";
        $systemPrompt .= "- description: Additional details if any\n";
        $systemPrompt .= "- start_at: Start date and time in ISO 8601 format (YYYY-MM-DDTHH:MM:SS) - ALWAYS set if date is mentioned\n";
        $systemPrompt .= "- due_date: Due date and time in ISO 8601 format (YYYY-MM-DDTHH:MM:SS) - ALWAYS set if date is mentioned\n";
        $systemPrompt .= "- assigned_to: User ID from the list who best matches the mentioned name\n";
        $systemPrompt .= "- priority: low, medium, high, or urgent (default: medium)\n";
        $systemPrompt .= "- tags: Array of relevant tags\n\n";
        $systemPrompt .= "Available users:\n";
        $systemPrompt .= json_encode($usersList, JSON_UNESCAPED_UNICODE);
        $systemPrompt .= "\n\nCurrent date and time: {$currentDateTime}\n\n";
        $systemPrompt .= "Rules:\n";
        $systemPrompt .= "- Tasks can be maximum 8 hours long\n";
        $systemPrompt .= "- If start time is mentioned but not end time, add 8 hours to start time for due_date\n";
        $systemPrompt .= "- If end time is mentioned but not start time, subtract 8 hours from end time for start_at\n";
        $systemPrompt .= "- Match Turkish time expressions:\n";
        $systemPrompt .= "  * yarın = tomorrow ({$tomorrowDate})\n";
        $systemPrompt .= "  * bugün = today ({$now->format('Y-m-d')})\n";
        $systemPrompt .= "  * sabah = morning (09:00)\n";
        $systemPrompt .= "  * öğlen = noon (12:00)\n";
        $systemPrompt .= "  * akşam = evening (21:00)\n";
        $systemPrompt .= "  * akşama kadar = until evening (21:00)\n";
        $systemPrompt .= "- Match person names using fuzzy matching (Samet matches \"Samet Yılmaz\")\n";
        $systemPrompt .= "- IMPORTANT: If a date is mentioned (yarın, bugün), you MUST include both start_at and due_date\n";
        $systemPrompt .= "- FILTERING RULES:\n";
        $systemPrompt .= "  * IGNORE the word 'Dinliyorum' or 'Dinleniyor' if it appears at the start of the sentence. Treat the rest of the sentence as the task.\n";
        $systemPrompt .= "  * IGNORE phrases like 'Altyazı M.K.', 'Altyazı', 'Subtitle' which are silence artifacts.\n";
        $systemPrompt .= "  * If the text is ONLY 'Dinliyorum', 'Dinleniyor', 'Tamam', 'Altyazı' or noise, return {\"title\": \"INVALID_INPUT\"}.\n";
        $systemPrompt .= "- Return ONLY valid JSON, no additional text\n\n";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $text,
                ],
            ],
            'temperature' => 0.3,
            'max_tokens' => 500,
        ]);

        if (! $response->successful()) {
            throw new \Exception('OpenAI API error: '.$response->body());
        }

        $result = $response->json();
        $content = $result['choices'][0]['message']['content'] ?? '{}';

        // Clean JSON from markdown code blocks if present
        $content = preg_replace('/```json\s*|\s*```/', '', $content);
        $content = trim($content);

        $extractedData = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response from OpenAI: '.json_last_error_msg());
        }

        return [
            'board_id' => Board::query()->where('is_active', true)->first()?->id,
            'title' => $extractedData['title'] ?? $text,
            'description' => $extractedData['description'] ?? null,
            'status' => 'todo',
            'priority' => $extractedData['priority'] ?? 'medium',
            'assigned_to' => $extractedData['assigned_to'] ?? null,
            'start_at' => $extractedData['start_at'] ?? null,
            'due_date' => $extractedData['due_date'] ?? null,
            'tags' => $extractedData['tags'] ?? null,
        ];
    }

}
