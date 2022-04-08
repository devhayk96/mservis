<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperatorAndProcessingToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table
                ->boolean('is_processing')
                ->default(false)
                ->after('execution_date')
                ->comment('Whether transaction is in processing or not.');

            $table
                ->tinyInteger('processing_operator')
                ->nullable()
                ->after('is_processing')
                ->comment('ID of a processing operator.');
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
            $table->dropColumn('is_processing');
            $table->dropColumn('processing_operator');
        });
    }
}
