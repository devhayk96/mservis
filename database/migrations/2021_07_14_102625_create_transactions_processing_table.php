<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsProcessingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_processing', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('transaction_id', false)
                ->comment('ID of a transaction.');

            $table->foreign('transaction_id')->references('id')->on('transactions');

            $table->boolean('is_processing')
                ->default(true)
                ->comment('Whether transaction is in processing or not.');

            $table->tinyInteger('processing_operator')
                ->nullable()
                ->comment('ID of a processing operator.');

            $table->text('end_comment')
                ->default('')
                ->comment('A comment after a processing finish.');

            $table->timestamps();
        });

        DB::statement("ALTER TABLE `transactions_processing` comment 'Transaction processing log.'");

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('fail_description');
            $table->dropColumn('processing_operator');
            $table->dropColumn('is_processing');
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
                ->boolean('is_processing')
                ->default(false)
                ->after('execution_date')
                ->comment('Whether transaction is in processing or not.');

            $table
                ->tinyInteger('processing_operator')
                ->nullable()
                ->after('is_processing')
                ->comment('ID of a processing operator.');

            $table->text('fail_description')
                ->default('')
                ->after('processing_operator')
                ->comment('Fail status reason.');
        });

        Schema::dropIfExists('transactions_processing');
    }
}
