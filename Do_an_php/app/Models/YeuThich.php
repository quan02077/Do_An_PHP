<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YeuThich extends Model
{
    use HasFactory;

    protected $table = 'yeu_thich';

    protected $fillable = [
        'nguoi_dung_id',
        'su_kien_id',
    ];

    /**
     * Yêu thích thuộc về 1 người dùng
     */
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id', 'id');
    }

    /**
     * Yêu thích trỏ tới 1 sự kiện
     */
    public function suKien()
    {
        return $this->belongsTo(SuKien::class, 'su_kien_id', 'id');
    }
}
