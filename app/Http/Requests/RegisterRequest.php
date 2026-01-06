<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RegisterRequest extends ApiBaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed'],
            'name' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => '此電子郵件已被註冊',
            'email.required' => '請輸入電子郵件',
            'password.confirmed' => '密碼確認不一致',
        ];
    }
}
