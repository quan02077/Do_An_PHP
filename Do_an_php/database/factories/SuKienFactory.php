<?php

namespace Database\Factories;

use App\Models\DanhMuc;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SuKienFactory extends Factory
{
    protected $model = SuKien::class;

    public function definition(): array
    {
        $name = fake()->sentence(4);
        return [
            'danh_muc_id' => DanhMuc::factory(),
            'nguoi_tao_id' => NguoiDung::factory(),
            'ten_su_kien' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(5),
            'mo_ta' => fake()->paragraphs(3, true),
            'hinh_anh' => '001.jpg',
            'thoi_gian_bat_dau' => now()->addDays(rand(1, 30)),
            'thoi_gian_ket_thuc' => now()->addDays(rand(31, 35)),
            'dia_diem' => fake()->address(),
            'ban_to_chuc' => fake()->company(),
            'so_luong_toi_da' => fake()->numberBetween(50, 500),
            'gia_ve' => fake()->randomElement([0, 100000, 200000, 500000]),
            'trang_thai' => fake()->randomElement(['sap_dien_ra', 'dang_dien_ra', 'da_ket_thuc']),
            'noi_bat' => fake()->boolean(20),
        ];
    }
}
