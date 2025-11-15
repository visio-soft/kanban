<?php

namespace Visiosoft\Kanban\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
