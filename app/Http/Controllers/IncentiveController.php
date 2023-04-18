<?php

namespace App\Http\Controllers;

use App\Services\IncentiveService;
use Illuminate\Http\Request;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\IncentiveCreateRequest;

class IncentiveController extends Controller
{
    private $service,$baseService;

    public function __construct()
    {
        $this->service = new IncentiveService;
        $this->baseService = new BaseService;
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
    public function store(IncentiveCreateRequest $request)
    {
        try {  
            $file_url = $request->hasFile('image') 
            ? $this->baseService->processFileUpload($request,'image','incentives','public')
            : null ;
        } catch (\Exception $e) {
            Log::error("Error creating incentive",[$e]);
            return response()->json(["message"=>"An internal error occured"],500);
        }
        $request['file_path'] = $file_url;
        unset($request['image']);
        $data = $this->service->create($request->only('rank_id','file_path','incentive','worth'));
        return response()->json($data, $data['status']);
    }

    public function show($id)
    {
        $data = $this->service->get($id);
        return response()->json($data, $data['status']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Incentive  $incentive
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(IncentiveCreateRequest $request, Int $id)
    {
        try {  
            $file_url = $request->hasFile('file_path') 
            ? $this->baseService->processFileUpload($request,'file_path','incentives','public')
            : null ;
        } catch (\Exception $e) {
            Log::error("Error updating incentive",[$e]);
            return response()->json(["message"=>"An internal error occured"],500);
        }
        $reqData = $request->only('rank_id','incentive','worth');
        $reqData['file_path'] = $file_url;
        $data = $this->service->update($id, $reqData);
        return response()->json($data, $data['status']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Incentive  $incentive
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Int $id)
    {
        $data = $this->service->delete($id);
        return response()->json($data, $data['status']);
    }

    public function claim(Request $request)
    {
        
    }
}
