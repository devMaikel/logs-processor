<?php
namespace Database\Factories;

use App\Models\Consumer;
use App\Models\Route;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => fake()->uuid(),
            'consumer_id' => Consumer::factory()->create()->id,
            'service_id' => Service::factory()->create()->id,
            'route_id' => Route::factory()->create()->id,
            'request_method' => fake()->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'request_uri' => '/' . fake()->word(),
            'request_url' => fake()->url(),
            'request_size' => fake()->numberBetween(100, 10000),
            'request_querystring' => ['param' => fake()->word()],
            'request_headers' => ['Content-Type' => 'application/json'],
            'response_status' => fake()->randomElement([200, 201, 400, 401, 404, 500]),
            'response_size' => fake()->numberBetween(100, 10000),
            'response_headers' => ['Content-Type' => 'application/json'],
            'proxy_latency' => fake()->numberBetween(1, 1000),
            'gateway_latency' => fake()->numberBetween(1, 1000),
            'request_latency' => fake()->numberBetween(1, 1000),
            'client_ip' => fake()->ipv4(),
            'started_at' => now(),
        ];
    }
}