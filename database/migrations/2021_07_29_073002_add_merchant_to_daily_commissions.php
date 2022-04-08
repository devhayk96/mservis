<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMerchantToDailyCommissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_commissions', function (Blueprint $table) {
            $table
                ->foreignId('merchant_id')
                ->after('id')
                ->comment('Merchant ID.')
                ->constrained();

            $table->index('merchant_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_commissions', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropForeign(['merchant_id']);
            $table->dropIndex(['merchant_id']);
            $table->dropColumn('merchant_id');
        });
    }
}
