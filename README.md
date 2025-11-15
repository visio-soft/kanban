# Kanban Board for FilamentPHP 3

A comprehensive Kanban board and issue management plugin for FilamentPHP 3 using Laravel 12.

## Features

- 📋 **Board Management**: Create and manage multiple kanban boards
- 🎯 **Issue Tracking**: Full-featured issue management system
- 🎨 **Color Coding**: Customizable board colors
- 📊 **Status Tracking**: Multiple status stages (Backlog, To Do, In Progress, Review, Done)
- ⚡ **Priority Levels**: Low, Medium, High, and Urgent priorities
- 👥 **User Assignment**: Assign issues to team members
- 🏷️ **Tagging**: Organize issues with custom tags
- 📅 **Due Dates**: Set and track issue due dates
- 🔍 **Advanced Filtering**: Filter by status, priority, board, and overdue items
- 🗑️ **Soft Deletes**: Safely delete and restore boards and issues
- ⚙️ **Customizable**: Configure statuses, priorities, and colors

## Requirements

- PHP 8.2 or higher
- Laravel 11.x or 12.x
- FilamentPHP 3.x

## Installation

You can install the package via composer:

```bash
composer require visiosoft/kanban
```

Publish the config file (optional):

```bash
php artisan vendor:publish --tag="kanban-config"
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

The package comes with a configuration file that allows you to customize various aspects:

```php
return [
    'statuses' => [
        'backlog' => 'Backlog',
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'review' => 'In Review',
        'done' => 'Done',
    ],
    
    'priorities' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],
    
    'default_board_color' => '#3b82f6',
    'soft_deletes' => true,
];
```

## Usage

After installation, two new resources will be available in your Filament admin panel:

### Boards

Create and manage your kanban boards with:
- Custom names and descriptions
- Color coding for visual organization
- Order management
- Active/inactive status

### Issues

Manage your issues with:
- Title and rich text description
- Status tracking (configurable)
- Priority levels (configurable)
- User assignment
- Due dates and start dates
- Tags for organization
- Automatic overdue detection

## Database Schema

### Boards Table
- `id` - Primary key
- `name` - Board name
- `description` - Board description
- `color` - Color code
- `order` - Display order
- `is_active` - Active status
- `created_at`, `updated_at`, `deleted_at` - Timestamps

### Issues Table
- `id` - Primary key
- `board_id` - Foreign key to boards
- `title` - Issue title
- `description` - Issue description
- `status` - Current status
- `order` - Display order
- `priority` - Priority level
- `due_date` - Due date
- `start_at` - Start timestamp
- `assigned_to` - Foreign key to users
- `tags` - JSON array of tags
- `created_at`, `updated_at`, `deleted_at` - Timestamps

## Features in Detail

### Board Management
- Create multiple boards for different projects or teams
- Customize board colors for easy visual identification
- Set board order for custom sorting
- Toggle active/inactive status
- Track issue count per board

### Issue Management
- Rich text descriptions using Filament's RichEditor
- Inline status updates from the table view
- Priority badges with color coding
- Overdue issue highlighting
- Advanced filtering options
- Bulk actions support

### Soft Deletes
Both boards and issues support soft deletes, allowing you to:
- Safely delete records
- View deleted records
- Restore deleted records
- Permanently delete records

## Customization

You can customize the statuses and priorities by publishing the config file and modifying the arrays. The changes will be reflected throughout the application.

## Security

If you discover any security-related issues, please email security@visiosoft.com instead of using the issue tracker.

## Credits

- [Visiosoft](https://github.com/visio-soft)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.