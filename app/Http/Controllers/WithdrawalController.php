<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WithdrawalService;

class WithdrawalController extends Controller
{
    private $service;
    function __construct()
    {
        $this->service = new WithdrawalService;
    }

    public function initiate(Request $request, string $uuid)
    {
        $data = $this->service->create($uuid,$request->all());
        return response()->json($data,$data['status']);
    }

    public function all(Request $request)
    {
        $data = $this->service->all();
        return response()->json($data,$data['status']);
    }

    public function userHistory(Request $request, string $uuid)
    {
        $data = $this->service->userHistory($uuid);
        return response()->json($data,$data['status']);
    }

    public function userTotal(Request $request, string $uuid)
    {
        $data = $this->service->userTotal($uuid);
        return response()->json($data,$data['status']);
    }

    public function total(Request $request, string $uuid)
    {
        $data = $this->service->total(); 
        return response()->json($data,$data['status']);
    }

    public function details(Request $request, string $id)
    {
        $data = $this->service->userTotal($id);
        return response()->json($data,$data['status']);
    }

    public function providerBalanceCheck(Request $request)
    {
        $data = $this->service->providerBalanceCheck();
        return response()->json($data,$data['status']);
    }
}
