<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BaseService;
use App\Services\WalletService;
use Illuminate\Console\Command;

class ProcessProfitPool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:profit_pool';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process user profit pool';

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
                (new WalletService)->computeProfitPool($user);
            });
            return 0;
        } catch (\Exception $e) {
            (new BaseService)->logger($e,"Error processing user profit pool");
        }
        return 1;
    }
}
