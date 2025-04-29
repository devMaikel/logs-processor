<?php

namespace App\Console\Commands;

use App\Services\ProcessLogsService;
use Illuminate\Console\Command;

class ProcessLogs extends Command
{
    protected $signature = 'process:Logs';
    protected $description = 'Processes the logs.txt file and saves information from it to the database';

    public function handle()
    {
        $start = microtime(true);
        
        $this->info('Processando dados...');
        $processor = new ProcessLogsService();
        $processor->processLogFile('logs100.txt', $this->output);
        
        $end = microtime(true);
        $duration = $end - $start;
        
        $this->info(PHP_EOL . 'Logs processados com sucesso!');
        $this->info('Tempo total: ' . number_format($duration, 2) . ' segundos');
    }
}
