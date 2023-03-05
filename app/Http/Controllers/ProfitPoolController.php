<?php

namespace App\Http\Controllers;

use App\Services\ProfitPoolService;
use Illuminate\Http\Request;

class ProfitPoolController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new ProfitPoolService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->service->all();
        return response()->json($data, $data['status']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $this->service->create($request->all());
        return response()->json($data, $data['status']);
    }

    
}
