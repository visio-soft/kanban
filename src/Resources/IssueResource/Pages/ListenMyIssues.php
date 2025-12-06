<?php

namespace Visiosoft\Kanban\Resources\IssueResource\Pages;

use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Visiosoft\Kanban\Models\Issue;
use Visiosoft\Kanban\Resources\IssueResource;

class ListenMyIssues extends Page
{
    protected static string $resource = IssueResource::class;

    protected static string $view = 'kanban::filament.resources.issue-resource.pages.listen-my-issues';

    protected static ?string $title = 'İş Dinle';

    protected static ?string $navigationLabel = 'İş Dinle';

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    public array $currentIssue = [];

    public bool $hasNewIssue = false;

    public array $issueHistory = [];

    public ?int $lastCheckedIssueId = null;

    public function mount(): void
    {
        // Get the latest issue ID assigned to this user
        $latestIssue = Issue::where('assigned_to', Auth::id())
            ->latest()
            ->first();

        $this->lastCheckedIssueId = $latestIssue?->id;
    }

    // Polling method - called every 3 seconds from blade
    public function checkForNewIssues(): void
    {
        $userId = Auth::id();

        // Get the latest issue assigned to this user
        $latestIssue = Issue::with('board')
            ->where('assigned_to', $userId)
            ->latest()
            ->first();

        if ($latestIssue && $latestIssue->id !== $this->lastCheckedIssueId) {
            // New issue found!
            $this->lastCheckedIssueId = $latestIssue->id;

            $priorityLabels = config('kanban.priorities', [
                'low' => 'Düşük',
                'medium' => 'Orta',
                'high' => 'Yüksek',
                'urgent' => 'Acil',
            ]);

            $issueData = [
                'id' => $latestIssue->id,
                'title' => $latestIssue->title,
                'description' => strip_tags($latestIssue->description ?? ''),
                'priority' => $latestIssue->priority,
                'priority_label' => $priorityLabels[$latestIssue->priority] ?? 'Orta',
                'due_date' => $latestIssue->due_date?->format('d.m.Y'),
                'due_date_raw' => $latestIssue->due_date?->format('Y-m-d'),
                'board_name' => $latestIssue->board?->name,
                'created_at' => $latestIssue->created_at->format('H:i'),
            ];

            $this->handleNewIssue($issueData);
        }
    }

    public function handleNewIssue(array $data): void
    {
        $this->currentIssue = $data;
        $this->hasNewIssue = true;

        // Add to history (keep last 10)
        array_unshift($this->issueHistory, $data);
        $this->issueHistory = array_slice($this->issueHistory, 0, 10);

        // Dispatch browser event for TTS
        $this->dispatch('new-issue-arrived', issue: $data);
    }

    public function dismissIssue(): void
    {
        $this->hasNewIssue = false;
    }

    public function clearCurrentIssue(): void
    {
        $this->currentIssue = [];
        $this->hasNewIssue = false;
    }
}
