<?php

namespace App\Services;

use App\Models\Consumer;
use App\Models\Log;
use App\Models\Route;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class ProcessLogsEloquentService
{

    public function processLogFile(string $filePath, $output)
    {
        $this->prepareDatabase();
        
        $file = fopen($filePath, 'r');
        $totalLines = $this->countFileLines($file);
        $progressBar = $output->createProgressBar($totalLines);
        
        while ($line = fgets($file)) {
            $this->processLine($line);
            $progressBar->advance();
        }
        
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
        $this->processLog($data);
    }

    private function processConsumer(array $data)
    {
      Consumer::firstOrCreate([
          'id' => $data['authenticated_entity']['consumer_id']['uuid']
      ], []);
    }

    private function processService(array $data)
    {
      Service::firstOrCreate([
        'id' => $data['service']['id']
      ], [
          'name' => $data['service']['name'],
          'host' => $data['service']['host'],
          'path' => $data['service']['path'],
          'port' => $data['service']['port'],
          'protocol' => $data['service']['protocol'],
          'connect_timeout' => $data['service']['connect_timeout'],
          'read_timeout' => $data['service']['read_timeout'],
          'write_timeout' => $data['service']['write_timeout'],
          'retries' => $data['service']['retries'],
          'created_at' => $data['service']['created_at'],
          'updated_at' => $data['service']['updated_at'],
      ]);
    }

    private function processRoute(array $data)
    {
        Route::firstOrCreate([
            'id' => $data['route']['id']
        ], [
            'hosts' => $data['route']['hosts'],
            'methods' => $data['route']['methods'],
            'paths' => $data['route']['paths'],
            'preserve_host' => $data['route']['preserve_host'],
            'protocols' => $data['route']['protocols'],
            'regex_priority' => $data['route']['regex_priority'],
            'strip_path' => $data['route']['strip_path'],
            'created_at' => Carbon::createFromTimestamp($data['route']['created_at']),
            'updated_at' => Carbon::createFromTimestamp($data['route']['updated_at']),
        ]);
    }

    private function processLog(array $data)
    {
      Log::create([
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
        'request_querystring' => $data['request']['querystring'],
        'request_headers' => $data['request']['headers'],
        'response_status' => $data['response']['status'],
        'response_size' => $data['response']['size'],
        'response_headers' => $data['response']['headers'],
        'client_ip' => $data['client_ip'],
        'started_at' => Carbon::createFromTimestamp($data['started_at'])
    ]);
    }
}