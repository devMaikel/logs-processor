<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConsumerFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => fake()->uuid(),
        ];
    }
}
