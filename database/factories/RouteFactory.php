<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RouteFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => fake()->uuid(),
            'hosts' => [fake()->domainName()],
            'methods' => [fake()->randomElement(['GET', 'POST', 'PUT', 'DELETE'])],
            'paths' => ['/' . fake()->word()],
            'preserve_host' => fake()->boolean(),
            'protocols' => [fake()->randomElement(['http', 'https'])],
            'regex_priority' => fake()->numberBetween(0, 100),
            'strip_path' => fake()->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}