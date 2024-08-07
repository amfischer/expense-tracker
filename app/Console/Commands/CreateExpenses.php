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
    protected $signature = 'expenses:create';

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
        $this->googleStorage();
    }

    protected function googleStorage()
    {
        $this->createGoogleStorageExpense('2.17', '2023-12-21', '2023-12-21');
        $this->createGoogleStorageExpense('2.17', '2023-11-21', '2023-11-21');
        $this->createGoogleStorageExpense('2.17', '2023-10-21', '2023-10-21');
        $this->createGoogleStorageExpense('2.17', '2023-09-22', '2023-09-21');
        $this->createGoogleStorageExpense('2.17', '2023-08-22', '2023-08-21');
        $this->createGoogleStorageExpense('2.17', '2023-07-22', '2023-07-21');
        $this->createGoogleStorageExpense('2.17', '2023-06-22', '2023-06-21');
        $this->createGoogleStorageExpense('2.17', '2023-05-21', '2023-05-21');
        $this->createGoogleStorageExpense('2.17', '2023-04-22', '2023-04-21');
        $this->createGoogleStorageExpense('2.17', '2023-03-21', '2023-03-21');
        $this->createGoogleStorageExpense('2.17', '2023-02-21', '2023-02-21');
        $this->createGoogleStorageExpense('2.17', '2023-01-21', '2023-01-21');

        $this->createGoogleStorageExpense('2.17', '2022-12-22', '2022-12-21');
        $this->createGoogleStorageExpense('2.17', '2022-11-22', '2022-11-21');
        $this->createGoogleStorageExpense('2.17', '2022-10-21', '2022-10-21');
        $this->createGoogleStorageExpense('2.17', '2022-09-22', '2022-09-21');
        $this->createGoogleStorageExpense('2.17', '2022-08-22', '2022-08-21');
        $this->createGoogleStorageExpense('2.17', '2022-07-22', '2022-07-21');
        $this->createGoogleStorageExpense('2.17', '2022-06-22', '2022-06-21');
        $this->createGoogleStorageExpense('2.17', '2022-05-22', '2022-05-21');
        $this->createGoogleStorageExpense('2.17', '2022-04-22', '2022-04-21');
        $this->createGoogleStorageExpense('2.17', '2022-03-22', '2022-03-21');
        $this->createGoogleStorageExpense('2.17', '2022-02-21', '2022-02-21');
        $this->createGoogleStorageExpense('2.17', '2022-01-21', '2022-01-21');

        $this->createGoogleStorageExpense('2.17', '2021-12-21', '2021-12-21');
        $this->createGoogleStorageExpense('2.17', '2021-11-22', '2021-11-21');
    }

    protected function createGoogleStorageExpense(string $amount, string $td, string $ed): void
    {
        $ctd = Carbon::createFromFormat('Y-m-d', $td);
        $ced = Carbon::createFromFormat('Y-m-d', $ed);

        Expense::create([
            'user_id'             => 1,
            'category_id'         => 14,
            'payee'               => 'Google Storage',
            'amount'              => $amount,
            'currency'            => 'USD',
            'payment_method'      => PaymentMethod::DISCOVER_CARD->value,
            'is_business_expense' => true,
            'transaction_date'    => $ctd,
            'effective_date'      => $ced,
        ]);

    }

    protected function digitalOcean()
    {
        $this->createDigitalOceanExpense('24.55', '2023-11-30', '2023-11-30');
        $this->createDigitalOceanExpense('24.53', '2023-11-01', '2023-10-31');
        $this->createDigitalOceanExpense('17.35', '2023-10-01', '2023-09-30');
        $this->createDigitalOceanExpense('17.35', '2023-08-31', '2023-08-31');
        $this->createDigitalOceanExpense('17.35', '2023-07-31', '2023-07-31');
        $this->createDigitalOceanExpense('17.35', '2023-06-30', '2023-06-30');
        $this->createDigitalOceanExpense('17.35', '2023-05-31', '2023-05-31');
        $this->createDigitalOceanExpense('17.35', '2023-05-01', '2023-04-30');
        $this->createDigitalOceanExpense('17.35', '2023-04-01', '2023-03-31');
        $this->createDigitalOceanExpense('13.55', '2023-02-28', '2023-02-28');
        $this->createDigitalOceanExpense('13.35', '2023-01-31', '2023-01-31');

        $this->createDigitalOceanExpense('13.35', '2022-12-31', '2022-12-31');
        $this->createDigitalOceanExpense('15.01', '2022-12-01', '2022-11-30');
        $this->createDigitalOceanExpense('19.35', '2022-10-31', '2022-10-31');
        $this->createDigitalOceanExpense('19.35', '2022-09-30', '2022-09-30');
        $this->createDigitalOceanExpense('19.35', '2022-08-31', '2022-08-31');
        $this->createDigitalOceanExpense('19.35', '2022-08-01', '2022-07-31');
        $this->createDigitalOceanExpense('16.29', '2022-07-01', '2022-06-30');
        $this->createDigitalOceanExpense('16.29', '2022-06-01', '2022-05-31');
        $this->createDigitalOceanExpense('16.29', '2022-04-30', '2022-04-30');
        $this->createDigitalOceanExpense('16.29', '2022-04-01', '2022-03-31');
        $this->createDigitalOceanExpense('16.29', '2022-03-01', '2022-02-28');
        $this->createDigitalOceanExpense('16.29', '2022-01-31', '2022-01-31');
    }

    protected function createDigitalOceanExpense(string $amount, string $td, string $ed): void
    {
        $ctd = Carbon::createFromFormat('Y-m-d', $td);
        $ced = Carbon::createFromFormat('Y-m-d', $ed);

        Expense::create([
            'user_id'             => 1,
            'category_id'         => 14,
            'payee'               => 'DigitalOcean',
            'amount'              => $amount,
            'currency'            => 'USD',
            'payment_method'      => PaymentMethod::DISCOVER_CARD->value,
            'is_business_expense' => true,
            'transaction_date'    => $ctd,
            'effective_date'      => $ced,
        ]);

    }

    protected function createFreeCodeCampExpenses(): void
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
