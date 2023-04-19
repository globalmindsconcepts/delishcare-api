<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GenealogyService;
use App\Services\UserService;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\InviteGuestRequest;
use App\Http\Requests\SendMessage;

class UserController extends Controller
{
    private $genealogyService;
    private $userService;

    function __construct(){
        $this->genealogyService = new GenealogyService;
        $this->userService = new UserService;
    }

    public function users()
    {
        $data = $this->userService->all();
        return response()->json($data,$data['status']);
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

    public function downlines(Request $request, string $uuid)
    {
        $data = $this->userService->downlines($uuid);
        return response()->json($data,$data['status']);
    }

    public function directDownlines(Request $request, string $uuid)
    {
        $data = $this->userService->directDownlines($uuid);
        return response()->json($data,$data['status']);
    }

    public function genealogy(string $uuid)
    {
        $data = $this->userService->genealogy($uuid);
        return response()->json($data,$data['status']);
    }

    public function inviteGuest(InviteGuestRequest $request, string $uuid)
    {
        $data = $this->userService->inviteGuest($uuid,$request->only(['email','referrer']));
        return response()->json($data,$data['status']);
    }

    public function totalRegistrations(Request $request)
    {
        $data = $this->userService->totalRegistrations();
        return response()->json($data,$data['status']);
    }
    public function totalRegistrationPV(Request $request)
    {
        $data = $this->userService->totalRegistrationPV();
        return response()->json($data,$data['status']);
    }

    public function getUser(Request $request, string $uuid)
    {
        $data = $this->userService->user($uuid);
        return response()->json($data,$data['status']);
    }

    public function sendMessage(SendMessage $request, string $uuid)
    {
        $data = $this->userService->sendMessage($uuid,$request->validated());
        return response()->json($data,$data['status']);
    }

    public function paidUsers(Request $request)
    {
        $data = $this->userService->paidUsers();
        return response()->json($data,$data['status']);
    }

    public function totalPaidUsers(Request $request)
    {
        $data = $this->userService->totalPaidUsers();
        return response()->json($data,$data['status']);
    }

    public function sumPaidUsers(Request $request)
    {
        $data = $this->userService->sumPaidUsers();
        return response()->json($data,$data['status']);
    }

}
