<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_commissions', function (Blueprint $table) {
            $table->id();
            $table->float('amount', 10, 2)->comment('Amount of commission.');
            $table->tinyInteger('type')->default(0)->comment('Type of commission.');
            $table->timestamp('date')->nullable()->comment('Day when commission is applied.');
        });

        DB::statement("ALTER TABLE `daily_commissions` comment 'Daily commissions.'");

        Schema::table('transactions', function (Blueprint $table) {
            $table
                ->unsignedBigInteger('daily_commission_id')
                ->nullable()
                ->after('execution_date')
                ->comment('Commission id.');
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
            $table->dropColumn('daily_commission_id');
        });

        Schema::dropIfExists('daily_commissions');
    }
}
