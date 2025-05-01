<?php
namespace Tests\Unit\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessLogsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_logs_command()
    {
        Storage::fake();
        Storage::put('logs.txt', $this->getSampleLogLine());

        $exitCode = Artisan::call('process:logs');

        $output = Artisan::output();
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('Processando dados...', $output);
        $this->assertStringContainsString('Logs processados com sucesso', $output);
    }

    private function getSampleLogLine(): string
    {
        return json_encode([
            'authenticated_entity' => [
                'consumer_id' => ['uuid' => 'consumer-1']
            ],
            'service' => [
                'id' => 'service-1',
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
                'id' => 'route-1',
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
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ],
            'response' => [
                'status' => 200,
                'size' => 456,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Custom-Header' => 'value'
                ],
            ],
            'latencies' => [
                'proxy' => 10,
                'gateway' => 20,
                'request' => 30,
            ],
            'client_ip' => '127.0.0.1',
            'started_at' => time(),
        ]);
    }
}