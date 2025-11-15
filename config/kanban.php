<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kanban Board Statuses
    |--------------------------------------------------------------------------
    |
    | Define the default statuses for your kanban issues.
    |
    */
    'statuses' => [
        'backlog' => 'Backlog',
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'review' => 'In Review',
        'done' => 'Done',
    ],

    /*
    |--------------------------------------------------------------------------
    | Issue Priorities
    |--------------------------------------------------------------------------
    |
    | Define the priority levels for issues.
    |
    */
    'priorities' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Board Color
    |--------------------------------------------------------------------------
    |
    | The default color for new boards.
    |
    */
    'default_board_color' => '#3b82f6',

    /*
    |--------------------------------------------------------------------------
    | Enable Soft Deletes
    |--------------------------------------------------------------------------
    |
    | Enable or disable soft deletes for boards and issues.
    |
    */
    'soft_deletes' => true,
];
