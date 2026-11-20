<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Validator;

class EventDateValidationTest extends TestCase
{
    private function validateDates(array $data)
    {
        $request = new EventRequest();
        $request->merge($data);

        $rules = [
            'thoi_gian_bat_dau' => $request->rules()['thoi_gian_bat_dau'],
            'thoi_gian_ket_thuc' => $request->rules()['thoi_gian_ket_thuc'],
        ];

        $validator = Validator::make($data, $rules, $request->messages());
        $request->withValidator($validator);

        return $validator;
    }

    public function test_fails_when_start_date_is_before_today(): void
    {
        $validator = $this->validateDates([
            'thoi_gian_bat_dau' => '2020-01-01 10:00',
            'thoi_gian_ket_thuc' => '2020-01-01 12:00',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertContains('Thời gian bắt đầu không được nhỏ hơn ngày hiện tại.', $validator->errors()->get('thoi_gian_bat_dau'));
    }

    public function test_fails_when_end_date_is_before_today(): void
    {
        $validator = $this->validateDates([
            'thoi_gian_bat_dau' => date('Y-m-d H:i', strtotime('+1 day')),
            'thoi_gian_ket_thuc' => '2020-01-01 12:00',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertContains('Thời gian kết thúc không được nhỏ hơn ngày hiện tại.', $validator->errors()->get('thoi_gian_ket_thuc'));
    }

    public function test_fails_when_end_date_is_before_start_date(): void
    {
        $validator = $this->validateDates([
            'thoi_gian_bat_dau' => date('Y-m-d H:i', strtotime('+5 days')),
            'thoi_gian_ket_thuc' => date('Y-m-d H:i', strtotime('+2 days')),
        ]);

        $this->assertTrue($validator->fails());
        $this->assertContains('Thời gian kết thúc phải sau thời gian bắt đầu.', $validator->errors()->get('thoi_gian_ket_thuc'));
    }

    public function test_passes_when_dates_are_valid(): void
    {
        $validator = $this->validateDates([
            'thoi_gian_bat_dau' => date('Y-m-d H:i', strtotime('+1 day')),
            'thoi_gian_ket_thuc' => date('Y-m-d H:i', strtotime('+2 days')),
        ]);

        $this->assertTrue($validator->passes());
    }

    public function test_fails_when_start_and_end_date_are_equal(): void
    {
        $validator = $this->validateDates([
            'thoi_gian_bat_dau' => date('Y-m-d H:i', strtotime('+1 day')),
            'thoi_gian_ket_thuc' => date('Y-m-d H:i', strtotime('+1 day')),
        ]);

        $this->assertTrue($validator->fails());
    }
}
