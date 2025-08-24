<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByPhoneAndPrefix($phone, $prefix)
    {
        return $this->model::where('phone_prefix', 'like', $prefix . '%')
            ->where('phone', 'like', '%' . $phone)
            ->first();
    }

    public function searchUsersExceptMe($keyword, $myId)
    {
        return $this->model::where('id', '!=', $myId)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('username', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            })
            ->get();
    }
}
