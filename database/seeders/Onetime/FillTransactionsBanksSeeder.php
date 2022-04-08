<?php

namespace Database\Seeders\Onetime;

use App\Models\Bank;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class FillTransactionsBanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transactions = Transaction::all();

        foreach ($transactions as $transaction) {
            $transaction->bank_id = $this->getBankId($transaction->card_number);
            $transaction->save();
        }
    }

    /**
     * @param string $value
     * @return string|null
     */
    protected function getBankId(string $value): ?string
    {
        $val = substr($value, 0, 6);

        return Bank::where('bin', $val)->value('id') ?? null;
    }
}
