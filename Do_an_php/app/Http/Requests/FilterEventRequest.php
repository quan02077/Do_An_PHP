<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'   => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:danh_muc,id'],
            'status'   => ['nullable', 'string', 'in:sap_dien_ra,dang_dien_ra,da_ket_thuc,yeu_thich,da_huy'],
        ];
    }

    public function messages(): array
    {
        return [
            'search.max'      => 'Từ khóa tìm kiếm không được vượt quá 100 ký tự.',
            'category.exists' => 'Danh mục tìm kiếm không tồn tại trong hệ thống.',
            'status.in'       => 'Trạng thái lọc không hợp lệ.',
        ];
    }
}
