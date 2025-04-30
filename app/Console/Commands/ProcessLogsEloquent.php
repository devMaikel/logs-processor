<?php

namespace App\Console\Commands;

use App\Services\ProcessLogsEloquentService;
use Illuminate\Console\Command;

class ProcessLogsEloquent extends Command
{
    protected $signature = 'process:LogsEloquent';
    protected $description = 'Processes the logs.txt file and saves information from it to the database';

    public function handle()
    {
        $start = microtime(true);
        
        $this->info('Processando dados...');
        $processor = new ProcessLogsEloquentService();
        $processor->processLogFile('logs100.txt', $this->output);
        
        $end = microtime(true);
        $duration = $end - $start;
        
        $this->info(PHP_EOL . 'Logs processados com sucesso!');
        $this->info('Tempo total da operação: ' . number_format($duration, 2) . ' segundos');
    }
}
