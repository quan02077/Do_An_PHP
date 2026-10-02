<?php

namespace Database\Factories;

use App\Models\DanhMuc;
use Illuminate\Database\Eloquent\Factories\Factory;

class DanhMucFactory extends Factory
{
    protected $model = DanhMuc::class;

    public function definition(): array
    {
        return [
            'ten_danh_muc' => fake()->words(2, true),
            'mo_ta' => fake()->sentence(),
            'trang_thai' => 'hoat_dong',
        ];
    }
}
