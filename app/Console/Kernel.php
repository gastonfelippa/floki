<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\TestTask',
        'App\Console\Commands\ResumenDiario',
        'App\Console\Commands\CreateTenantDatabase',
        'App\Console\Commands\CreateTenantMigrations',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //todos los 24 a las 18:30 hs, sin que se superpongan
        $schedule->command('resumen:diario'); 
        // $schedule->command('resumen:diario')->monthlyOn(6, '17:53')->withoutOverlapping(10); 
        // $schedule->command('model:prune')->daily();
        //ejecuta tareas en segundo plano
        // $schedule->command('resumen:diario')->monthlyOn(5, '17:22')->runInBackground();; 
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
