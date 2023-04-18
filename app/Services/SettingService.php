<?php
namespace App\Services;

use App\Repositories\SettingRepository;
use App\Repositories\ReferralBonusSettingRepository;
use \Exception;
class SettingService{

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
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function getSettings()
    {
        try {
            $rank = $this->settingRepo->getSettings();
            return ['data' => $rank, 'message' => 'Setting created succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function get(string $column)
    {
        try {
            $data = $this->settingRepo->get($column);
            return ['data' => $data, 'status' => 200, 'success' => true];
        } catch (Exception $e) {
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function update(array $data)
    {
        try {
            $this->settingRepo->update($data);
            return ['message' => 'Setting updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function updateReferralBonusSetting(Int $id, array $data)
    {
        try {
            $this->referralBonusSetting->update($id,$data);
            return ['message' => 'Referral bonus setting updated succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function getReferralBonusSetting()
    {
        try {
            $data = $this->referralBonusSetting->all();
            return ['data'=>$data, 'message' => 'Referral bonus setting fetched succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function delete()
    {
        try {
            $this->settingRepo->table->delete(1);
            return ['message' => 'Setting deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            $message = env('APP_ENV' == 'production') ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}