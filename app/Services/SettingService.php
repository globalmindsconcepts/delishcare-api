<?php
namespace App\Services;

use App\Repositories\SettingRepository;
use App\Repositories\ReferralBonusSettingRepository;
use \Exception;
class SettingService extends BaseService{

    private $settingRepo,$referralBonusSetting;
    public function __construct(){
        $this->settingRepo = new SettingRepository;
        $this->referralBonusSetting = new ReferralBonusSettingRepository;
    }

    public function create(array $data)
    {
        try {
           $rank = $this->settingRepo->create($data);
            return ['data' => $rank, 'message' => 'Setting created succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e, "Error creating setting");
        }
    }

    public function getSettings()
    {
        try {
            $rank = $this->settingRepo->getSettings();
            return ['data' => $rank, 'message' => 'Setting fetched succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching settings");
        }
    }

    public function get(string $column)
    {
        try {
            $data = $this->settingRepo->get($column);
            return ['data' => $data, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching setting");
        }
    }

    public function update(array $data)
    {
        try {
            $this->settingRepo->update($data);
            return ['message' => 'Setting updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error updating settings");
        }
    }

    public function updateReferralBonusSetting(Int $id, array $data)
    {
        try {
            $this->referralBonusSetting->update($id,$data);
            return ['message' => 'Referral bonus setting updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error updating referral bonus settings");
        }
    }

    public function getReferralBonusSetting()
    {
        try {
            $data = $this->referralBonusSetting->all();
            return ['data'=>$data, 'message' => 'Referral bonus setting fetched succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching referral bonus settings");
        }
    }

    public function delete()
    {
        try {
            $this->settingRepo->table->delete(1);
            return ['message' => 'Setting deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            return $this->logger($e,"Error deleting settings");
        }
    }
}