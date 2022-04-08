<?php

namespace App\Console\Commands;

use App\Models\MerchantWebhookUrl;
use Illuminate\Console\Command;

class AddMerchantWebhookUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhooks:set
                            {merchantId : ID of a merchant}
                            {url? : URL for callback}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set merchant\'s URL for getting a webhook callback.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        MerchantWebhookUrl::updateOrCreate(
            ['merchant_id' => $this->argument('merchantId')],
            ['url' => $this->argument('url') ?? ''],
        );
    }
}
