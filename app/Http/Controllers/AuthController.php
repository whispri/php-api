<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\CheckPhoneRequest;
use App\Http\Requests\Auth\SigninRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\VerifyOTPRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * Summary of signup
     * @param \App\Http\Requests\Auth\SignupRequest $request
     * @return void
     */
    function signup(SignupRequest $request)
    {
        $data = $this->service->signup($request);
        return $this->response($data);
    }

    function verifyOTP(VerifyOTPRequest $request)
    {
        $data = $this->service->verifyOTP($request);
        return $this->response($data);
    }

    function updateProfile(UpdateProfileRequest $request)
    {
        $data = $this->service->updateProfile($request);
        return $this->response($data);
    }

    function signin(SigninRequest $request)
    {
        $data = $this->service->signin($request);
        return $this->response($data);
    }

    function checkPhone(CheckPhoneRequest $request)
    {
        $data = $this->service->checkPhone($request);
        return $this->response($data);
    }

    function me(Request $request)
    {
        $data = $this->service->me($request);
        return $this->response($data);
    }
}
