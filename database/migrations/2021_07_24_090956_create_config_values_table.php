<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_values', function (Blueprint $table) {
            $table->id();
            $table
                ->string('key')
                ->comment('Name of a value.');
            $table
                ->text('value')
                ->default('')
                ->comment('Configuration value.');
        });

        DB::statement("ALTER TABLE `transactions_processing` comment 'Configuration values that should be kept in a database.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_values');
    }
}
