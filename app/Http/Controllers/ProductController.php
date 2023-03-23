<?php

namespace App\Http\Controllers;

use App\Services\CompanyProductService;
use Illuminate\Http\Request;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;

class ProductController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new CompanyProductService; 
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
    public function store(ProductCreateRequest $request)
    {
        $data = $this->service->create($request->all());
        return response()->json($data, $data['status']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductUpdateRequest $request, Int $id)
    {
        $data = $this->service->update($id, $request->all());
        return response()->json($data, $data['status']);
    }

    public function show(Int $id)
    {
        $data = $this->service->get($id);
        return response()->json($data, $data['status']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Int $id)
    {
        $data = $this->service->delete($id);
        return response()->json($data, $data['status']);
    }
}
