<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = Auth::id() ?? session('user')?->id;

        return [
            'ho_ten'        => ['required', 'string', 'max:100'],
            'email'         => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('nguoi_dung', 'email')->ignore($userId),
            ],
            'so_dien_thoai' => ['nullable', 'string', 'max:15'],
            'dia_chi'       => ['nullable', 'string', 'max:255'],
            'tieu_su'       => ['nullable', 'string', 'max:500'],
            'anh_dai_dien'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required'     => 'Vui lòng nhập họ và tên của bạn.',
            'ho_ten.max'          => 'Họ và tên không được vượt quá 100 ký tự.',
            'email.required'      => 'Vui lòng nhập địa chỉ email.',
            'email.email'         => 'Địa chỉ email không đúng định dạng.',
            'email.unique'        => 'Địa chỉ email này đã có người sử dụng.',
            'so_dien_thoai.max'   => 'Số điện thoại không được vượt quá 15 ký tự.',
            'dia_chi.max'         => 'Địa chỉ không được vượt quá 255 ký tự.',
            'anh_dai_dien.image'  => 'Ảnh đại diện tải lên phải là tệp ảnh.',
            'anh_dai_dien.mimes'  => 'Định dạng ảnh chỉ chấp nhận: jpeg, png, jpg, webp.',
            'anh_dai_dien.max'    => 'Kích thước ảnh đại diện không được vượt quá 2MB.',
        ];
    }
}
