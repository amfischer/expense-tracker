<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    private array $payees = [
        'PedidosYa',
        'Walmart',
        'McDonalds',
        'Digital Ocean',
        'Starbucks',
        'Tottus',
        'KFC',
    ];

    public function definition(): array
    {
        return [
            'user_id'             => User::factory(),
            'category_id'         => function (array $attr) {
                return Category::factory()->set('user_id', $attr['user_id']);
            },
            'payee'               => $this->faker->randomElement($this->payees),
            'amount'              => $this->faker->randomFloat(2, 1, 1000), // between $1 USD - $1,000 USD
            'currency'            => Currency::USD(),
            'is_business_expense' => $this->faker->boolean(30),
            'transaction_date'    => $this->faker->dateTimeBetween('-1 year')->format('Y-m-d'),
            'effective_date'      => function (array $attr) {
                return $attr['transaction_date'];
            },
        ];
    }
}
