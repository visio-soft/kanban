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
        'backlog' => 'Beklemede',
        'todo' => 'Yapılacak',
        'in_progress' => 'Devam Ediyor',
        'review' => 'İncelemede',
        'done' => 'Tamamlandı',
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
        'low' => 'Düşük',
        'medium' => 'Orta',
        'high' => 'Yüksek',
        'urgent' => 'Acil',
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
