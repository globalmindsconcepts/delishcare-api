<?php
namespace App\Services;

use App\Repositories\RankRepository;
use App\Repositories\SettingRepository;
use App\Repositories\PackageRepository;
use App\Services\GenealogyService;
use App\Repositories\ReferralBonusSettingRepository;
use App\Repositories\UserRepository;
use App\Repositories\PackagePaymentRepository;
use App\Repositories\ProfitPoolRepository;
use App\Repositories\EquilibrumBonusRepository;
use App\Repositories\LoyaltyBonusRepository;
use App\Repositories\GlobalProfitRepository;
use App\Repositories\WelcomeBonusRepository;
use App\Repositories\ReferralBonusRepository;
use App\Repositories\PlacementBonusRepository;
use App\Repositories\WithdrawalRepository;

/**
 * user wallet trait
 */
class WalletService extends BaseService{

    private $setting;
    private $referralBonus;
    //private $profitPoolBonus;
    private $globalProfitBonus;
    private $genealogyService;
    private $package;
    private $referralBonusSetting;
    private $user;
    private $packagePayment;
    private $profitPool, $equilibrumBonus, $loyaltyBonus, 
    $globalProfit, $welcomeBonus,$rank,$placementBonus,$withdrawal;
    public function __construct()
    {
        $this->setting = new SettingRepository;
        $this->genealogyService = new GenealogyService;
        $this->package = new PackageRepository;
        $this->referralBonusSetting = new ReferralBonusSettingRepository;
        $this->user = new UserRepository;
        $this->packagePayment = new PackagePaymentRepository;
        $this->profitPool = new ProfitPoolRepository;
        $this->equilibrumBonus = new EquilibrumBonusRepository;
        $this->loyaltyBonus = new LoyaltyBonusRepository;
        $this->globalProfit = new GlobalProfitRepository;
        $this->welcomeBonus = new WelcomeBonusRepository;
        $this->rank = new RankRepository;
        $this->referralBonus = new ReferralBonusRepository;
        $this->placementBonus = new PlacementBonusRepository;
        $this->withdrawal = new WithdrawalRepository;
    }

    /**
     * get system welcome bonus
     */
    public function computeWelcomeBonus(string $user_uuid, bool $fetch=false){
        if($fetch){
            return $this->welcomeBonus->table->where('user_uuid',$user_uuid)->first();
        }
        $user = $this->user->getUser($user_uuid);
        $package_id = $user['package_id'];
        $package_pv = $this->package->get($package_id)->point_value;
        $bonus_percentage = $this->setting->get('welcome_bonus_percentage')->welcome_bonus_percentage;
        $unit_pv = $this->setting->get('unit_point_value')->unit_point_value;
        $welcome_bonus = ($bonus_percentage/100 * $package_pv) * $unit_pv;

        $this->welcomeBonus->create([
            'user_uuid' => $user_uuid,
            'bonus' => $welcome_bonus,
            'data' => json_encode([
                'package_pv' => $package_pv,
                'package_id' => $package_id,
                'unit_pv' => $unit_pv
            ]),
            'created_at'=>now()
        ]);
    }

    public function referralBonus(string $user_uuid)
    {
        return $this->referralBonus->table->where('user_uuid', $user_uuid)->get();
    }

    public function placementBonus(string $user_uuid)
    {
        return $this->placementBonus->table->where('user_uuid', $user_uuid)->get();
    }

    public function totalBonus(string $user_uuid)
    {
        $welcome_bonus = $this->computeWelcomeBonus($user_uuid,true)->bonus ?? 0;
        $referral_bonus = $this->referralBonus($user_uuid)->sum('bonus');
        $placement_bonus = $this->placementBonus($user_uuid)->sum('bonus');
        $equilibrum_bonus = $this->equillibrumBonus($user_uuid,true)->sum('value');
        $loyalty_bonus = $this->loyaltyBonus($user_uuid,true)->sum('value');
        $profit_pool = $this->computeProfitPool($user_uuid, true)->sum('value');
        $global_profit = $this->computeUserGlobalProfitShare($user_uuid, 1, true)->sum('profit');

        //info('bonuses',['wel'=>$welcome_bonus,'ref'=>$referral_bonus,'plce'=>$placement_bonus,'equ'=>$equilibrum_bonus,'loy'=>$loyalty_bonus,'profp'=>$profit_pool,'global'=>$global_profit]);

        $total = $welcome_bonus + $referral_bonus + $placement_bonus + $equilibrum_bonus + $loyalty_bonus + $profit_pool + $global_profit;
        return $total;
    }

