# Installation Guide

## Step 1: Install the Package

```bash
composer require visiosoft/kanban
```

## Step 2: Publish Assets (Optional)

Publish the configuration file if you want to customize statuses and priorities:

```bash
php artisan vendor:publish --tag="kanban-config"
```

This will create `config/kanban.php` where you can customize:
- Issue statuses
- Priority levels
- Default board color
- Soft delete settings

## Step 3: Run Migrations

```bash
php artisan migrate
```

This will create two tables:
- `boards` - For managing kanban boards
- `issues` - For managing issues/tasks

## Step 4: Access the Resources

After installation, navigate to your Filament admin panel. You'll find two new menu items under the "Kanban" group:
- **Boards** - Manage your kanban boards
- **Issues** - Manage your issues/tasks

## Configuration Options

### Customizing Statuses

Edit `config/kanban.php`:

```php
'statuses' => [
    'new' => 'New',
    'in_progress' => 'In Progress',
    'testing' => 'Testing',
    'deployed' => 'Deployed',
],
```

### Customizing Priorities

```php
'priorities' => [
    'critical' => 'Critical',
    'high' => 'High',
    'normal' => 'Normal',
    'low' => 'Low',
],
```

### Changing Default Board Color

```php
'default_board_color' => '#22c55e', // Green color
```

## Usage Examples

### Creating a Board

1. Go to **Boards** in your Filament panel
2. Click **New Board**
3. Fill in the details:
   - Name: "Development Board"
   - Description: "Track development tasks"
   - Color: Choose a color
   - Order: 1
   - Is Active: Yes

### Creating an Issue

1. Go to **Issues** in your Filament panel
2. Click **New Issue**
3. Fill in the details:
   - Board: Select a board
   - Title: "Implement user authentication"
   - Description: Add detailed description
   - Status: Select status (defaults to backlog)
   - Priority: Select priority (defaults to medium)
   - Assigned To: Select a user (optional)
   - Due Date: Set deadline (optional)
   - Tags: Add tags like "backend", "security"

## Features Overview

### Board Management
- Create multiple boards for different projects
- Color-code boards for easy identification
- Set display order
- Toggle active/inactive status
- View issue count per board

### Issue Management
- Rich text descriptions
- Status tracking with customizable stages
- Priority levels with visual badges
- User assignment
- Due date tracking with overdue detection
- Tagging system
- Filtering and search
- Bulk actions

### Advanced Features
- **Soft Deletes**: Safely delete and restore records
- **Inline Editing**: Update issue status directly from the table
- **Overdue Highlighting**: Visual indication for overdue issues
- **Advanced Filters**: Filter by board, status, priority, overdue status
- **Bulk Actions**: Perform actions on multiple records

## Troubleshooting

### Resources Not Showing

Make sure your Filament panel is properly configured. The resources will automatically register when you visit any Filament page.

### Migration Errors

If you encounter foreign key errors, ensure the `users` table exists before running migrations.

### Configuration Not Applied

If changes to `config/kanban.php` aren't reflected:

```bash
php artisan config:clear
php artisan cache:clear
```

## Requirements

- PHP 8.2 or higher
- Laravel 11.x or 12.x
- FilamentPHP 3.x
- A users table (for issue assignment)

## Support

For issues, questions, or contributions, please visit:
https://github.com/visio-soft/kanban
