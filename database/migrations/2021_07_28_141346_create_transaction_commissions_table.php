<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->comment('Transaction ID.')->constrained();
            $table->foreignId('daily_commission_id')->comment('Daily commission ID.')->constrained();
            $table->float('amount', 10, 2, true)->comment('Amount of commission.');
            $table->timestamps();

            $table->index('transaction_id');
        });

        DB::statement("ALTER TABLE `daily_commissions` comment 'Commissions of a successful transactions.'");

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('daily_commission_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table
                ->unsignedBigInteger('daily_commission_id')
                ->nullable()
                ->after('execution_date')
                ->comment('Commission id.');
        });

        Schema::dropIfExists('transaction_commissions');
    }
}
