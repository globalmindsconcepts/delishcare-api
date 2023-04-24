<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Console\Command;

class DeactivateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:user_deactivation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process user deactivation';

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
            User::all()->each(function($user){
                $signUpTime = strtotime($user->created_at);
                $currTime = time();
                $timeDiff = $currTime - $signUpTime;
                $daysDiff = round($timeDiff/(60 * 60 * 24));
                if($daysDiff >= 7){
                    User::find($user->id)->update(['is_deactivated'=>1]);
                }
            });
            return 0;
        } catch (\Exception $e) {
            (new BaseService)->logger($e,"Error processing user deactivation");
            return 1;
        }
        
    }
}
