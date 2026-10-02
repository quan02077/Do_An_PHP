<?php

namespace Database\Factories;

use App\Models\NguoiDung;
use Illuminate\Database\Eloquent\Factories\Factory;

class NguoiDungFactory extends Factory
{
    protected $model = NguoiDung::class;

    public function definition(): array
    {
        return [
            'ho_ten' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'mat_khau' => '123456',
            'so_dien_thoai' => fake()->phoneNumber(),
            'vai_tro' => 'user',
            'trang_thai' => 'hoat_dong',
            'tieu_su' => fake()->sentence(),
            'dia_chi' => fake()->city(),
            'ngay_sinh' => fake()->date(),
            'gioi_tinh' => fake()->randomElement(['Nam', 'Nữ']),
            'anh_dai_dien' => null,
        ];
    }
}
