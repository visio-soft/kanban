<?php

namespace Visiosoft\Kanban\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Visiosoft\Kanban\Models\Issue;

class MyIssuesKanban extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'kanban::pages.my-issues-kanban';

    protected static ?string $navigationGroup = 'Kanban';

    protected static ?string $navigationLabel = 'Görevlerim';

    protected static ?string $title = 'Görevlerim';

    protected static ?int $navigationSort = -1;

    public function getMyIssuesCount(): int
    {
        return Issue::where('assigned_to', Auth::id())
            ->whereIn('status', ['backlog', 'todo', 'in_progress'])
            ->count();
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Issue::where('assigned_to', Auth::id())
            ->whereIn('status', ['backlog', 'todo', 'in_progress'])
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = Issue::where('assigned_to', Auth::id())
            ->whereIn('status', ['backlog', 'todo', 'in_progress'])
            ->count();

        if ($count > 10) {
            return 'danger';
        } elseif ($count > 5) {
            return 'warning';
        }

        return 'primary';
    }

    public function getIssuesByStatus(string $status)
    {
        return Issue::where('assigned_to', Auth::id())
            ->where('status', $status)
            ->with(['board', 'assignedUser'])
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOverdueIssues()
    {
        return Issue::where('assigned_to', Auth::id())
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['done'])
            ->with(['board', 'assignedUser'])
            ->orderBy('due_date')
            ->get();
    }

    public function updateIssueStatus(int $issueId, string $newStatus): void
    {
        $issue = Issue::where('id', $issueId)
            ->where('assigned_to', Auth::id())
            ->first();

        if ($issue) {
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

    public function getPriorityColor(string $priority): string
    {
        return match ($priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'medium' => 'primary',
            'low' => 'gray',
            default => 'gray',
        };
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
