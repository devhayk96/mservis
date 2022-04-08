<?php

use App\Models\Merchant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('token')->default('');
            $table->timestamps();
        });

        $tokens = [
            "3b11ea0c639e69b65b0fb5e8",
            "0269fbd1286e5d4a84ceb369",
            "c75df00671da8444f4af5256",
            "30f4d9ed4969783e4fc968fa",
            "e752ba662b77af0841e3724d",
        ];

        for ($i = 1; $i <= 5; $i++) {
            Merchant::create([
                'name' => 'Merchant ' . $i,
                'token' => $tokens[$i - 1],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants');
    }
}
