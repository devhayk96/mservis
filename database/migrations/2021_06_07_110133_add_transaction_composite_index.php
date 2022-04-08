<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\Onetime\RemoveTransactionDuplicates;

class AddTransactionCompositeIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', ['--class' => RemoveTransactionDuplicates::class, '--force' => true]);

        Schema::table('transactions', function (Blueprint $table) {
            $table->unique(['merchant_id', 'external_id'], 'merchant_transactions');
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
            $table->dropUnique('merchant_transactions');
        });
    }
}
