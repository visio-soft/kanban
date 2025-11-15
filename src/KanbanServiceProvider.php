<?php

namespace Visiosoft\Kanban;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Visiosoft\Kanban\Pages\AllBoardsKanban;
use Visiosoft\Kanban\Pages\MyIssuesKanban;
use Visiosoft\Kanban\Resources\BoardResource;
use Visiosoft\Kanban\Resources\IssueResource;

class KanbanServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('kanban')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_boards_table',
                'create_issues_table',
            ]);
    }

    public function packageBooted(): void
    {
        // Register Filament resources and pages
        if (class_exists(\Filament\Facades\Filament::class)) {
            \Filament\Facades\Filament::serving(function () {
                \Filament\Facades\Filament::registerResources([
                    BoardResource::class,
                    IssueResource::class,
                ]);

                \Filament\Facades\Filament::registerPages([
                    AllBoardsKanban::class,
                    MyIssuesKanban::class,
                ]);
            });
        }
    }
}
