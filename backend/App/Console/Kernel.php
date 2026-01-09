<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // Regjistro komandat tuaja këtu nëse dëshironi
        \App\Console\Commands\ResetWeeklyOrderItems::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Ekzekuto komandën çdo të hënë në mesnatë
        $schedule->command('orders:reset-weekly')->weeklyOn(1, '00:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
