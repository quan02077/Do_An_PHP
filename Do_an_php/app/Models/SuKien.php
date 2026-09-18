<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuKien extends Model
{
    protected $table = 'su_kien';
    protected $guarded = [];

    // Quan hệ: Một sự kiện thuộc về 1 danh mục
    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'danh_muc_id');
    }

    // Quan hệ: Một sự kiện có nhiều lượt đăng ký
    public function dangKys()
    {
        return $this->hasMany(DangKy::class, 'su_kien_id');
    }

    // Quan hệ: Một sự kiện được tạo bởi 1 người dùng (quản trị viên / ban tổ chức)
    public function nguoiTao()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_tao_id');
    }

    // Quan hệ: Một sự kiện có thể được nhiều người dùng yêu thích (bookmark)
    public function yeuThichs()
    {
        return $this->hasMany(YeuThich::class, 'su_kien_id');
    }

    public function getSoLuongDaDangKyAttribute()
    {
        return $this->relationLoaded('dangKys') ? $this->dangKys->count() : $this->dangKys()->count();
    }

    public function getUrlHinhAnhAttribute()
    {
        if (empty($this->hinh_anh)) {
            return 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=800&h=500&fit=crop';
        }

        if (str_starts_with($this->hinh_anh, 'http://') || str_starts_with($this->hinh_anh, 'https://')) {
            return $this->hinh_anh;
        }

        return asset('uploads/su_kien/' . $this->hinh_anh);
    }
}