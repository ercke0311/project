<?php

namespace App\Http\Requests;

class LoginRequest extends ApiBaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => '請輸入電子郵件',
            'password.required' => '請輸入密碼',
        ];
    }
}
