<?php

namespace Visiosoft\Kanban\Resources\IssueResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Visiosoft\Kanban\Resources\IssueResource;

class EditIssue extends EditRecord
{
    protected static string $resource = IssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