    public function totalBalance(string $uuid)
    {
        $balance = $this->totalBonus($uuid) - $this->withdrawal->userTotal($uuid);
        return $balance;
    }

     /**
     * get user referral bonus
     
    public function referralBonus($user_uuid,$user_package_id){
        $package = $this->package->get($user_package_id);
        switch ($package->vip) {
            case 'vip1':
               $generation_1 = $this->genealogyService->findChildren($user_uuid);
               foreach ($generation_1 as $key => $value) {
                    $generation_1_percentage = $this->referralBonusSetting->get($user_package_id)->generation_1_percentage;
                    $package_id = $this->user->getUser($value->child_id)['package_id'];
                    $package_pv = $this->package->get($package_id)->point_value;
                    ($package_pv * $generation_1_percentage) * $this->setting->get('unit_point_value');

                    foreach ($this->genealogyService->findChildren($value->child_id) as $key => $grandchild) {
                        $generation_2_percentage = $this->referralBonusSetting->get($user_package_id)->generation_2_percentage;
                        $grandchildPackage_id = $this->user->getUser($value->child_id)['package_id'];
                        $grandchildPackage_pv = $this->package->get($package_id)->point_value;
                        ($grandchildPackage_pv * $generation_2_percentage) * $this->setting->get('unit_point_value');
                    }
                }

                foreach ($generation_1 as $key => $value) {
                    $generation_1_percentage = $this->referralBonusSetting->get($user_package_id)->generation_1_percentage;
                    $package_id = $this->user->getUser($value->child_id)['package_id'];
                    $package_pv = $this->package->get($package_id)->point_value;
                    ($package_pv * $generation_1_percentage) * $this->setting->get('unit_point_value');
                }

                

                break;
            
            default:
                # code...
                break;
        }
        $referrals = $user->referreds->filter(function($item){
           return $item->referred->is_approve;
        });
        return $referrals->count() * $this->checkProperty('systemSetting')->value('referal_bonus');
    }*/

    // public function generateReferralBonusTree(&$family_arr=[],$user_uuid,$user_package_id,$count=1): array
    // {
    //     if($children = $this->genealogyService->findChildren($user_uuid,false)){
    //         //$count = 1;
    //         $child_count = 1;
    //         foreach ($children as $child) {
    //             if ($count == 7) {
    //                 break;
    //             }

    //             $param = "generation_{$count}_percentage";

    //             $generation_1_percentage = $this->referralBonusSetting->get($user_package_id)->{$param};
    //             $package_id = $this->user->getUser($child->child_id)['package_id'];
    //             $package_pv = $this->package->get($package_id)->point_value;
    //             $bonus = ($package_pv * $generation_1_percentage) * $this->setting->get('unit_point_value');

    //             $family_arr['children'] = [
    //                 "child_{$child_count}" => $bonus
    //             ];
    //             $child_count++;

    //             if ($child_count > 2) {
    //                 $this->generateReferralBonusTree($family_arr['children'], $child->child_id, $user_package_id, $count++);
    //             }
    //         }
    //     }
    //     return $family_arr;
    // }

    /**
     * calaculate user wallet ballance
     */


    public function equillibrumBonus(string $user_uuid,bool $fetch=false)
    {
        
        if($fetch){
            return $this->equilibrumBonus->table->where('user_uuid', $user_uuid)->get();//->sum('value');
        }

        $sum = 0;
        $bonus = 0;
        $sumDownlines =  $this->genealogyService->sumDownlines($sum, $user_uuid);
        $eqilibrum_bonus = $this->setting->get('equillibrum_bonus')->equillibrum_bonus;
        $eqilibrum_downline = $this->equilibrumDownline($sumDownlines);
        
        if($user = $this->equilibrumBonus->table->where('user_uuid', $user_uuid)->get()->last()){
            if($eqilibrum_downline > $user->num_downlines){
                $diff = $eqilibrum_downline  -  $user->num_downlines;
                $bonus = $diff/2 * $eqilibrum_bonus;
                if($user->bonus_value != $eqilibrum_bonus){
                    $this->equilibrumBonus->create([
                        'user_uuid'=>$user_uuid,
                        'value'=>$bonus,
                        'bonus_value'=>$eqilibrum_bonus,
                        'num_downlines'=>$eqilibrum_downline,
                        'created_at'=>now()
                    ]);
                }else{
                    $this->equilibrumBonus->table->where(['user_uuid' => $user_uuid, 'bonus_value' => $eqilibrum_bonus])->update(
                        [
                            'value' => $bonus,
                            'num_downlines'=>$diff,
                            'updated_at'=>now()
                        ]
                    );
                }
            }
        }else{

            $direct_downlines = \App\Models\Child::where('parent_id', $user_uuid)->count(); //$this->genealogyService->childrenTable->where(['parent_id'=>$user_uuid,'placer_id'=>null])->count();
            //info($direct_downlines);
            if($direct_downlines>=2){
                $bonus = $eqilibrum_downline/2 * $eqilibrum_bonus;
                $this->equilibrumBonus->create([
                    'user_uuid'=>$user_uuid,
                    'value'=>$bonus,
                    'bonus_value'=>$eqilibrum_bonus,
                    'num_downlines'=>$eqilibrum_downline,
                    'created_at'=>now()
                ]);
            }
        }
        
        return $bonus;   
    }

