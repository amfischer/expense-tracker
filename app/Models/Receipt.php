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

    const ALLOWED_IMAGE_MIME_TYPES = [
        'image/png',
        'image/jpeg',
        'image/webp',
    ];

    protected $guarded = [];

    protected $appends = [
        'is_image',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    protected function isImage(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attr) {
                return in_array($attr['mimetype'], self::ALLOWED_IMAGE_MIME_TYPES);
            }
        );
    }

    protected function base64(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attr) {
                $expense = Expense::find($attr['expense_id']);
                $file = $expense->getReceiptStoragePath() . '/' . $attr['filename'];

                return base64_encode(Storage::disk('receipts')->get($file));
            }
        );
    }

    public function filenameWithPath()
    {
        return $this->expense->getReceiptStoragePath() . '/' . $this->filename;
    }
}
