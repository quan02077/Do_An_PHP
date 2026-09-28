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

    /**
     * Danh sách sự kiện thuộc danh mục này
     */
    public function suKiens()
    {
        return $this->hasMany(SuKien::class, 'danh_muc_id', 'id');
    }
}
