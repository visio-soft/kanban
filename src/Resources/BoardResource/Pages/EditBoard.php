<?php

namespace Visiosoft\Kanban\Resources\BoardResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Visiosoft\Kanban\Resources\BoardResource;

class EditBoard extends EditRecord
{
    protected static string $resource = BoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
