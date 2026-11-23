<?php

namespace Database\Factories;

use App\Models\DanhGia;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DanhGia>
 */
class DanhGiaFactory extends Factory
{
    protected $model = DanhGia::class;

    public function definition(): array
    {
        return [
            'nguoi_dung_id' => NguoiDung::factory(),
            'su_kien_id' => SuKien::factory(),
            'so_sao' => fake()->numberBetween(3, 5),
            'noi_dung' => fake()->sentence(12),
            'trang_thai' => 'hien',
        ];
    }
}
