<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\BaseService;
use App\Services\WalletService;
use Illuminate\Console\Command;

class ProcessLoyaltyBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:loyalty_bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process users loyalty bonus';

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
            $users = (new UserRepository)->cronUsers();
            $users->each(function($user) {
                (new WalletService)->loyaltyBonus($user->uuid);
            });
            return 0;
        } catch (\Exception $e) {
            (new BaseService)->logger($e,"Error processing user loyalty bonus");
            return 1;
        }
    }
}
