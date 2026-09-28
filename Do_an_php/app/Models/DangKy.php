<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DangKy extends Model
{
    use HasFactory;

    protected $table = 'dang_ky';

    protected $fillable = [
        'nguoi_dung_id',
        'su_kien_id',
        'ma_ve',
        'thoi_gian_dang_ky',
        'trang_thai',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'thoi_gian_dang_ky' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($dangKy) {
            if (empty($dangKy->ma_ve)) {
                $dangKy->ma_ve = 'VE-' . strtoupper(Str::random(8));
            }
            if (empty($dangKy->thoi_gian_dang_ky)) {
                $dangKy->thoi_gian_dang_ky = now();
            }
        });
    }

    /**
     * Vé đăng ký thuộc về 1 người dùng
     */
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id', 'id');
    }

    /**
     * Vé đăng ký thuộc về 1 sự kiện
     */
    public function suKien()
    {
        return $this->belongsTo(SuKien::class, 'su_kien_id', 'id');
    }
}
