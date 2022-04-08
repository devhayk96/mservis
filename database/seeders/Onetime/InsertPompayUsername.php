<?php

namespace Database\Seeders\Onetime;

use App\Models\ConfigValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class InsertPompayUsername extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pompayUsername = ConfigValue::where('key', 'pompay.username')->first();

        if (!$pompayUsername) {
            ConfigValue::create([
              'key' => 'pompay.username',
              'value' => 'pompay',
          ]);
        }
    }
}
