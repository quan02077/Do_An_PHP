<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'                 => ['required', 'string', 'email'],
            'phone'                 => ['required', 'string'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'                 => 'Vui lòng nhập địa chỉ email đã đăng ký.',
            'email.email'                    => 'Địa chỉ email không đúng định dạng.',
            'phone.required'                 => 'Vui lòng nhập số điện thoại xác minh tài khoản.',
            'password.required'              => 'Vui lòng nhập mật khẩu mới.',
            'password.min'                   => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'password.confirmed'             => 'Xác nhận mật khẩu mới không trùng khớp.',
            'password_confirmation.required' => 'Vui lòng nhập lại mật khẩu mới.',
        ];
    }
}
