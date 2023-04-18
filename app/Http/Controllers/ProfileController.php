<?php

namespace App\Http\Controllers;

use App\Services\UserProfileService;
use Illuminate\Http\Request;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\BankDetailsUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\Toggle2FARequest;
use App\Http\Requests\BankEditable;

class ProfileController extends Controller
{
    private $service,$baseService;

    public function __construct()
    {
        $this->service = new UserProfileService;
        $this->baseService = new BaseService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request,string $user_uuid)
    {
        try {  
            $file_url = $request->hasFile('image') 
            ? $this->baseService->processFileUpload($request,'image','profile-photos','public')
            : null ;
            $request['photo_path'] = $file_url;
            
        } catch (\Exception $e) {
            Log::error("Error creating profile",[$e]);
            return response()->json(["message"=>"An internal error occured"],500);
        }
        
        $data = $this->service->create($user_uuid,$request->all());
        return response()->json($data, $data['status']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $user_uuid)
    {
        $data = $this->service->get($user_uuid);
        return response()->json($data,$data['status']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProfileUpdateRequest $request, string $user_uuid)
    {
        try {  
            $file_url = $request->hasFile('image') 
            ? $this->baseService->processFileUpload($request,'image','profile-photos','public')
            : null ;
            $request['photo_path'] = $file_url;
        } catch (\Exception $e) {
            Log::error("Error updating profile",[$e]);
            return response()->json(["message"=>"An internal error occured"],500);
        }
        unset($request['image']);
        $user_uuid = $request->user()->uuid;
        $data = $this->service->update($user_uuid, $request->validated()+['photo_path'=>$request['photo_path']]);
        return response()->json($data, $data['status']);
    }

    public function updateBankDetails(BankDetailsUpdateRequest $request, string $user_uuid)
    {
        $data = $this->service->updateBankDetails($user_uuid,$request->all());
        return response()->json($data,$data['status']);
    }

    public function toggle2FA(Toggle2FARequest $request, string $uuid)
    {
        $data = $this->service->toggle2FA($uuid,$request->all());
        return response()->json($data,$data['status']);
    }

    public function setBankEditable(BankEditable $request, string $uuid)
    {
        $data = $this->service->bankEditable($uuid,$request->all());
        return response()->json($data,$data['status']);
    }
}
