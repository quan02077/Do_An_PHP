<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    use HasFactory;

    protected $table = 'danh_gia';

    protected $fillable = [
        'nguoi_dung_id',
        'su_kien_id',
        'so_sao',
        'noi_dung',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'so_sao' => 'integer',
        ];
    }

    /** Người dùng viết đánh giá */
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id', 'id');
    }

    /** Sự kiện được đánh giá */
    public function suKien()
    {
        return $this->belongsTo(SuKien::class, 'su_kien_id', 'id');
    }
}
