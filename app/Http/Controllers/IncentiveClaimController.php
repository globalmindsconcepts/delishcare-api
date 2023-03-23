<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IncentiveClaimService;

class IncentiveClaimController extends Controller
{
    private $service;
    function __construct()
    {
        $this->service = new IncentiveClaimService;
    }

    public function all(Request $request)
    {
        $data = $this->service->all();
        return response()->json($data, $data['status']);
    }

    public function create(Request $request)
    {
        $data = $this->service->create($request->all());
        return response()->json($data, $data['status']);
    }

    public function approve(Request $request, Int $id)
    {
        $reqData = ['status'=>'approved'];
        $data = $this->service->update($id, $reqData);
        return response()->json($data, $data['status']);
    }

    public function decline(Request $request, Int $id)
    {
        $reqData = ['status'=>'declined'];
        $data = $this->service->update($id, $reqData);
        return response()->json($data, $data['status']);
    }

    public function claimedIncentives(Request $request, string $uuid)
    {
        $data = $this->service->claimedIncentives($uuid);
        return response()->json($data, $data['status']);
    }
}
