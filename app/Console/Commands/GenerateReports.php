<?php

namespace App\Console\Commands;

use App\Services\GenerateReportsService;
use Illuminate\Console\Command;

class GenerateReports extends Command
{
    protected $signature = 'generate:reports';
    protected $description = 'Generates CSV reports of logs grouped by consumer and service';

    public function handle()
    {
        $start = microtime(true);
        $this->info('Gerando relatórios...');

        $service = new GenerateReportsService();
        $relativeDir = $service->generateReports();

        $duration = microtime(true) - $start;
        $this->info("Relatórios gerados com sucesso em {$relativeDir}!");
        $this->info('Tempo total da operação: ' . number_format($duration, 2) . ' segundos');
    }
}
