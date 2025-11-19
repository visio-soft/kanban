# Kanban Installation

## Quick Install

```bash
composer require visio/kanban
php artisan vendor:publish --tag="kanban-config" # optional overrides
php artisan migrate
```

The migration adds `boards` and `issues` tables. Skipping the publish step keeps package defaults intact.

## Configure

`config/kanban.php` exposes the knobs most teams touch:

```php
'statuses' => ['new' => 'New', 'in_progress' => 'In Progress', ...],
'priorities' => ['critical' => 'Critical', 'high' => 'High', ...],
'default_board_color' => '#22c55e',
'soft_deletes' => true,
```

Tune these and clear the cache when needed:

```bash
php artisan config:clear
php artisan cache:clear
```

## Add to Filament Panel

- Prefer the plugin: it hooks everything up in one line.

```php
use Visiosoft\Kanban\Plugins\KanbanPlugin;

->plugins([
    KanbanPlugin::make(),
])
```

- If you need full control (multiple panels, conditional registration, custom middleware), wire the resources and pages manually:

```php
use Filament\Panel;
use Visiosoft\Kanban\Pages\{AllBoardsKanban, MyIssuesKanban};
use Visiosoft\Kanban\Resources\{BoardResource, IssueResource};

public function panel(Panel $panel): Panel
{
    return $panel
        ->navigationGroup('Kanban')
        ->resources([
            BoardResource::class,
            IssueResource::class,
        ])
        ->pages([
            AllBoardsKanban::class,
            MyIssuesKanban::class,
        ]);
}
```
