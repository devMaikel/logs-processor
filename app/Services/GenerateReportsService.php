<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class GenerateReportsService
{
    public function generateReports(): string
    {
        $timestamp = Carbon::now()->format('Ymd_His');
        $directory = storage_path("app/reports/{$timestamp}");

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $this->generateRequestsByConsumer("{$directory}/requests_by_consumer.csv");
        $this->generateRequestsByService("{$directory}/requests_by_service.csv");
        $this->generateAverageLatenciesByService("{$directory}/average_latencies_by_service.csv");

        return str_replace(base_path() . '/', '', $directory);
    }

    private function generateRequestsByConsumer(string $filepath): void
    {
        $handle = fopen($filepath, 'w');
        fputcsv($handle, ['Consumer ID', 'Request Count']);

        $data = Log::select('consumer_id', DB::raw('count(*) as request_count'))
            ->groupBy('consumer_id')
            ->orderByDesc('request_count')
            ->get();

        foreach ($data as $row) {
            fputcsv($handle, [$row->consumer_id, $row->request_count]);
        }

        fclose($handle);
    }

    private function generateRequestsByService(string $filepath): void
    {
        $handle = fopen($filepath, 'w');
        fputcsv($handle, ['Service Name', 'Request Count']);

        $data = Log::join('services', 'logs.service_id', '=', 'services.id')
            ->select('services.name as service_name', DB::raw('count(*) as request_count'))
            ->groupBy('services.name')
            ->orderByDesc('request_count')
            ->get();

        foreach ($data as $row) {
            fputcsv($handle, [$row->service_name, $row->request_count]);
        }

        fclose($handle);
    }

    private function generateAverageLatenciesByService(string $filepath): void
    {
        $handle = fopen($filepath, 'w');
        fputcsv($handle, ['Service Name', 'Avg Request Latency', 'Avg Proxy Latency', 'Avg Gateway Latency']);

        $data = Log::join('services', 'logs.service_id', '=', 'services.id')
            ->select(
                'services.name as service_name',
                DB::raw('AVG(request_latency) as avg_request_latency'),
                DB::raw('AVG(proxy_latency) as avg_proxy_latency'),
                DB::raw('AVG(gateway_latency) as avg_gateway_latency')
            )
            ->groupBy('services.name')
            ->orderBy('services.name')
            ->get();

        foreach ($data as $row) {
            fputcsv($handle, [
                $row->service_name,
                round($row->avg_request_latency, 2),
                round($row->avg_proxy_latency, 2),
                round($row->avg_gateway_latency, 2),
            ]);
        }

        fclose($handle);
    }
}
