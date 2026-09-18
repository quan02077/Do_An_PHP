<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NguoiDung extends Model
{
    protected $table = 'nguoi_dung';
    protected $guarded = [];

    // Quan hệ: Một người dùng có nhiều vé đã đăng ký
    public function dangKys()
    {
        return $this->hasMany(DangKy::class, 'nguoi_dung_id');
    }

    // Quan hệ: Một người dùng có nhiều sự kiện yêu thích (bookmarks)
    public function yeuThichs()
    {
        return $this->hasMany(YeuThich::class, 'nguoi_dung_id');
    }

    // Quan hệ: Một người dùng (admin/người tạo) có thể tạo nhiều sự kiện
    public function suKienDaTaos()
    {
        return $this->hasMany(SuKien::class, 'nguoi_tao_id');
    }
}
