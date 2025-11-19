<?php

namespace Visiosoft\Kanban\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Visiosoft\Kanban\Models\Board;
use Visiosoft\Kanban\Models\Issue;

class AllBoardsKanban extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string $view = 'kanban::pages.all-boards-kanban';

    protected static ?string $navigationGroup = 'Kanban';

    protected static ?string $navigationLabel = 'Kanban Panosu';

    protected static ?string $title = 'Kanban Panosu';

    protected static ?int $navigationSort = 0;

    public ?int $selectedBoardId = null;

    public function mount(): void
    {
        // Select the first active board by default
        $firstBoard = Board::where('is_active', true)
            ->orderBy('order')
            ->first();

        $this->selectedBoardId = $firstBoard?->id;
    }

    public function getBoards()
    {
        return Board::where('is_active', true)
            ->withCount('issues')
            ->orderBy('order')
            ->get();
    }

    public function getSelectedBoard()
    {
        if (! $this->selectedBoardId) {
            return null;
        }

        return Board::with([
            'issues' => function ($query) {
                $query->orderBy('order')->orderBy('created_at', 'desc');
            },
        ])
            ->find($this->selectedBoardId);
    }

    public function getIssuesByStatus(string $status)
    {
        if (! $this->selectedBoardId) {
            return collect();
        }

        return Issue::where('board_id', $this->selectedBoardId)
            ->where('status', $status)
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->with('assignedUser')
            ->get();
    }

    public function selectBoard(int $boardId): void
    {
        $this->selectedBoardId = $boardId;
    }

    public function updateIssueStatus(int $issueId, string $newStatus): void
    {
        $issue = Issue::find($issueId);

        if ($issue && $issue->board_id === $this->selectedBoardId) {
            $issue->update(['status' => $newStatus]);
            $this->dispatch('issue-updated');
        }
    }

    public function getStatuses(): array
    {
        return config('kanban.statuses', [
            'backlog' => 'Backlog',
            'todo' => 'To Do',
            'in_progress' => 'In Progress',
            'review' => 'In Review',
            'done' => 'Done',
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }
}
