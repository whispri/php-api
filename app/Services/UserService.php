<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService extends BaseService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    function search($data)
    {
        $data = $this->repository->searchUsersExceptMe($data->q, auth()->id());
        return $data;
    }
}