    private function equilibrumDownline(int $value)
    {
        if($value%2 !== 0){
            return $value - 1;
        }
        return $value;
    }

    public function loyaltyBonus(string $user_uuid, bool $fetch=false)
    {   
        if($fetch){
           return $this->loyaltyBonus->table->where('user_uuid', $user_uuid)->get();//->sum('value');
        }

        $loyalty_bonus = $this->setting->get('loyalty_bonus_percentage')->loyalty_bonus_percentage;
        $parent = $this->genealogyService->checkIfRefHasAParent($user_uuid);
        $equilibrum_bonus = $this->equillibrumBonus($parent);
        $bonus = $equilibrum_bonus * ($loyalty_bonus / 100);
        if($parent && $bonus>0){
            if($user = $this->loyaltyBonus->table->where('user_uuid',$user_uuid)->get()->last()){
                if($user->bonus_value != $loyalty_bonus){
                    $this->loyaltyBonus->create([
                        'user_uuid'=>$user_uuid,
                        'value'=>$bonus,
                        'bonus_value'=>$loyalty_bonus,
                        'created_at'=>now()
                    ]);
                }else{
                    $this->loyaltyBonus->table->where(['user_uuid' => $user_uuid, 'bonus_value' => $loyalty_bonus])
                        ->update(['value' => $bonus,'updated_at'=>now()]);
                }
            }else{
                $this->loyaltyBonus->create([
                    'user_uuid'=>$user_uuid,
                    'value'=>$bonus,
                    'bonus_value'=>$loyalty_bonus,
                    'created_at'=>now()
                ]);
            }
        }
    }

    public function computeProfitPool(string $user_uuid, bool $fetch=false)
    {
        if($fetch){
            return $this->profitPool->table->where('user_uuid', $user_uuid)->get();
        }

        $currentDate = date('Y-m-d h:i:s');

        $user = $this->user->getUser($user_uuid);
        $month = date('n');
        $currMonthProfitClaimed = $this->profitPool->table->where('user_uuid', '=', $user_uuid)->whereMonth('created_at', '=', $month)->count();
        $profitCount = $this->profitPool->table->where('user_uuid', '=', $user_uuid)->count();
        $profitDuration = $this->setting->get('profit_pool_duration')->profit_pool_duration; //6
        $eligiblePackages = $this->package->table->where('profit_pool_eligible', true)->get();

        $payment = $this->packagePayment->getData('user_uuid',$user_uuid)->first();
        
        $payment_date = $payment->updated_at;
        $date_diff  = strtotime($currentDate) - strtotime($payment_date);
        $days = round($date_diff / (60 * 60 * 24));

        if($user && $days>=30 && $currMonthProfitClaimed == 0 && $profitCount < $profitDuration){
            $eligible = $this->checkProfitPoolEligibility($user_uuid);
            //info('$eligible', [$eligible]);
            $package = $this->package->get($user['package_id']);
            if($eligiblePackages->contains('id','=',$package->id) && $eligible){
                $pool_percentage = $this->setting->get('profit_pool_percentage')->profit_pool_percentage/100;
                $unit_pv = $this->setting->get('unit_point_value')->unit_point_value;
                $profit = ($package->point_value * $pool_percentage) * $unit_pv;
                $this->profitPool->create([
                    'user_uuid'=>$user_uuid,
                    'value'=>$profit,
                    'data'=> json_encode([
                        'unit_pv'=>$unit_pv,
                        'profit_pool_percentage'=>$pool_percentage,
                        'package_pv'=>$package->point_value
                    ]),
                    'created_at'=>now()
                ]);
            }
        }
    }

