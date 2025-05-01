<?php
namespace Tests\Unit\Services;

use App\Models\Consumer;
use App\Models\Log;
use App\Models\Route;
use App\Models\Service;
use App\Services\GenerateReportsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GenerateReportsServiceTest extends TestCase
{
    use RefreshDatabase;

    private GenerateReportsService $service;
    private string $reportsPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GenerateReportsService();
        $this->reportsPath = storage_path('app/reports');
        
        if (File::exists($this->reportsPath)) {
            File::deleteDirectory($this->reportsPath);
        }
    }

    public function test_generate_reports_creates_all_expected_files()
    {
        $consumer = Consumer::factory()->create(['id' => 'consumer-1']);
        $service = Service::factory()->create([
            'id' => 'service-1',
            'name' => 'test-service'
        ]);
        $route = Route::factory()->create(['id' => 'route-1']);
        
        Log::factory()->create([
            'consumer_id' => 'consumer-1',
            'service_id' => 'service-1',
            'route_id' => 'route-1',
            'request_latency' => 100,
            'proxy_latency' => 20,
            'gateway_latency' => 30
        ]);

        Log::factory()->create([
            'consumer_id' => 'consumer-1',
            'service_id' => 'service-1',
            'route_id' => 'route-1',
            'request_latency' => 200,
            'proxy_latency' => 40,
            'gateway_latency' => 60
        ]);

        $relativeDir = $this->service->generateReports();
        
        $this->assertDirectoryExists($this->reportsPath, 'O diretório de relatórios deve existir');

        $directories = array_filter(glob($this->reportsPath.'/*'), 'is_dir');
        
        $this->assertCount(1, $directories, 'Deve haver apenas um diretório de relatórios');
        
        $reportDir = $directories[0];
        $this->assertFileExists("$reportDir/requests_by_consumer.csv", 'O arquivo requests_by_consumer.csv deve existir');
        $this->assertFileExists("$reportDir/requests_by_service.csv", 'O arquivo requests_by_service.csv deve existir');
        $this->assertFileExists("$reportDir/average_latencies_by_service.csv", 'O arquivo average_latencies_by_service.csv deve existir');

        
        $requestsByConsumer = array_map('str_getcsv', file("$reportDir/requests_by_consumer.csv"));
        $this->assertEquals(['Consumer ID', 'Request Count'], $requestsByConsumer[0]);
        $this->assertEquals(['consumer-1', '2'], $requestsByConsumer[1], 'O arquivo requests_by_consumer.csv deve conter o consumer-1 com 2 requests');
        
        $requestsByService = array_map('str_getcsv', file("$reportDir/requests_by_service.csv"));
        $this->assertEquals(['Service Name', 'Request Count'], $requestsByService[0]);
        $this->assertEquals(['test-service', '2'], $requestsByService[1], 'O arquivo requests_by_service.csv deve conter o service-1 com 2 requests');

        $avgLatencies = array_map('str_getcsv', file("$reportDir/average_latencies_by_service.csv"));
        $this->assertEquals(['Service Name', 'Avg Request Latency', 'Avg Proxy Latency', 'Avg Gateway Latency'], $avgLatencies[0]);
        $this->assertEquals(['test-service', '150', '30', '45'], $avgLatencies[1], 'O arquivo average_latencies_by_service.csv deve conter o service-1 com latências de 150ms, 30ms e 45ms');
        // dump($requestsByConsumer, $requestsByService, $avgLatencies);
    }

    protected function tearDown(): void
    {
        if (File::exists($this->reportsPath)) {
            File::deleteDirectory($this->reportsPath);
        }
        parent::tearDown();
    }
}