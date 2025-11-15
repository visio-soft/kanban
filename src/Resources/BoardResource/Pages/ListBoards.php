<?php

namespace Visiosoft\Kanban\Resources\BoardResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Visiosoft\Kanban\Resources\BoardResource;

class ListBoards extends ListRecords
{
    protected static string $resource = BoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
