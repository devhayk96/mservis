<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantWebhookUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_webhook_urls', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id')->comment('Merchant ID.');
            $table->string('url')->default('')->comment('Notification URL.');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `merchant_webhook_urls` comment 'Merchant webhooks URLs.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_webhook_urls');
    }
}
