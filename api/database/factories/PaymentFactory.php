<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition() : array
    {
        return [
            'order_id' => $this->faker->randomNumber(),
            'payment_reference' => $this->faker->bothify('###???'),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'status' => $this->faker->randomElement(['pending', 'done']),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
