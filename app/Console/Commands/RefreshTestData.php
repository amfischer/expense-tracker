<?php

namespace App\Console\Commands;

use App\Enums\Currency;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class RefreshTestData extends Command
{
    protected $signature = 'app:refresh-test-data';

    protected $description = 'Refresh the data on the test account(s)';

    public function handle(): void
    {
        $jeremy = User::where('email', 'benjis@momoney.com')->first();

        if (is_null($jeremy)) {
            $jeremy = User::create([
                'name'              => 'Jay Money',
                'email'             => 'benjis@momoney.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
            ]);
        }

        $jeremy->expenses()->delete();
        $jeremy->categories()->delete();

        foreach ($this->getCategoriesAndExpenses() as $data) {
            $category = Category::create([
                'user_id' => $jeremy->id,
                'name'    => $data['category']['name'],
                'color'   => $data['category']['color'],
            ]);

            foreach ([[1, 30], [31, 60], [61, 90]] as $range) {
                foreach ($data['expenses'] as $e) {
                    $date = Carbon::now()->subDays(random_int($range[0], $range[1]));

                    Expense::create([
                        'user_id'          => $jeremy->id,
                        'category_id'      => $category->id,
                        'currency'         => Currency::USD,
                        'payee'            => $e['payee'],
                        'amount'           => $e['amount'],
                        'transaction_date' => $date,
                        'effective_date'   => $date,
                    ]);
                }
            }
        }

    }

    public function getCategoriesAndExpenses()
    {

        return [
            [
                'category' => ['name' => Category::DEFAULT_NAME, 'color' => Category::DEFAULT_COLOR],
                'expenses' => [],
            ],
            [
                'category' => ['name' => 'Investments', 'color' => '#FF5733'],
                'expenses' => [
                    ['payee' => 'Nasdaq Index', 'amount' => 500],
                    ['payee' => '401K', 'amount' => 250],
                ],
            ],
            [
                'category' => ['name' => 'Food & Drink', 'color' => '#FFBD33'],
                'expenses' => [
                    ['payee' => 'McDonalds', 'amount' => 18.35],
                    ['payee' => 'Starbucks', 'amount' => 8.77],
                ],
            ],
            [
                'category' => ['name' => 'Entertainment', 'color' => '#33FFBD'],
                'expenses' => [
                    ['payee' => 'Wimbledon Tickets', 'amount' => 185],
                    ['payee' => 'PS5 Game', 'amount' => 56.99],
                ],
            ],
            [
                'category' => ['name' => 'Medical', 'color' => '#33DBFF'],
                'expenses' => [
                    ['payee' => 'Doctors appt co-pay', 'amount' => 25],
                    ['payee' => 'Ibuprofen', 'amount' => 11.35],
                ],
            ],
            [
                'category' => ['name' => 'Fitness', 'color' => '#BD33FF'],
                'expenses' => [
                    ['payee' => 'Planet Fitness Membership', 'amount' => 19.89],
                    ['payee' => 'Tennis balls', 'amount' => 2.99],
                ],
            ],
        ];
    }
}
