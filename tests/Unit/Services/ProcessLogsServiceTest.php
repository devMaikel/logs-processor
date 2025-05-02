<?php
namespace Tests\Unit\Services;

use App\Services\ProcessLogsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProcessLogsServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProcessLogsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProcessLogsService();
    }

    public function test_process_log_file_inserts_data_correctly()
    {
        $logFile = 'storage/app/test_logs.txt';
        file_put_contents($logFile, $this->getSampleLogLines());

        $this->service->processLogFile($logFile, $this->getMockOutput());

        $this->assertDatabaseCount('consumers', 2);
        $this->assertDatabaseCount('services', 2);
        $this->assertDatabaseCount('routes', 2);
        $this->assertDatabaseCount('logs', 2);

        $log = DB::table('logs')->first();
        $this->assertNotNull($log->consumer_id, 'O consumer_id deve ser preenchido');
        $this->assertNotNull($log->service_id, 'O service_id deve ser preenchido');
        $this->assertNotNull($log->route_id, 'O route_id deve ser preenchido');

        unlink($logFile);
    }


    private function getSampleLogLines(): string
    {
        return implode("\n", [
            json_encode($this->getSampleLogData('consumer-1', 'service-1', 'route-1')),
            json_encode($this->getSampleLogData('consumer-2', 'service-2', 'route-2')),
        ]);
    }

    private function getSampleLogData(
        string $consumerId = 'consumer-1',
        string $serviceId = 'service-1',
        string $routeId = 'route-1'
    ): array {
        return [
            'authenticated_entity' => [
                'consumer_id' => ['uuid' => $consumerId]
            ],
            'service' => [
                'id' => $serviceId,
                'name' => 'Test Service',
                'host' => 'example.com',
                'path' => '/test',
                'port' => 80,
                'protocol' => 'http',
                'connect_timeout' => 60000,
                'read_timeout' => 60000,
                'write_timeout' => 60000,
                'retries' => 5,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'route' => [
                'id' => $routeId,
                'hosts' => ['example.com'],
                'methods' => ['GET'],
                'paths' => ['/test'],
                'preserve_host' => false,
                'protocols' => ['http'],
                'regex_priority' => 0,
                'strip_path' => true,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'request' => [
                'method' => 'GET',
                'uri' => '/test',
                'url' => 'http://example.com/test',
                'size' => 123,
                'querystring' => ['param' => 'value'],
                'headers' => ['Content-Type' => 'application/json'],
            ],
            'response' => [
                'status' => 200,
                'size' => 456,
                'headers' => ['Content-Type' => 'application/json'],
            ],
            'latencies' => [
                'proxy' => 10,
                'gateway' => 20,
                'request' => 30,
            ],
            'client_ip' => '127.0.0.1',
            'started_at' => time(),
        ];
    }

    private function getMockOutput()
    {
        return new class {
            public function createProgressBar($total)
            {
                return new class {
                    public function advance() {}
                    public function finish() {}
                };
            }
        };
    }
}