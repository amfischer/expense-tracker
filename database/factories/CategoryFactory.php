<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public static array $categories = [
        'Utilities',
        'Rent',
        'Investments',
        'Food & Drink',
        'Entertainment',
        'Medical',
        'Fitness',
        'BCP Transfer',
        'Education',
        'Charity',
        'Software',
        'Hardware',
        'Transportation',
        'Travel',
    ];

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name'    => $this->faker->name(),
            'color'   => $this->faker->hexColor(),
        ];
    }
}
