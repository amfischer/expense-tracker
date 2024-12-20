<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class Expense extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $casts = [
        'transaction_date'    => 'datetime:Y-m-d',
        'effective_date'      => 'datetime:Y-m-d',
        'is_business_expense' => 'boolean',
    ];

    protected $appends = [
        'amount_pretty',
        'effective_date_pretty',
        'has_receipt',
        'notes_raw',
    ];

    public function toSearchableArray()
    {
        return [
            'id'               => $this->id,
            'payee'            => $this->payee,
            'amount'           => $this->amount,
            'transaction_date' => $this->transaction_date,
            'effective_date'   => $this->effective_date,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    protected function amount(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                return app(DecimalMoneyParser::class)->parse($value, new Currency('USD'))->getAmount();
            }
        );
    }

    protected function amountPretty(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attr) {
                $money = new Money($attr['amount'], new Currency($attr['currency']));

                return app(IntlMoneyFormatter::class)->format($money);
            }
        );
    }

    public function getReceiptStoragePath(): string
    {
        return 'user_' . $this->user->id . '/' . date('Y/m', strtotime($this->transaction_date));
    }

    protected function effectiveDatePretty(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attr) {
                return date('M d, Y', strtotime($attr['effective_date']));
            }
        );
    }

    protected function notes(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => Str::markdown($value ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false])
        );
    }

    protected function notesRaw(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attr) => $attr['notes'] ?? ''
        );
    }

    protected function hasReceipt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->receipts()->count() > 0
        );
    }
}
