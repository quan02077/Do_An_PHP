<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    protected $table = 'danh_muc';
    protected $guarded = [];

    public function suKiens()
    {
        return $this->hasMany(SuKien::class, 'danh_muc_id');
    }
}
