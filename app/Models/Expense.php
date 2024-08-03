<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;
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
        'has_fees',
        'amount_pretty',
        'foreign_currency_conversion_fee_pretty',
        'total',
        'effective_date_pretty',
        'has_receipt',
    ];

    public function toSearchableArray()
    {
        return [
            'id'                              => $this->id,
            'payee'                           => $this->payee,
            'amount'                          => $this->amount,
            'foreign_currency_conversion_fee' => $this->foreign_currency_conversion_fee,
            'transaction_date'                => $this->transaction_date,
            'effective_date'                  => $this->effective_date,
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

    protected function foreignCurrencyConversionFee(): Attribute
    {
        return Attribute::make(
            set: function (?string $value) {
                if ($value === null) {
                    return 0;
                }

                return app(DecimalMoneyParser::class)->parse($value, new Currency('USD'))->getAmount();
            }
        );
    }

    protected function foreignCurrencyConversionFeePretty(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attr) {
                $money = new Money($attr['foreign_currency_conversion_fee'], new Currency($attr['currency']));

                return app(IntlMoneyFormatter::class)->format($money);
            }
        );
    }

    protected function hasFees(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attr) {
                return $attr['foreign_currency_conversion_fee'] > 0;
            }
        );
    }

    protected function total(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attr) {
                $amount = Money::USD($attr['amount']);
                $fees = Money::USD($attr['foreign_currency_conversion_fee']);

                $total = $amount->add($fees);

                return app(IntlMoneyFormatter::class)->format($total);
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
            get: function (?string $value) {
                if (Route::getCurrentRoute()->getName() === 'expenses.edit') {
                    return $value;
                }

                return Str::markdown($value ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]);
            }
        );
    }

    protected function hasReceipt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->receipts()->count() > 0
        );
    }
}
