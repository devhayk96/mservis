<?php

namespace Database\Seeders\Onetime;

use Illuminate\Database\Seeder;
use DB;

class RemoveTransactionDuplicates extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::delete('DELETE t1 FROM transactions t1, transactions t2 WHERE t1.id > t2.id AND t1.external_id = t2.external_id AND t1.merchant_id = t2.merchant_id');
    }
}
