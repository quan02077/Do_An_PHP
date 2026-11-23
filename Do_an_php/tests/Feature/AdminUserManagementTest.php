<?php

namespace Tests\Feature;

use App\Models\NguoiDung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(array $overrides = []): NguoiDung
    {
        return NguoiDung::factory()->create(array_merge([
            'vai_tro' => 'admin',
            'trang_thai' => 'hoat_dong',
            'mat_khau' => Hash::make('secret123'),
        ], $overrides));
    }

    public function test_guest_and_regular_user_cannot_access_admin_user_routes(): void
    {
        $target = NguoiDung::factory()->create(['vai_tro' => 'user']);
        $regularUser = NguoiDung::factory()->create(['vai_tro' => 'user']);

        $this->put(route('admin.users.status', $target->id), ['trang_thai' => 'bi_khoa'])
            ->assertRedirect(route('Auth.index'));
        $this->put(route('admin.users.role', $target->id), ['vai_tro' => 'admin'])
            ->assertRedirect(route('Auth.index'));

        $this->actingAs($regularUser)
            ->put(route('admin.users.status', $target->id), ['trang_thai' => 'bi_khoa'])
            ->assertRedirect(route('Home.index'));
        $this->actingAs($regularUser)
            ->put(route('admin.users.role', $target->id), ['vai_tro' => 'admin'])
            ->assertRedirect(route('Home.index'));

        $this->assertSame('hoat_dong', $target->fresh()->trang_thai);
        $this->assertSame('user', $target->fresh()->vai_tro);
    }

    public function test_admin_can_view_search_and_paginate_users_with_query_string(): void
    {
        $admin = $this->createAdmin(['email' => 'boss@qqq.vn']);
        $target = NguoiDung::factory()->create([
            'ho_ten' => 'Nguyễn Tìm Kiếm',
            'email' => 'timkiem.unique@example.test',
            'vai_tro' => 'user',
            'trang_thai' => 'hoat_dong',
        ]);
        NguoiDung::factory()->count(12)->create(['vai_tro' => 'user', 'trang_thai' => 'hoat_dong']);

        $this->actingAs($admin)
            ->get(route('admin.index', ['tab' => 'users', 'user_search' => 'timkiem.unique@example.test']))
            ->assertOk()
            ->assertSee($target->ho_ten)
            ->assertSee($target->email);

        $this->actingAs($admin)
            ->get(route('admin.index', ['tab' => 'users', 'user_role' => 'user', 'users_page' => 1]))
            ->assertOk()
            ->assertSee('users_page=2')
            ->assertSee('user_role=user');
    }

    public function test_locked_user_cannot_log_in(): void
    {
        $admin = $this->createAdmin();
        $member = NguoiDung::factory()->create([
            'mat_khau' => Hash::make('secret123'),
            'trang_thai' => 'hoat_dong',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.index', ['tab' => 'users']))
            ->put(route('admin.users.status', $member->id), ['trang_thai' => 'bi_khoa'])
            ->assertRedirect();

        $this->assertSame('bi_khoa', $member->fresh()->trang_thai);

        auth()->logout();

        $this->from('/auth')
            ->post('/login', ['email' => $member->email, 'password' => 'secret123'])
            ->assertRedirect('/auth')
            ->assertSessionHasErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
            ]);

        $this->assertGuest();
    }

    public function test_logged_in_user_who_gets_locked_is_logged_out_on_next_request(): void
    {
        $member = NguoiDung::factory()->create(['trang_thai' => 'hoat_dong']);

        $this->actingAs($member)->get('/profile')->assertOk();

        $member->update(['trang_thai' => 'bi_khoa']);

        $this->actingAs($member->fresh())
            ->get('/profile')
            ->assertRedirect(route('Auth.index'))
            ->assertSessionHas('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.');

        $this->assertGuest();
    }

    public function test_unlocked_user_can_log_in_again(): void
    {
        $admin = $this->createAdmin();
        $member = NguoiDung::factory()->create([
            'mat_khau' => Hash::make('secret123'),
            'trang_thai' => 'bi_khoa',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.index', ['tab' => 'users']))
            ->put(route('admin.users.status', $member->id), ['trang_thai' => 'hoat_dong'])
            ->assertRedirect();

        $this->assertSame('hoat_dong', $member->fresh()->trang_thai);

        auth()->logout();

        $this->post('/login', ['email' => $member->email, 'password' => 'secret123'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($member->fresh());
    }

    public function test_admin_cannot_lock_or_demote_self(): void
    {
        $admin = $this->createAdmin();
        $this->createAdmin();

        $this->actingAs($admin)
            ->from(route('admin.index', ['tab' => 'users']))
            ->put(route('admin.users.status', $admin->id), ['trang_thai' => 'bi_khoa'])
            ->assertRedirect()
            ->assertSessionHas('warning');

        $this->actingAs($admin)
            ->from(route('admin.index', ['tab' => 'users']))
            ->put(route('admin.users.role', $admin->id), ['vai_tro' => 'user'])
            ->assertRedirect()
            ->assertSessionHas('warning');

        $this->assertSame('hoat_dong', $admin->fresh()->trang_thai);
        $this->assertSame('admin', $admin->fresh()->vai_tro);
    }

    public function test_cannot_lock_or_demote_last_active_admin(): void
    {
        $onlyActiveAdmin = $this->createAdmin();
        // Admin thứ hai đang bị khóa sẵn, dùng actingAs để kiểm tra trường hợp khóa/hạ quyền admin hoạt động duy nhất
        $lockedAdmin = $this->createAdmin(['trang_thai' => 'bi_khoa']);

        // Khóa chính mình đã bị chặn; ở đây tạo admin hoạt động thứ 2 rồi khóa thành 1 để chắc chắn
        $adminA = $onlyActiveAdmin;
        $this->actingAs($adminA)
            ->from(route('admin.index', ['tab' => 'users']))
            ->put(route('admin.users.status', $adminA->id), ['trang_thai' => 'bi_khoa'])
            ->assertSessionHas('warning');

        // Kiểm tra trường hợp 1 admin đang hoạt động duy nhất không thể bị khóa/hạ quyền bởi bất kỳ admin nào khác
        $adminB = $this->createAdmin(['trang_thai' => 'hoat_dong']);
        $this->actingAs($adminB)
            ->put(route('admin.users.status', $adminA->id), ['trang_thai' => 'bi_khoa']);
        $this->assertSame('bi_khoa', $adminA->fresh()->trang_thai);

        // Giờ chỉ còn $adminB là admin hoạt động duy nhất; kể cả khi $lockedAdmin (chỉ là record khác) gọi,
        // nếu cập nhật $adminB về bi_khoa hoặc user đều phải bị chặn
        $this->assertSame(1, NguoiDung::where('vai_tro', 'admin')->where('trang_thai', 'hoat_dong')->count());

        // Mở khóa $adminA, hạ quyền $adminA -> còn 1 active admin ($adminB); $adminB tự hạ quyền bị chặn
        $this->actingAs($adminB)
            ->put(route('admin.users.role', $adminB->id), ['vai_tro' => 'user'])
            ->assertSessionHas('warning');
        $this->assertSame('admin', $adminB->fresh()->vai_tro);
        $this->assertSame('bi_khoa', $lockedAdmin->fresh()->trang_thai);
    }

    public function test_promoting_user_to_admin_grants_admin_access(): void
    {
        $admin = $this->createAdmin();
        $member = NguoiDung::factory()->create(['vai_tro' => 'user', 'trang_thai' => 'hoat_dong']);

        $this->actingAs($admin)
            ->put(route('admin.users.role', $member->id), ['vai_tro' => 'admin'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('admin', $member->fresh()->vai_tro);

        $this->actingAs($member->fresh())
            ->get(route('admin.index'))
            ->assertOk();
    }

    public function test_invalid_status_or_role_returns_validation_error(): void
    {
        $admin = $this->createAdmin();
        $member = NguoiDung::factory()->create(['vai_tro' => 'user']);

        $this->actingAs($admin)
            ->from(route('admin.index', ['tab' => 'users']))
            ->put(route('admin.users.status', $member->id), ['trang_thai' => 'da_xoa'])
            ->assertSessionHasErrors('trang_thai');

        $this->actingAs($admin)
            ->from(route('admin.index', ['tab' => 'users']))
            ->put(route('admin.users.role', $member->id), ['vai_tro' => 'superadmin'])
            ->assertSessionHasErrors('vai_tro');
    }

    public function test_forgot_password_for_locked_account_returns_generic_status_without_sending_link(): void
    {
        Notification::fake();

        $locked = NguoiDung::factory()->create(['trang_thai' => 'bi_khoa']);

        $this->from('/auth?mode=forgot')
            ->post('/forgot-password', ['email' => $locked->email])
            ->assertRedirect('/auth?mode=forgot')
            ->assertSessionHas('status');

        Notification::assertNothingSent();
    }
}
