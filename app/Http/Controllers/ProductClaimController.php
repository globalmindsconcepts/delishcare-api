<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductClaimService;
use App\Http\Requests\ProductClaimCreate;

class ProductClaimController extends Controller
{
    private $service;
    function __construct(){
        $this->service = new ProductClaimService;
    }
    public function all(Request $request)
    {
        $data = $this->service->all();
        return response()->json($data, $data['status']);
    }

    public function create(ProductClaimCreate $request, string $uuid)
    {
        $data = $this->service->create($uuid,$request->product_ids);
        return response()->json($data, $data['status']);
    }

    public function approve(Request $request, string $uuid)
    {
        $reqData = ['status'=>'approved'];
        $data = $this->service->update($uuid, $reqData);
        return response()->json($data, $data['status']);
    }

    public function decline(Request $request, string $uuid)
    {
        $reqData = ['status'=>'declined'];
        $data = $this->service->update($uuid, $reqData);
        return response()->json($data, $data['status']);
    }

    public function claimedProducts(Request $request, string $uuid)
    {
        $data = $this->service->claimedProducts($uuid);
        return response()->json($data, $data['status']);
    }

    public function totalProductSold(Request $request)
    {
        $data = $this->service->totalProductSold();
        return response()->json($data, $data['status']);
    }

    public function totalProductPV(Request $request)
    {
        $data = $this->service->totalProductPV();
        return response()->json($data, $data['status']);
    }

    public function sumClaimedProducts(Request $request)
    {
        $data = $this->service->sumClaimedProducts(); 
        return response()->json($data, $data['status']);
    }
}

