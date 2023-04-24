<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BaseService;
use App\Services\WalletService;
use Illuminate\Console\Command;

class ProcessEquilibriumBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:equilibrium_bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process user equilibrium bonus';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $users = User::all()->where('is_deactivated',0);
            $users->each(function($user) {
                (new WalletService)->equillibrumBonus($user);
            });
            return 0;
        } catch (\Exception $e) {
            (new BaseService)->logger($e,"Error processing user equilibrium bonus");
            return 1;
        }
        
    }
}
