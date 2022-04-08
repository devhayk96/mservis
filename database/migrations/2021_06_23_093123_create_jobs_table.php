<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index()->comment('Queue name.');
            $table->longText('payload')->comment('Job payload.');
            $table->unsignedTinyInteger('attempts')->comment('Number of attempts.');
            $table->unsignedInteger('reserved_at')->nullable()->comment('Time (timestamp) of reservation by a queue worker.');
            $table->unsignedInteger('available_at')->comment('Time (timestamp) when a job can be processed by a queue worker.');
            $table->unsignedInteger('created_at');
        });

        DB::statement("ALTER TABLE `jobs` comment 'Laravel queue jobs.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
