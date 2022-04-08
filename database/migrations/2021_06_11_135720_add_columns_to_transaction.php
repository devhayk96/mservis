<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->char('currency_code', 3)->after('amount')->default('RUB');
            $table->string('manager_name')->after('merchant_id')->default('');
            $table->timestamp('execution_date')->after('manager_name')->nullable();

            DB::statement("ALTER TABLE transactions CHANGE date date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
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
            DB::statement("ALTER TABLE transactions CHANGE date date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");

            $table->dropColumn('execution_date');
            $table->dropColumn('manager_name');
            $table->dropColumn('currency_code');
        });
    }
}
