<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trang_thai' => ['required', 'in:hoat_dong,bi_khoa'],
        ];
    }

    public function messages(): array
    {
        return [
            'trang_thai.required' => 'Vui lòng chọn trạng thái tài khoản.',
            'trang_thai.in'       => 'Trạng thái tài khoản không hợp lệ.',
        ];
    }
}
