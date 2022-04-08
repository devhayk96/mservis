<?php

namespace Database\Factories;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => $this->faker->randomElement([2, 3]),
            'date' => Carbon::now()->subDays($this->faker->numberBetween(0, 3)),
            'amount' => $this->faker->randomFloat(2, 0, 200000),
            'card_number' => $this->faker->creditCardNumber(),
            'external_id' => $this->faker->uuid(),
            'merchant_id' => $this->faker->randomElement([1, 2, 3]),
            'execution_date' => Carbon::now(),
        ];
    }
}
