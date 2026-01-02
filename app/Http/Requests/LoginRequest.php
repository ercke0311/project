<?php

namespace App\Http\Requests;

class LoginRequest extends ApiBaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            // 'email' => '電子郵件',
            // 'password' => '密碼',
        ];
    }
}
