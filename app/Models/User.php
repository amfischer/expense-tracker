<?php

namespace App\Models;

use App\Events\UserCreated;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    protected $dispatchesEvents = [
        'created' => UserCreated::class,
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    protected function categoriesArray(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->categories->map(function (Category $category, int $key) {
                    return ['id' => $category->id, 'name' => $category->name];
                })->sortBy('name')->values()->all();
            }
        );
    }

    protected function categoryIds(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->categories->map(fn (Category $category) => $category->id)->all()
        );
    }

    public function getExpenseSummary(Carbon $from, Carbon $to): array
    {
        $dateRange = [$from->format('Y-m-d'), $to->format('Y-m-d')];

        $expenses = $this->expenses()->with('category')->whereBetween('effective_date', $dateRange)->get();

        $total = $expenses->reduce(function (Money $carry, Expense $item) {
            return $carry->add(Money::USD($item->amount), Money::USD($item->foreign_currency_conversion_fee));
        }, Money::USD(0));

        $categories = [];

        /** @var \App\Models\Expense $e */
        foreach ($expenses as $e) {
            if (! array_key_exists($e->category_id, $categories)) {
                $categories[$e->category_id] = $e->category->toArray();
                $categories[$e->category_id]['expenses'] = [];
            }

            $categories[$e->category_id]['expenses'][] = $e;
        }

        // order each category's expenses by date
        foreach ($categories as &$category) {
            usort($category['expenses'], function (Expense $a, Expense $b) {
                if ($a->effective_date === $b->effective_date) {
                    return 0;
                }

                return $a->effective_date->lessThan($b->effective_date) ? -1 : 1;
            });
        }

        // get total for each category
        foreach ($categories as &$category) {
            $categoryTotal = array_reduce($category['expenses'], function (Money $carry, Expense $item) {
                return $carry->add(Money::USD($item->amount), Money::USD($item->foreign_currency_conversion_fee));
            }, Money::USD(0));

            $category['total'] = app(IntlMoneyFormatter::class)->format($categoryTotal);
            $category['total_raw'] = app(DecimalMoneyFormatter::class)->format($categoryTotal);
        }

        return [
            'total'      => app(IntlMoneyFormatter::class)->format($total),
            'count'      => $expenses->count(),
            'categories' => array_values($categories),
        ];
    }
}
