<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductServiceController extends Controller
{
    private $service;

    public function __construct(){
        $this->service = new ProductService;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Int $id)
    {
        $data = $this->service->get($id);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductService  $productService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Int $id)
    {
        $data = $this->service->update($id, $request->all());
        return response()->json($data, $data['status']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductService  $productService
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Int $id)
    {
        $data = $this->service->delete($id);
        return response()->json($data, $data['status']);
    }
}
