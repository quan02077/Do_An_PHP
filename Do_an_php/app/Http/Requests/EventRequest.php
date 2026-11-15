<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $ruleReq = $isUpdate ? ['sometimes', 'required'] : ['required'];

        return [
            'ten_su_kien'        => array_merge($ruleReq, ['string', 'max:255']),
            'danh_muc_id'        => array_merge($ruleReq, ['integer', 'exists:danh_muc,id']),
            'mo_ta'              => ['nullable', 'string'],
            'hinh_anh'           => ['nullable', 'string', 'max:255'],
            'hinh_anh_tai_len'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'thoi_gian_bat_dau'  => array_merge($ruleReq, ['date']),
            'thoi_gian_ket_thuc' => array_merge($ruleReq, ['date']),
            'dia_diem'           => array_merge($ruleReq, ['string', 'max:255']),
            'ban_to_chuc'        => ['nullable', 'string', 'max:255'],
            'so_luong_toi_da'    => array_merge($ruleReq, ['integer', 'min:1']),
            'gia_ve'             => array_merge($ruleReq, ['numeric', 'min:0']),
            'trang_thai'         => ['nullable', 'in:nhap,cong_khai,da_huy'],
            'noi_bat'            => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_su_kien.required'        => 'Vui lòng nhập tên sự kiện.',
            'ten_su_kien.max'             => 'Tên sự kiện không được vượt quá 255 ký tự.',
            'danh_muc_id.required'        => 'Vui lòng chọn danh mục sự kiện.',
            'danh_muc_id.exists'          => 'Danh mục được chọn không hợp lệ.',
            'hinh_anh.max'                => 'Đường dẫn ảnh không được vượt quá 255 ký tự.',
            'hinh_anh_tai_len.image'      => 'Tệp tải lên phải là ảnh.',
            'hinh_anh_tai_len.mimes'      => 'Ảnh chỉ chấp nhận định dạng jpeg, png, jpg hoặc webp.',
            'hinh_anh_tai_len.max'        => 'Kích thước ảnh không được vượt quá 2MB.',
            'thoi_gian_bat_dau.required'  => 'Vui lòng chọn thời gian bắt đầu sự kiện.',
            'thoi_gian_bat_dau.date'      => 'Thời gian bắt đầu không đúng định dạng ngày giờ.',
            'thoi_gian_ket_thuc.required' => 'Vui lòng chọn thời gian kết thúc sự kiện.',
            'thoi_gian_ket_thuc.date'     => 'Thời gian kết thúc không đúng định dạng ngày giờ.',
            'dia_diem.required'           => 'Vui lòng nhập địa điểm tổ chức sự kiện.',
            'so_luong_toi_da.required'    => 'Vui lòng nhập số lượng vé tối đa.',
            'so_luong_toi_da.integer'     => 'Số lượng vé phải là số nguyên.',
            'so_luong_toi_da.min'         => 'Số lượng vé phải lớn hơn hoặc bằng 1.',
            'gia_ve.required'             => 'Vui lòng nhập giá vé.',
            'gia_ve.numeric'              => 'Giá vé phải là một con số hợp lệ.',
            'gia_ve.min'                  => 'Giá vé không được là số âm (0 là miễn phí).',
            'trang_thai.in'               => 'Trạng thái sự kiện không hợp lệ.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $start = $this->input('thoi_gian_bat_dau');
            $end = $this->input('thoi_gian_ket_thuc');

            if ($start && $end && strtotime($end) < strtotime($start)) {
                $validator->errors()->add('thoi_gian_ket_thuc', 'Thời gian kết thúc không được nhỏ hơn ngày bắt đầu.');
            }
        });
    }
}
