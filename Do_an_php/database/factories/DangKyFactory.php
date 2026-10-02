<?php

namespace Database\Factories;

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DangKyFactory extends Factory
{
    protected $model = DangKy::class;

    public function definition(): array
    {
        return [
            'nguoi_dung_id' => NguoiDung::factory(),
            'su_kien_id' => SuKien::factory(),
            'ma_ve' => 'VE-' . strtoupper(Str::random(8)),
            'thoi_gian_dang_ky' => now(),
            'trang_thai' => 'da_xac_nhan',
            'ghi_chu' => fake()->optional()->sentence(),
        ];
    }
}
