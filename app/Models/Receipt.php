<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Receipt extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'image_contents',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    protected function imageContents(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attr) {
                $filenameFull = $attr['path'].'/'.$attr['filename'];

                return base64_encode(Storage::disk('receipts')->get($filenameFull));
            }
        );
    }
}
