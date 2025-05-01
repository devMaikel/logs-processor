<?php
namespace Tests\Unit\Console\Commands;

use App\Models\Consumer;
use App\Models\Log;
use App\Models\Route;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class GenerateReportsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_reports_command()
    {
        Consumer::factory()->create(['id' => 'consumer-1']);
        Service::factory()->create(['id' => 'service-1']);
        Route::factory()->create(['id' => 'route-1']);
        
        Log::factory()->create([
            'consumer_id' => 'consumer-1',
            'service_id' => 'service-1',
            'route_id' => 'route-1',
            'request_latency' => 100,
            'proxy_latency' => 20,
            'gateway_latency' => 30
        ]);

        $exitCode = Artisan::call('generate:reports');
        
        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Gerando relatórios...', $output);
        $this->assertStringContainsString('Relatórios gerados com sucesso', $output);
    }
}