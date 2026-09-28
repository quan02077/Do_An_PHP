<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuKien extends Model
{
    use HasFactory;

    protected $table = 'su_kien';

    protected $fillable = [
        'danh_muc_id',
        'nguoi_tao_id',
        'ten_su_kien',
        'slug',
        'mo_ta',
        'hinh_anh',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'dia_diem',
        'ban_to_chuc',
        'so_luong_toi_da',
        'gia_ve',
        'trang_thai',
        'noi_bat',
    ];

    protected function casts(): array
    {
        return [
            'thoi_gian_bat_dau' => 'datetime',
            'thoi_gian_ket_thuc' => 'datetime',
            'gia_ve' => 'decimal:2',
            'so_luong_toi_da' => 'integer',
            'noi_bat' => 'boolean',
        ];
    }

    /**
     * Sự kiện thuộc về 1 danh mục
     */
    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'danh_muc_id', 'id');
    }

    /**
     * Sự kiện do 1 người dùng tạo / chủ trì
     */
    public function nguoiTao()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_tao_id', 'id');
    }

    /**
     * Danh sách lượt đăng ký của sự kiện
     */
    public function dangKys()
    {
        return $this->hasMany(DangKy::class, 'su_kien_id', 'id');
    }

    /**
     * Danh sách người dùng tham gia sự kiện (nhiều - nhiều)
     */
    public function nguoiThamGias()
    {
        return $this->belongsToMany(NguoiDung::class, 'dang_ky', 'su_kien_id', 'nguoi_dung_id')
                    ->withPivot('ma_ve', 'thoi_gian_dang_ky', 'trang_thai', 'ghi_chu')
                    ->withTimestamps();
    }

    /**
     * Danh sách lượt yêu thích của sự kiện
     */
    public function yeuThichs()
    {
        return $this->hasMany(YeuThich::class, 'su_kien_id', 'id');
    }

    /**
     * Danh sách người dùng yêu thích sự kiện (nhiều - nhiều)
     */
    public function nguoiYeuThichs()
    {
        return $this->belongsToMany(NguoiDung::class, 'yeu_thich', 'su_kien_id', 'nguoi_dung_id')
                    ->withTimestamps();
    }

    /**
     * Kiểm tra sự kiện có miễn phí vé hay không
     */
    public function getIsFreeAttribute(): bool
    {
        return (float) $this->gia_ve <= 0;
    }

    /**
     * Định dạng hiển thị giá vé (VD: "Miễn phí" hoặc "100.000 đ")
     */
    public function getGiaVeFormattedAttribute(): string
    {
        if ($this->is_free) {
            return 'Miễn phí';
        }
        return number_format($this->gia_ve, 0, ',', '.') . ' đ';
    }

    /**
     * Trả về đường dẫn ảnh đầy đủ từ tên file lưu trong CSDL (VD: "001.jpg")
     */
    public function getUrlHinhAnhAttribute(): string
    {
        if (empty($this->hinh_anh)) {
            return asset('uploads/su_kien/001.jpg');
        }
        if (str_starts_with($this->hinh_anh, 'http://') || str_starts_with($this->hinh_anh, 'https://')) {
            return $this->hinh_anh;
        }
        // Nếu trong CSDL chỉ lưu tên file như "001.jpg"
        if (!str_contains($this->hinh_anh, '/')) {
            return asset('uploads/su_kien/' . $this->hinh_anh);
        }
        return asset($this->hinh_anh);
    }

    /**
     * Số lượng vé / lượt đã đăng ký của sự kiện
     */
    public function getSoLuongDaDangKyAttribute(): int
    {
        if ($this->relationLoaded('dangKys')) {
            return $this->dangKys->count();
        }
        return $this->dangKys()->count();
    }
}
