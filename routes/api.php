<?php

use Illuminate\Support\Facades\Route;
use Visiosoft\Kanban\Http\Controllers\API\BoardController;
use Visiosoft\Kanban\Http\Controllers\API\IssueController;

Route::group(['prefix' => 'api/kanban', 'middleware' => ['api']], function () {
    Route::apiResource('boards', BoardController::class)->except(['destroy']);
    Route::apiResource('issues', IssueController::class)->except(['destroy']);
    
    Route::post('issues/{id}/change-priority', [IssueController::class, 'changePriority']);
    Route::post('issues/{id}/change-assignee', [IssueController::class, 'changeAssignee']);
    Route::post('issues/{id}/change-due-date', [IssueController::class, 'changeDueDateTime']);
    Route::post('issues/{id}/change-start-date', [IssueController::class, 'changeStartDateTime']);
    Route::post('issues/{id}/change-status', [IssueController::class, 'changeStatus']);
    Route::get('issue/users', [IssueController::class, 'users']);
});
