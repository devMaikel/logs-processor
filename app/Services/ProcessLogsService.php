<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class ProcessLogsService
{
    private $existing_consumers = [];
    private $existing_services = [];
    private $existing_routes = [];
    private $logs_batch = [];
    private $batch_size = 1000;

    public function processLogFile(string $filePath, $output)
    {
        $this->prepareDatabase();

        $fullPath = base_path($filePath);

        if (!file_exists($fullPath)) {
            throw new \Exception("O arquivo '{$filePath}' não foi encontrado na raiz do projeto.");
        }
        
        $file = fopen($filePath, 'r');
        $totalLines = $this->countFileLines($file);
        $progressBar = $output->createProgressBar($totalLines);
        
        while ($line = fgets($file)) {
            $this->processLine($line);
            $progressBar->advance();
        }
        
        $this->insertRemainingLogs();
        fclose($file);
        
        $progressBar->finish();
    }

    private function prepareDatabase()
    {
        DB::transaction(function () {
            Schema::disableForeignKeyConstraints();
            DB::table('logs')->truncate();
            DB::table('services')->truncate();
            DB::table('consumers')->truncate();
            DB::table('routes')->truncate();
            Schema::enableForeignKeyConstraints();
        });
    }

    private function countFileLines($file): int
    {
        $totalLines = 0;
        while (fgets($file)) {
            $totalLines++;
        }
        rewind($file);
        return $totalLines;
    }

    private function processLine(string $line)
    {
        $data = json_decode($line, true);
        
        $this->processConsumer($data);
        $this->processService($data);
        $this->processRoute($data);
        $this->addLogToBatch($data);
    }

    private function processConsumer(array $data)
    {
        $consumer_id = $data['authenticated_entity']['consumer_id']['uuid'];
        
        if (!isset($this->existing_consumers[$consumer_id])) {
            DB::table('consumers')->insert([
                'id' => $consumer_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->existing_consumers[$consumer_id] = true;
        }
    }

    private function processService(array $data)
    {
        $service_id = $data['service']['id'];
        
        if (!isset($this->existing_services[$service_id])) {
            DB::table('services')->insert([
                'id' => $service_id,
                'name' => $data['service']['name'],
                'host' => $data['service']['host'],
                'path' => $data['service']['path'],
                'port' => $data['service']['port'],
                'protocol' => $data['service']['protocol'],
                'connect_timeout' => $data['service']['connect_timeout'],
                'read_timeout' => $data['service']['read_timeout'],
                'write_timeout' => $data['service']['write_timeout'],
                'retries' => $data['service']['retries'],
                'created_at' => Carbon::createFromTimestamp($data['service']['created_at']),
                'updated_at' => Carbon::createFromTimestamp($data['service']['updated_at']),
            ]);
            $this->existing_services[$service_id] = true;
        }
    }

    private function processRoute(array $data)
    {
        $route_id = $data['route']['id'];
        
        if (!isset($this->existing_routes[$route_id])) {
            DB::table('routes')->insert([
                'id' => $route_id,
                'hosts' => json_encode($data['route']['hosts']),
                'methods' => json_encode($data['route']['methods']),
                'paths' => json_encode($data['route']['paths']),
                'preserve_host' => $data['route']['preserve_host'],
                'protocols' => json_encode($data['route']['protocols']),
                'regex_priority' => $data['route']['regex_priority'],
                'strip_path' => $data['route']['strip_path'],
                'created_at' => Carbon::createFromTimestamp($data['route']['created_at']),
                'updated_at' => Carbon::createFromTimestamp($data['route']['updated_at']),
            ]);
            $this->existing_routes[$route_id] = true;
        }
    }

    private function addLogToBatch(array $data)
    {
        $this->logs_batch[] = [
            'id' => Str::uuid(),
            'consumer_id' => $data['authenticated_entity']['consumer_id']['uuid'],
            'service_id' => $data['service']['id'],
            'route_id' => $data['route']['id'],
            'request_method' => $data['request']['method'],
            'request_uri' => $data['request']['uri'],
            'request_url' => $data['request']['url'],
            'request_size' => $data['request']['size'],
            'proxy_latency' => $data['latencies']['proxy'],
            'gateway_latency' => $data['latencies']['gateway'],
            'request_latency' => $data['latencies']['request'],
            'request_querystring' => json_encode($data['request']['querystring']),
            'request_headers' => json_encode($data['request']['headers']),
            'response_status' => $data['response']['status'],
            'response_size' => $data['response']['size'],
            'response_headers' => json_encode($data['response']['headers']),
            'client_ip' => $data['client_ip'],
            'started_at' => Carbon::createFromTimestamp($data['started_at']),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (count($this->logs_batch) >= $this->batch_size) {
            $this->insertBatch();
        }
    }

    private function insertBatch()
    {
        DB::table('logs')->insert($this->logs_batch);
        $this->logs_batch = [];
    }

    private function insertRemainingLogs()
    {
        if (!empty($this->logs_batch)) {
            $this->insertBatch();
        }
    }
}