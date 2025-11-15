<?php

namespace Visiosoft\Kanban;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Visiosoft\Kanban\Resources\BoardResource;
use Visiosoft\Kanban\Resources\IssueResource;

class KanbanServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('kanban')
            ->hasConfigFile()
            ->hasMigrations([
                'create_boards_table',
                'create_issues_table',
            ]);
    }

    public function packageBooted(): void
    {
        // Register Filament resources
        if (class_exists(\Filament\Facades\Filament::class)) {
            \Filament\Facades\Filament::serving(function () {
                \Filament\Facades\Filament::registerResources([
                    BoardResource::class,
                    IssueResource::class,
                ]);
            });
        }
    }
}
