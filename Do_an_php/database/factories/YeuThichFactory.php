<?php

namespace Database\Factories;

use App\Models\NguoiDung;
use App\Models\SuKien;
use App\Models\YeuThich;
use Illuminate\Database\Eloquent\Factories\Factory;

class YeuThichFactory extends Factory
{
    protected $model = YeuThich::class;

    public function definition(): array
    {
        return [
            'nguoi_dung_id' => NguoiDung::factory(),
            'su_kien_id' => SuKien::factory(),
        ];
    }
}
