<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BaseService;
use App\Services\WalletService;
use Illuminate\Console\Command;

class ProcessGlobalProfitSharing extends Command
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
    protected $description = 'Process user profit pool calculation';

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
            $count = $users->count();
            $users->each(function($user) use ($count){
                (new WalletService)->computeUserGlobalProfitShare($user,$count);
            });
            return 0;
        } catch (\Exception $e) {
            (new BaseService)->logger($e,"Error processing users global profit");
            return 1;
        }
    }
}
