<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vai_tro' => ['required', 'in:user,admin'],
        ];
    }

    public function messages(): array
    {
        return [
            'vai_tro.required' => 'Vui lòng chọn vai trò tài khoản.',
            'vai_tro.in'       => 'Vai trò tài khoản không hợp lệ.',
        ];
    }
}
