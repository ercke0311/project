<?php

namespace App\Http\Requests;

class RegisterRequest extends ApiBaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed'],
            'name' => ['required'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '名稱',
            'email' => '電子郵件',
            'password' => '密碼',
        ];
    }
}
