<?php

namespace Visiosoft\Kanban;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Visiosoft\Kanban\Pages\AllBoardsKanban;
use Visiosoft\Kanban\Pages\MyIssuesKanban;
use Visiosoft\Kanban\Resources\BoardResource;
use Visiosoft\Kanban\Resources\IssueResource;

class KanbanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/kanban.php',
            'kanban'
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'kanban');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Register Livewire components from package
        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component(
                'visiosoft.kanban.resources.issue-resource.pages.listen-my-issues',
                \Visiosoft\Kanban\Resources\IssueResource\Pages\ListenMyIssues::class
            );
            \Livewire\Livewire::component(
                'visiosoft.kanban.resources.issue-resource.pages.create-issue-with-voice',
                \Visiosoft\Kanban\Resources\IssueResource\Pages\CreateIssueWithVoice::class
            );
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/kanban.php' => config_path('kanban.php'),
            ], 'kanban-config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'kanban-migrations');

            $this->publishes([
                __DIR__.'/../database/seeders/' => database_path('seeders'),
            ], 'kanban-seeders');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/kanban'),
            ], 'kanban-views');
        }

        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    BoardResource::class,
                    IssueResource::class,
                ]);

                Filament::registerPages([
                    AllBoardsKanban::class,
                    MyIssuesKanban::class,
                ]);
            });
        }
    }
}
