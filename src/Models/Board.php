<?php

namespace Visiosoft\Kanban\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Board",
 *     title="Board",
 *     description="Board model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="My Board"),
 *     @OA\Property(property="description", type="string", example="Board description"),
 *     @OA\Property(property="color", type="string", example="#ffffff"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="BoardStoreRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="New Board"),
 *     @OA\Property(property="description", type="string", example="Description"),
 *     @OA\Property(property="color", type="string", example="#000000"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 * 
 * @OA\Schema(
 *     schema="BoardUpdateRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="Updated Board"),
 *     @OA\Property(property="description", type="string", example="Updated Description"),
 *     @OA\Property(property="color", type="string", example="#000000"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 */
class Board extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function activeIssues(): HasMany
    {
        return $this->issues()->whereNull('deleted_at');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
