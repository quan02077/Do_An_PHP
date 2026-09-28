<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class NguoiDung extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'nguoi_dung';

    protected $fillable = [
        'ho_ten',
        'email',
        'mat_khau',
        'so_dien_thoai',
        'vai_tro',
        'trang_thai',
        'tieu_su',
        'dia_chi',
        'ngay_sinh',
        'gioi_tinh',
        'anh_dai_dien',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    protected function casts(): array
    {
        return [
            'ngay_sinh' => 'date',
            'mat_khau' => 'hashed',
        ];
    }

    /**
     * Dùng trường mat_khau thay cho password mặc định của Laravel Auth
     */
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    /**
     * Các sự kiện do người dùng này tạo / tổ chức
     */
    public function suKiens()
    {
        return $this->hasMany(SuKien::class, 'nguoi_tao_id', 'id');
    }

    /**
     * Danh sách đơn / vé đăng ký tham gia sự kiện của người dùng
     */
    public function dangKys()
    {
        return $this->hasMany(DangKy::class, 'nguoi_dung_id', 'id');
    }

    /**
     * Các sự kiện mà người dùng đã đăng ký tham gia (Quan hệ nhiều - nhiều qua bảng dang_ky)
     */
    public function suKienDangKys()
    {
        return $this->belongsToMany(SuKien::class, 'dang_ky', 'nguoi_dung_id', 'su_kien_id')
                    ->withPivot('ma_ve', 'thoi_gian_dang_ky', 'trang_thai', 'ghi_chu')
                    ->withTimestamps();
    }

    /**
     * Danh sách sự kiện người dùng đã bấm yêu thích (Quan hệ 1 - nhiều)
     */
    public function yeuThichs()
    {
        return $this->hasMany(YeuThich::class, 'nguoi_dung_id', 'id');
    }

    /**
     * Các sự kiện người dùng đã yêu thích (Quan hệ nhiều - nhiều qua bảng yeu_thich)
     */
    public function suKienYeuThichs()
    {
        return $this->belongsToMany(SuKien::class, 'yeu_thich', 'nguoi_dung_id', 'su_kien_id')
                    ->withTimestamps();
    }
}
