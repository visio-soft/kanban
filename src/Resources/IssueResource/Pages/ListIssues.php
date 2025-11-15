<?php

namespace Visiosoft\Kanban\Resources\IssueResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Visiosoft\Kanban\Resources\IssueResource;

class ListIssues extends ListRecords
{
    protected static string $resource = IssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
