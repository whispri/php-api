<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use Faker\Provider\Base;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $this->user()->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $this->user()->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bio' => 'nullable|string|max:500'
        ];
    }
}
