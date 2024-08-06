<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    const ALLOWED_IMAGE_MIME_TYPES = [
        'image/png',
        'image/jpeg',
        'image/webp',
    ];

    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'is_image',
        'size_formatted',
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

    public function filenameWithPath()
    {
        return $this->expense->getReceiptStoragePath() . '/' . $this->filename;
    }

    protected function sizeFormatted(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attr) {
                $units = ['B', 'KB', 'MB', 'GB', 'TB'];

                $bytes = max($attr['size'], 0);
                $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                $pow = min($pow, count($units) - 1);

                $bytes /= pow(1024, $pow);

                return round($bytes, 0) . $units[$pow];
            }
        );

    }
}
