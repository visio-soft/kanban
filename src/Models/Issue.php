<?php

namespace Visiosoft\Kanban\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Issue",
 *     title="Issue",
 *     description="Issue model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="board_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="My Issue"),
 *     @OA\Property(property="description", type="string", example="Issue description"),
 *     @OA\Property(property="status", type="string", example="todo"),
 *     @OA\Property(property="priority", type="string", example="high"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="due_date", type="string", format="date", example="2023-01-01"),
 *     @OA\Property(property="start_at", type="string", format="date-time", example="2023-01-01 12:00:00"),
 *     @OA\Property(property="assigned_to", type="integer", example=1),
 *     @OA\Property(property="tags", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="IssueStoreRequest",
 *     type="object",
 *     required={"title", "board_id", "status"},
 *     @OA\Property(property="title", type="string", example="New Issue"),
 *     @OA\Property(property="board_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="todo"),
 *     @OA\Property(property="description", type="string", example="Issue details"),
 *     @OA\Property(property="priority", type="string", example="normal"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
 *     @OA\Property(property="start_at", type="string", format="date-time", example="2023-12-31 10:00:00"),
 *     @OA\Property(property="assigned_to", type="integer", example=1),
 *     @OA\Property(property="tags", type="array", @OA\Items(type="string"))
 * )
 *
 * @OA\Schema(
 *     schema="IssueUpdateRequest",
 *     type="object",
 *     @OA\Property(property="title", type="string", example="Updated Issue"),
 *     @OA\Property(property="board_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="in_progress"),
 *     @OA\Property(property="description", type="string", example="Updated details"),
 *     @OA\Property(property="priority", type="string", example="high"),
 *     @OA\Property(property="order", type="integer", example=2),
 *     @OA\Property(property="due_date", type="string", format="date", example="2024-01-01"),
 *     @OA\Property(property="assigned_to", type="integer", example=2),
 *     @OA\Property(property="tags", type="array", @OA\Items(type="string"))
 * )
 */
class Issue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'board_id',
        'title',
        'description',
        'status',
        'order',
        'priority',
        'due_date',
        'start_at',
        'assigned_to',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'due_date' => 'date',
        'start_at' => 'datetime',
        'order' => 'integer',
    ];

    protected static function booted(): void
    { 
        
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'assigned_to');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['done']);
    }
}
