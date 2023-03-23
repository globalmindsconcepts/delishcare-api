<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GenealogyService;
use App\Services\UserService;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
    private $genealogyService;
    private $userService;

    function __construct(){
        $this->genealogyService = new GenealogyService;
        $this->userService = new UserService;
    }

    public function totalPointValues(Request $request, string $uuid)
    {
        $init = 0;
        $total_pv = $this->genealogyService->sumUserPointValue($uuid,$init);
        return response()->json(['total_pv'=>$total_pv],200);
    }

    public function update(UserUpdateRequest $request, string $uuid)
    {
        $data = $this->userService->update($uuid,$request->all());
        return response()->json($data,$data['status']);
    }

    public function uplineDetails(Request $request, string $uuid)
    {
        $data = $this->userService->uplineDetails($uuid);
        return response()->json($data,$data['status']);
    }
}
