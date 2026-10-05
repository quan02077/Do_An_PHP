<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:nguoi_dung,email'],
            'phone'    => ['nullable', 'string', 'max:10'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Vui lòng nhập họ và tên của bạn.',
            'name.string'       => 'Họ và tên phải là chuỗi ký tự.',
            'name.max'          => 'Họ và tên không được vượt quá 100 ký tự.',
            'email.required'    => 'Vui lòng nhập địa chỉ email của bạn.',
            'email.email'       => 'Địa chỉ email không đúng định dạng.',
            'email.max'         => 'Email không được vượt quá 150 ký tự.',
            'email.unique'      => 'Địa chỉ email này đã được sử dụng.',
            'phone.max'         => 'Số điện thoại không được vượt quá 10 ký tự.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'      => 'Mật khẩu phải có tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
