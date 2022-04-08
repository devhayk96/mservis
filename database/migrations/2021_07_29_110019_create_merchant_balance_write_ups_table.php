<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantBalanceWriteUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_balance_write_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->comment('Merchant id.')->constrained();
            $table->timestamp('date')->nullable()->comment('Wire-up date.');
            $table->float('rate', 10, 2, true)->comment('Currency rate.');
            $table->float('commission', 10, 2, true)->comment('Amount of commission.');
            $table->float('sum', 10, 2, true)->comment('Amount of money.');
            $table->float('total_amount', 10, 2, true)->comment('Amount of write-up.');
            $table->text('comment')->default('')->comment('Comment.');
            $table->timestamps();

            $table->index('merchant_id');
        });

        DB::statement("ALTER TABLE `merchant_balance_write_ups` comment 'Write-ups of merchant balances.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_balance_write_ups');
    }
}
