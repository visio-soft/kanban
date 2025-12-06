<?php

namespace Visiosoft\Kanban\Resources\IssueResource\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
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

    public ?array $data = [];

    public bool $showForm = false;

    public string $transcribedText = '';

    public function mount(): void
    {
        $this->form->fill([
            'board_id' => Board::query()->where('is_active', true)->first()?->id,
            'status' => 'todo',
            'priority' => 'medium',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Görev Detayları')
                    ->schema([
                        Forms\Components\Select::make('board_id')
                            ->label('Pano')
                            ->options(Board::query()->where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->default(fn () => Board::query()->where('is_active', true)->first()?->id),

                        Forms\Components\TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Durum ve Öncelik')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options(config('kanban.statuses', [
                                'backlog' => 'Beklemede',
                                'todo' => 'Yapılacak',
                                'in_progress' => 'Devam Ediyor',
                                'review' => 'İncelemede',
                                'done' => 'Tamamlandı',
                            ]))
                            ->default('todo')
                            ->required(),

                        Forms\Components\Select::make('priority')
                            ->label('Öncelik')
                            ->options(config('kanban.priorities', [
                                'low' => 'Düşük',
                                'medium' => 'Orta',
                                'high' => 'Yüksek',
                                'urgent' => 'Acil',
                            ]))
                            ->default('medium')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Atama ve Zamanlama')
                    ->schema([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Atanan Kişi')
                            ->options(User::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Forms\Components\DateTimePicker::make('start_at')
                            ->label('Başlangıç Tarihi')
                            ->nullable()
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('due_date')
                            ->label('Bitiş Tarihi')
                            ->nullable()
                            ->seconds(false),
                    ])->columns(3),

                Forms\Components\Section::make('Etiketler')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Etiketler')
                            ->nullable(),
                    ]),
            ])
            ->statePath('data');
    }

    public function processVoiceInputWithSummary(array $voiceData): array
    {
        try {
            $transcribedText = $voiceData['text'] ?? '';
            $this->transcribedText = $transcribedText;

            if (empty($transcribedText)) {
                return [
                    'success' => false,
                    'summary' => null,
                    'needsAssignment' => false,
                ];
            }

            // Get assignable users
            $users = User::select('id', 'name', 'email')
                ->orderBy('name')
                ->get();

            // Process with OpenAI to extract task details
            $taskDetails = $this->extractTaskDetailsWithOpenAI($transcribedText, $users->toArray());

            // Create summary
            $summary = $this->createTaskSummary($taskDetails, $transcribedText);

            // Fill the form with extracted data
            $this->form->fill([
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

            // Check if assignment is needed
            $needsAssignment = empty($taskDetails['assigned_to']);

            if (! $needsAssignment) {
                $this->showForm = true;
            }

            return [
                'success' => true,
                'summary' => $summary,
                'needsAssignment' => $needsAssignment,
            ];
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata')
                ->body('Görev işlenirken bir hata oluştu: '.$e->getMessage())
                ->danger()
                ->send();

            return [
                'success' => false,
                'summary' => null,
                'needsAssignment' => false,
            ];
        }
    }

    public function assignTaskToUser(array $data): void
    {
        try {
            $assigneeName = $data['name'] ?? '';

            if (empty($assigneeName)) {
                $this->showForm = true;
                return;
            }

            // Find user by fuzzy matching
            $users = User::select('id', 'name')
                ->orderBy('name')
                ->get();

            $assignedUserId = null;
            $bestMatch = 0;
            $bestMatchedUser = null;

            foreach ($users as $user) {
                // Exact match check
                similar_text(
                    strtolower($assigneeName),
                    strtolower($user->name),
                    $percent
                );

                // Contains check
                $nameParts = explode(' ', strtolower($user->name));
                $searchParts = explode(' ', strtolower($assigneeName));

                foreach ($searchParts as $searchPart) {
                    foreach ($nameParts as $namePart) {
                        if (strlen($searchPart) >= 3 && str_contains($namePart, $searchPart)) {
                            $percent = max($percent, 80);
                        }
                    }
                }

                if ($percent > $bestMatch) {
                    $bestMatch = $percent;
                    $assignedUserId = $user->id;
                    $bestMatchedUser = $user;
                }
            }

            // Update form with assigned user
            $currentData = $this->form->getState();
            $currentData['assigned_to'] = $assignedUserId;
            $this->form->fill($currentData);

            $this->showForm = true;

            if ($assignedUserId && $bestMatchedUser) {
                Notification::make()
                    ->title('Başarılı')
                    ->body("Görev {$bestMatchedUser->name} kişisine atandı.")
                    ->success()
                    ->send();

                $this->dispatch('userAssigned', name: $bestMatchedUser->name);
            } else {
                Notification::make()
                    ->title('Uyarı')
                    ->body('Belirtilen isimde kullanıcı bulunamadı. Lütfen manuel seçin.')
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            $this->showForm = true;
            Notification::make()
                ->title('Hata')
                ->body('Atama yapılırken bir hata oluştu: '.$e->getMessage())
                ->danger()
                ->send();
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
        $systemPrompt .= "- Return ONLY valid JSON, no additional text\n\n";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
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

    public function createIssue(): void
    {
        $data = $this->form->getState();

        try {
            $issue = Issue::create($data);

            Notification::make()
                ->title('Başarılı')
                ->body('Görev başarıyla oluşturuldu.')
                ->success()
                ->send();

            $this->redirect(IssueResource::getUrl('index'));
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata')
                ->body('Görev oluşturulurken bir hata oluştu: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetForm(): void
    {
        $this->showForm = false;
        $this->transcribedText = '';
        $this->form->fill([
            'board_id' => Board::query()->where('is_active', true)->first()?->id,
            'status' => 'todo',
            'priority' => 'medium',
        ]);
    }
}
