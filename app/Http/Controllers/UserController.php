<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }
    function search(Request $request)
    {
        $data = $this->service->search($request);
        return $this->response($data);
    }

    function getUserById(Request $request, $userId)
    {
        $data = User::find($userId);
        return $this->response($data);
    }
}
