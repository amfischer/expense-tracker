<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call([
        //     UserSeeder::class
        // ]);

        $user = User::factory()->create([
            'name'     => 'Aaron',
            'email'    => 'aaron@example.com',
            'password' => Hash::make('password'),
        ]);

        $categoryIds = [];

        foreach (CategoryFactory::$categories as $category) {
            $data = [
                'user_id' => $user->id,
                'name'    => $category,
            ];

            $categoryIds[] = Category::factory()->create($data)->id;
        }

        for ($i = 0; $i < 10; $i++) {
            Expense::factory()->create([
                'user_id'     => $user->id,
                'category_id' => Arr::random($categoryIds),
            ]);
        }
    }
}
