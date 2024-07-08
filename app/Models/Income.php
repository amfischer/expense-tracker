<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class Income extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'payment_date'   => 'datetime:Y-m-d',
        'effective_date' => 'datetime:Y-m-d',
    ];

    protected $appends = [
        'amount_pretty',
        'effective_date_pretty',
    ];

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

}
