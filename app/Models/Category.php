<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Category extends Model
{
    use HasFactory;

    const DEFAULT_NAME = 'Uncategorized';

    const DEFAULT_COLOR = '#e5e7eb';

    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public static function grouped(User $user): Collection
    {
        return static::query()
            ->withCount('expenses')
            ->whereNull('parent_id')
            ->where('user_id', $user->id)
            ->with(['children' => fn ($q) => $q->withCount('expenses')->orderBy('name')])
            ->orderBy('name')
            ->get();
    }

    public static function createDefault(User $user)
    {
        $default = [
            'user_id' => $user->id,
            'name'    => self::DEFAULT_NAME,
            'color'   => self::DEFAULT_COLOR,

        ];

        self::create($default);
    }
}
