<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_su_kien'        => ['required', 'string', 'max:255'],
            'danh_muc_id'        => ['required', 'integer', 'exists:danh_muc,id'],
            'mo_ta'              => ['nullable', 'string'],
            'hinh_anh'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'thoi_gian_bat_dau'  => ['required', 'date'],
            'thoi_gian_ket_thuc' => ['required', 'date', 'after:thoi_gian_bat_dau'],
            'dia_diem'           => ['required', 'string', 'max:255'],
            'ban_to_chuc'        => ['nullable', 'string', 'max:255'],
            'so_luong_toi_da'    => ['required', 'integer', 'min:1'],
            'gia_ve'             => ['required', 'numeric', 'min:0'],
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
            'hinh_anh.image'              => 'Hình ảnh tải lên phải là tệp ảnh.',
            'hinh_anh.mimes'              => 'Định dạng ảnh chỉ chấp nhận: jpeg, png, jpg, webp.',
            'hinh_anh.max'                => 'Kích thước ảnh không được vượt quá 2MB.',
            'thoi_gian_bat_dau.required'  => 'Vui lòng chọn thời gian bắt đầu sự kiện.',
            'thoi_gian_bat_dau.date'      => 'Thời gian bắt đầu không đúng định dạng ngày giờ.',
            'thoi_gian_ket_thuc.required' => 'Vui lòng chọn thời gian kết thúc sự kiện.',
            'thoi_gian_ket_thuc.date'     => 'Thời gian kết thúc không đúng định dạng ngày giờ.',
            'thoi_gian_ket_thuc.after'    => 'Thời gian kết thúc phải diễn ra sau thời gian bắt đầu.',
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
}
