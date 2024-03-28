<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    const DEFAULT = 'Uncategorized';

    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public static function createDefault(User $user)
    {
        $default = [
            'user_id' => $user->id,
            'name'    => self::DEFAULT,
            'color'   => '#e5e7eb',

        ];

        self::create($default);
    }
}
