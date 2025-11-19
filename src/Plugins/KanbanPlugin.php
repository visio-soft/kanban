<?php

namespace Visiosoft\Kanban\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Visiosoft\Kanban\Pages\AllBoardsKanban;
use Visiosoft\Kanban\Pages\MyIssuesKanban;
use Visiosoft\Kanban\Resources\BoardResource;
use Visiosoft\Kanban\Resources\IssueResource;

class KanbanPlugin implements Plugin
{
    public static function make(): static
    {
        return new static;
    }

    public function getId(): string
    {
        return 'kanban';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                BoardResource::class,
                IssueResource::class,
            ])
            ->pages([
                AllBoardsKanban::class,
                MyIssuesKanban::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Nothing to boot yet.
    }
}
