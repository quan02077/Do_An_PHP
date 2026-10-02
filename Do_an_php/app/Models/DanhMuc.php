<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    use HasFactory;

    protected $table = 'danh_muc';

    protected $fillable = [
        'ten_danh_muc',
        'mo_ta',
        'trang_thai',
    ];

    protected static function booted(): void
    {
        // Rule: Chặn xóa danh mục nếu vẫn còn sự kiện thuộc danh mục đó
        static::deleting(function ($danhMuc) {
            if ($danhMuc->suKiens()->exists()) {
                throw new \Exception("Không thể xóa danh mục '{$danhMuc->ten_danh_muc}' vì vẫn còn sự kiện thuộc danh mục này!");
            }
        });
    }

    /**
     * Danh sách sự kiện thuộc danh mục này
     */
    public function suKiens()
    {
        return $this->hasMany(SuKien::class, 'danh_muc_id', 'id');
    }
}
