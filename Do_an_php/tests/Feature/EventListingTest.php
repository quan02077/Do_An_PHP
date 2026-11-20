<?php

namespace Tests\Feature;

use App\Models\DanhMuc;
use App\Models\SuKien;
use App\Models\NguoiDung;
use App\Models\DangKy;
use Tests\TestCase;

class EventListingTest extends TestCase
{
    public function test_listing_paginates_at_twelve_and_keeps_filters(): void
    {
        $category = DanhMuc::factory()->create();
        SuKien::factory()->count(13)->create([
            'danh_muc_id' => $category->id,
            'ten_su_kien' => 'Hoi thao Laravel',
            'trang_thai' => 'cong_khai',
        ]);
        SuKien::factory()->create(['ten_su_kien' => 'Buoi nhac khac']);

        $query = ['search' => 'Laravel', 'category' => $category->id, 'status' => 'sap_dien_ra'];
        $first = $this->get('/?'.http_build_query($query));
        $first->assertOk()->assertSee('Hiển thị 12 / 13 sự kiện')
            ->assertSee('page=2', false)->assertSee('search=Laravel', false)
            ->assertSee('category='.$category->id, false)->assertSee('status=sap_dien_ra', false);

        $this->get('/?'.http_build_query($query + ['page' => 2]))
            ->assertOk()->assertSee('Hiển thị 1 / 13 sự kiện');
    }

    public function test_draft_event_is_absent_from_search_results(): void
    {
        SuKien::factory()->create(['ten_su_kien' => 'Bí mật Laravel', 'trang_thai' => 'nhap']);
        $this->get('/?search=Laravel')->assertOk()->assertSee('Hiển thị 0 / 0 sự kiện');
    }

    public function test_search_uses_description_and_category_name(): void
    {
        $category = DanhMuc::factory()->create(['ten_danh_muc' => 'Công nghệ']);
        SuKien::factory()->create(['danh_muc_id' => $category->id, 'ten_su_kien' => 'Sự kiện A']);
        SuKien::factory()->create(['mo_ta' => 'Chủ đề bảo mật', 'ten_su_kien' => 'Sự kiện B']);
        $this->get('/?search=Công%20nghệ')->assertOk()->assertSee('Hiển thị 1 / 1 sự kiện');
        $this->get('/?search=bảo%20mật')->assertOk()->assertSee('Hiển thị 1 / 1 sự kiện');
    }

    public function test_admin_events_are_searchable_and_paginated(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        SuKien::factory()->count(11)->create(['mo_ta' => 'Chủ đề Laravel']);
        $response = $this->actingAs($admin)->get('/admin?tab=events&event_search=Laravel');
        $response->assertOk()->assertSee('events_page=2', false)->assertSee('event_search=Laravel', false);
        $this->actingAs($admin)->get('/admin?tab=events&event_search=Laravel&events_page=2')->assertOk();
    }

    public function test_admin_categories_and_registrations_are_paginated(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        DanhMuc::factory()->count(11)->create();
        $this->actingAs($admin)->get('/admin?tab=categories')
            ->assertOk()->assertSee('categories_page=2', false);
        $this->actingAs($admin)->get('/admin?tab=categories&categories_page=2')->assertOk();
        DangKy::factory()->count(11)->create();
        $this->actingAs($admin)->get('/admin?tab=registrations')
            ->assertOk()->assertSee('registrations_page=2', false);
    }
}
