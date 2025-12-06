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
            \Filament\Actions\Action::make('listenIssues')
                ->label('İş Dinle')
                ->icon('heroicon-o-speaker-wave')
                ->color('success')
                ->url(fn (): string => IssueResource::getUrl('listen'))
                ->openUrlInNewTab(),
            \Filament\Actions\Action::make('createWithVoice')
                ->label('Sesli İş Oluştur')
                ->icon('heroicon-o-microphone')
                ->color('danger')
                ->url(fn (): string => IssueResource::getUrl('create-with-voice'))
                ->openUrlInNewTab(),
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
