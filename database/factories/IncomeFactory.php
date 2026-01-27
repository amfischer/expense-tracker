<?php

namespace Database\Factories;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class IncomeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'source'           => $this->faker->word(),
            'amount'           => $this->faker->randomFloat(2, 1, 1000), // between $1 USD - $1,000 USD
            'payment_date'     => Carbon::now(),
            'effective_date'   => Carbon::now(),
            'is_earned_income' => $this->faker->boolean(),
        ];
    }
}
