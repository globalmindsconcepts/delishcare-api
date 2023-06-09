<?php
namespace App\Services;

use App\Repositories\ReferralBonusRepository;
use App\Repositories\EquilibrumBonusRepository;
use App\Repositories\LoyaltyBonusRepository;
use \Exception;
//use \Exception;
class BonusService extends BaseService{

    private $referralBonusRepo, $equilibrumBonusRepo, $loyaltyBonusRepo;
    public function __construct(){
        $this->referralBonusRepo = new ReferralBonusRepository;
        $this->equilibrumBonusRepo = new EquilibrumBonusRepository;
        $this->loyaltyBonusRepo = new LoyaltyBonusRepository;
    }

    public function createReferralBonus(array $data)
    {
        try {
           $rank = $this->referralBonusRepo->create($data);
            return ['data' => $rank, 'message' => 'Referral bonus created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error creating referral bonus");
        }
    }

    public function createEquilibrumBonus(array $data)
    {
        try {
           $rank = $this->equilibrumBonusRepo->create($data);
            return ['data' => $rank, 'message' => 'Equilibrum bonus created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error creating equilibrum bonus");
        }
    }

    public function createLoyaltyBonus(array $data)
    {
        try {
           $rank = $this->loyaltyBonusRepo->create($data);
            return ['data' => $rank, 'message' => 'Loyalty bonus created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error creating loyalty bonus");
        }
    }

    public function totalEquilibrumBonus()
    {

    }

    public function totalLoyaltyBonus()
    {
        
    }

    public function totalProfitPoolBonus()
    {
        
    }

    public function totalGLobalProfitBonus()
    {
        
    }
}