<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class Income extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $casts = [
        'payment_date'     => 'datetime:Y-m-d',
        'effective_date'   => 'datetime:Y-m-d',
        'is_earned_income' => 'boolean',
    ];

    protected $appends = [
        'amount_pretty',
        'effective_date_pretty',
    ];

    public function toSearchableArray()
    {
        return [
            'id'               => $this->id,
            'source'           => $this->source,
            'amount'           => $this->amount,
            'is_earned_income' => $this->is_earned_income,
            'payment_date'     => $this->payment_date,
            'effective_date'   => $this->effective_date,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
                $money = new Money($attr['amount'], new Currency('USD'));

                return app(IntlMoneyFormatter::class)->format($money);
            }
        );
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
            get: function (?string $value) {
                if (Route::getCurrentRoute()->getName() === 'incomes.edit') {
                    return $value;
                }

                return Str::markdown($value ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]);
            }
        );
    }
}
