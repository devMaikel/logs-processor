<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => fake()->uuid(),
            'name' => fake()->word(),
            'host' => fake()->domainName(),
            'path' => '/' . fake()->word(),
            'port' => fake()->numberBetween(80, 9000),
            'protocol' => fake()->randomElement(['http', 'https']),
            'connect_timeout' => fake()->numberBetween(100, 60000),
            'read_timeout' => fake()->numberBetween(100, 60000),
            'write_timeout' => fake()->numberBetween(100, 60000),
            'retries' => fake()->numberBetween(0, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}