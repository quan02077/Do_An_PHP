<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'so_sao'   => ['required', 'integer', 'between:1,5'],
            'noi_dung' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'so_sao.required' => 'Vui lòng chọn số sao đánh giá (từ 1 đến 5 sao).',
            'so_sao.integer'  => 'Số sao đánh giá phải là số nguyên từ 1 đến 5.',
            'so_sao.between'  => 'Số sao đánh giá phải nằm trong khoảng từ 1 đến 5.',
            'noi_dung.string' => 'Nội dung đánh giá không hợp lệ.',
            'noi_dung.max'    => 'Nội dung đánh giá không được vượt quá 1000 ký tự.',
        ];
    }
}
