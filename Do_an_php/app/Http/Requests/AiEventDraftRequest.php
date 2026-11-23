<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AiEventDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mo_ta_nhanh' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'mo_ta_nhanh.required' => 'Vui lòng nhập mô tả nhanh về sự kiện.',
            'mo_ta_nhanh.string'   => 'Mô tả nhanh phải là chuỗi văn bản hợp lệ.',
            'mo_ta_nhanh.min'      => 'Mô tả nhanh cần có ít nhất 10 ký tự để AI soạn chính xác.',
            'mo_ta_nhanh.max'      => 'Mô tả nhanh không được vượt quá 1000 ký tự.',
        ];
    }
}
