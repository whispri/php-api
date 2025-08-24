<?php

namespace App\Services;

use App\Http\Resources\Auth\SigninResource;
use App\Repositories\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService extends BaseService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    function signup($data)
    {
        $this->repository->create($data->all());
        $data = null;
        return $data;
    }

    function verifyOTP($data)
    {
        $otp = $data->otp;
        if ($otp != 123456) {
            throw new BadRequestHttpException(OTP_INVALID_ERROR);
        }
        $data = null;
        return $data;
    }

    function signin($data)
    {
        $credentials = ['phone' => $data->phone, 'password' => $data->password];
        if (! auth()->attempt($credentials)) {
            throw new BadRequestHttpException(PASSWORD_INVALID_ERROR);
        }

        // Bước 2: Lấy thông tin user
        $user = auth()->user();

        // Bước 3: Tạo token kèm claims
        $token = JWTAuth::claims([
            'id'       => $user->id,
        ])->fromUser($user);

        $data = new SigninResource([
            'token' => $token,
            'user'  => $user
        ]);
        return $data;
    }

    function checkPhone($data)
    {
        $user = $this->repository->findBy('phone', $data->phone);
        if ($user) {
            throw new BadRequestHttpException(PHONE_ALREADY_EXITS);
        }
        $data = null;
        return $data;
    }

    function me($data)
    {
        $data = $data->user();
        return $data;
    }

    function updateProfile($data)
    {
        $data = $this->repository->update($data->all(), auth()->id());
        return $data;
    }
}
