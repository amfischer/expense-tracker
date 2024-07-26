<?php

namespace App\Console\Commands;

use App\Enums\PaymentMethod;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateExpenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expenses:fcc-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create expenses in bulk';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::createFromFormat('Y-m-d', '2023-12-26');

        $counter = 23;

        while ($counter > 0) {
            $date->subMonth();

            Expense::create([
                'user_id'          => 1,
                'category_id'      => 12,
                'payee'            => 'FreeCodeCamp',
                'amount'           => 5,
                'currency'         => 'USD',
                'payment_method'   => PaymentMethod::DISCOVER_CARD->value,
                'transaction_date' => $date,
                'effective_date'   => $date,
            ]);

            $counter--;
        }
    }
}
