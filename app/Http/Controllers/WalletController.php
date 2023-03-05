<?php

namespace App\Http\Controllers;

use App\Services\WalletService;;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    private $service;

    public function __construct(){
        $this->service = new WalletService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function welcomeBonus(string $user_uuid)
    {
        try {
            $data = $this->service->computeWelcomeBonus($user_uuid,true);
            return response()->json(['bonus'=>$data->bonus,'success'=>true], 200);
        } catch (\Exception $e) {
            Log::error("welcome bonus error", [$e]);
            return response()->json(['message'=>'An error occured','success'=>false], 500);
        }
    }

    public function referralBonus(string $user_uuid)
    {
        try {
            $data = $this->service->referralBonus($user_uuid)->sum('bonus');
            return response()->json(['bonus'=>$data,'success'=>true], 200);
        } catch (\Exception $e) {
            Log::error("welcome bonus error", [$e]);
            return response()->json(['message'=>'An error occured','success'=>false], 500);
        }
    }

    public function placementBonus(string $user_uuid)
    {
        try {
            $data = $this->service->placementBonus($user_uuid)->sum('bonus');
            return response()->json(['bonus'=>$data,'success'=>true], 200);
        } catch (\Exception $e) {
            Log::error("placement bonus error", [$e]);
            return response()->json(['message'=>'An error occured','success'=>false], 500);
        }
    }

    public function equilibrumBonus(string $user_uuid)
    {
        try {
            $data = $this->service->equillibrumBonus($user_uuid,true);
            return response()->json(['bonus'=>$data->sum('value'),'success'=>true], 200);
        } catch (\Exception $e) {
            Log::error("equilibrum bonus error", [$e]);
            return response()->json(['message'=>'An error occured','success'=>false], 500);
        }
    }

    public function loyaltyBonus(string $user_uuid)
    {
        try {
            $data = $this->service->loyaltyBonus($user_uuid,true);
            return response()->json(['bonus'=>$data->sum('value'),'success'=>true], 200);
        } catch (\Exception $e) {
            Log::error("loyalty bonus error", [$e]);
            return response()->json(['message'=>'An error occured','success'=>false], 500);
        }
    }

    public function profitPool(string $user_uuid)
    {
        try {
            $data = $this->service->computeProfitPool($user_uuid,true)->sum('value');
            return response()->json(['profit_pool'=>$data,'success'=>true], 200);
        } catch (\Exception $e) {
            Log::error("profit pool error", [$e]);
            return response()->json(['message'=>'An error occured','success'=>false], 500);
        }
    }
    
    public function profitPools(string $user_uuid)
    {
        try {
            $data = $this->service->computeProfitPool($user_uuid,true);
            return response()->json($data+['success'=>true], 200);
        } catch (\Exception $e) {
            Log::error("profit pools error", [$e]);
            return response()->json(['message'=>'An error occured','success'=>false], 500);
        }
    }

    public function globalProfit(string $user_uuid)
    {
        try {
            $data = $this->service->computeUserGlobalProfitShare($user_uuid,1,true)->last()->profit;
            return response()->json(['bonus'=>$data,'success'=>true], 200);
        } catch (\Exception $e) {
            Log::error("global profit error", [$e]);
            return response()->json(['message'=>'An error occured','success'=>false], 500);
        }
    } 

    public function totalBonus(string $user_uuid)
    {
        try {
            $data = $this->service->totalBonus($user_uuid);
            return response()->json(['bonus'=>$data,'success'=>true], 200);
        } catch (\Exception $e) {
            Log::error("total bonus error", [$e]);
            return response()->json(['message'=>'An error occured','success'=>false], 500);
        }
    } 
}