    /**
     * Summary of checkProfitPoolEligibility
     * @param mixed $child_id
     * @param mixed $count
     * @return bool
     */
    private function checkProfitPoolEligibility($child_id,$count=0): bool
    {
        //start after 30 days
        //check 4 downlines in a month
        $year = date('Y');
        $month = date('n');
        $currentDate = date('Y-m-d h:i:s');
        if($count==4){
            return true;
        }

        if($children = $this->genealogyService->findChildren($child_id,false)){
            //info("payments", [$this->packagePayment->all()]);
            foreach($children as $child){
                //info("child", [$child->child_id]);

                $payment = $this->packagePayment->getData('user_uuid',$child->child_id)->first();

                //info("pay", [$payment->created_at]);
                
                $payment_date = $payment->updated_at;
                $date_diff  = strtotime($currentDate) - strtotime($payment_date);
                $days = round($date_diff / (60 * 60 * 24));
                $payment_month = date('n', strtotime($payment->created_at));
                $payment_year = date('Y', strtotime($payment->created_at));

                if($year == $payment_year && $month==$payment_month){
                    $count++;
                }
                //info('count', [$count]);
                $this->checkProfitPoolEligibility($child->child_id,$count);
            }
        }
        return $count>=4 ? true : false;
    }

    private function computeGlobalProfit()
    {
        $currYear = date('Y');
        $totalPvs = $this->packagePayment->table->where('status', '=', 'approved')->whereYear('updated_at',$currYear)->get()->sum('point_value');
        $global_profit_first_percentage = $this->setting->get('global_profit_first_percentage')->global_profit_first_percentage;
        $global_profit_second_percentage = $this->setting->get('global_profit_second_percentage')->global_profit_second_percentage;
        $firstBonus = ($global_profit_first_percentage / 100) * $totalPvs;
        $secondBonus = ($global_profit_second_percentage / 100) * $firstBonus;
        $unitPv = $this->setting->get('unit_point_value')->unit_point_value;
        $bonus = $secondBonus * $unitPv;
        return [
            'bonus'=>$bonus,
            'global_profit_first_percentage'=>$global_profit_first_percentage,
            'global_profit_second_percentage'=>$global_profit_second_percentage,
            'total_pv'=>$totalPvs,
            'unit_pv'=>$unitPv
        ];
    }

    public function computeUserGlobalProfitShare(string $user_uuid, int $user_count=1, bool $fetch=false)
    {
        if($fetch){
            return $this->globalProfit->table->where('user_uuid', $user_uuid)->get();
        }

        $user = $this->user->getUser($user_uuid);
        $rank_id = $user['rank_id'];

        $eligible_ranks = $this->rank->table->where('is_global_profit_eligible',true)->get();
        
        if($eligible_ranks->contains('id','=',$rank_id)){
            //info('Hi');
            $globalProfit = $this->computeGlobalProfit();
            $this->globalProfit->create([
                'user_uuid'=>$user_uuid,
                'profit'=>$globalProfit['bonus']/$user_count,
                'data'=> json_encode([
                    'total_pv'=>$globalProfit['total_pv'],
                    'unit_pv'=>$globalProfit['unit_pv'],
                    'global_profit_first_percentage'=>$globalProfit['global_profit_first_percentage'],
                    'global_profit_second_percentage'=>$globalProfit['global_profit_second_percentage']
                ]),
                'created_at'=>now()
            ]);
        }
    }

    public function totalEquilibrumBonus($count=false)
    {
        $bonus = $this->equilibrumBonus->table->get();//->sum('value');
        return $count ? $bonus->count() : $bonus->sum('value');
    }

    public function totalLoyaltyBonus($count=false)
    {
        $bonus = $this->loyaltyBonus->table->get();//->sum('value');
        return $count ? $bonus->count() : $bonus->sum('value');
    }

    public function totalProfitPoolBonus($count=false)
    {
        $bonus = $this->profitPool->table->get();//->sum('value');
        return $count ? $bonus->count() : $bonus->sum('value');
    }

    public function totalGlobalProfitBonus($count=false)
    {
        $bonus = $this->globalProfit->table->get();//->sum('profit');
        return $count ? $bonus->count() : $bonus->sum('profit');
    }

    public function totalWelcomeBonus($count=false)
    {
        $bonus = $this->welcomeBonus->table->get();//->sum('profit');
        return $count ? $bonus->count() : $bonus->sum('bonus');
    }

    public function totalCompanyWallet()
    {
       return $this->packagePayment->table->where('status','approved')->get()->sum('amount');
    }

    public function totalWithdrawals()
    {
        return $this->withdrawal->table->where('status','successful')->get()->sum('amount');
    }

    public function companyWalletBalance()
    {
        return $this->totalCompanyWallet() - $this->totalWithdrawals();
    }
}