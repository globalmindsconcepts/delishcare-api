<?php

namespace App\Console;

use App\Console\Commands\DeactivateUserCommand;
use App\Console\Commands\ProcessEquilibriumBonus;
use App\Console\Commands\ProcessGlobalProfitSharing;
use App\Console\Commands\ProcessLoyaltyBonus;
use App\Console\Commands\ProcessProfitPool;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel; 

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command(DeactivateUserCommand::class)->daily();
        $schedule->command(ProcessProfitPool::class)->daily();
        $schedule->command(ProcessGlobalProfitSharing::class)->yearlyOn(10,30);
        $schedule->command(ProcessLoyaltyBonus::class)->hourly();
        $schedule->command(ProcessEquilibriumBonus::class)->hourly();
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
