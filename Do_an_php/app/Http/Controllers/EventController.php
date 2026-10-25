<?php

namespace App\Http\Controllers;

use App\Models\DangKy;
use App\Models\SuKien;
use App\Models\YeuThich;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EventController extends Controller
{
    protected function getCurrentUser()
    {
        return Auth::user() ?? session('user');
    }
    /**
     * Lưu sự kiện mới vào CSDL (Resource store)
     */
    public function store(StoreEventRequest $request)
    {
        $user = $this->getCurrentUser();
        if (!$user || $user->vai_tro !== 'admin') {
            return redirect()->route('Home.index')->with('warning', 'Bạn không có quyền tạo sự kiện.');
        }

        $data = $request->validated();
        $data['nguoi_tao_id'] = $user->id;
        $data['trang_thai'] = $data['trang_thai'] ?? 'cong_khai';
        $data['slug'] = $this->makeUniqueSlug($data['ten_su_kien']);
        $data = $this->saveUploadedImage($request, $data);

        SuKien::create($data);

        return redirect()->route('admin.index', ['tab' => 'events'])
            ->with('success', 'Đã tạo sự kiện mới.');
    }

    /**
     * Chi tiết sự kiện (Resource show)
     */
    public function show($id)
    {
        $event = SuKien::with(['danhMuc', 'nguoiTao', 'dangKys'])->findOrFail($id);
        $user = $this->getCurrentUser();

        $isFavorited = false;
        $userTicket = null;

        if ($user) {
            $isFavorited = YeuThich::where('nguoi_dung_id', $user->id)
                ->where('su_kien_id', $id)
                ->exists();

            $userTicket = DangKy::where('nguoi_dung_id', $user->id)
                ->where('su_kien_id', $id)
                ->first();
        }

        return view('user.event_detail', compact('event', 'user', 'isFavorited', 'userTicket'));
    }

    /**
     * Cập nhật thông tin sự kiện (Resource update)
     */
    public function update(UpdateEventRequest $request, $id)
    {
        $user = $this->getCurrentUser();
        if (!$user || $user->vai_tro !== 'admin') {
            return redirect()->route('Home.index')->with('warning', 'Bạn không có quyền cập nhật sự kiện.');
        }

        $event = SuKien::findOrFail($id);
        $data = $request->validated();

        if (isset($data['ten_su_kien']) && $data['ten_su_kien'] !== $event->ten_su_kien) {
            $data['slug'] = $this->makeUniqueSlug($data['ten_su_kien'], $event->id);
        }
        $data = $this->saveUploadedImage($request, $data);

        $event->update($data);

        return redirect()->route('admin.index', ['tab' => 'events'])
            ->with('success', 'Đã cập nhật sự kiện.');
    }

    /**
     * Xóa sự kiện (Resource destroy)
     */
    public function destroy($id)
    {
        $user = $this->getCurrentUser();
        if (!$user || $user->vai_tro !== 'admin') {
            return redirect()->route('Home.index')->with('warning', 'Bạn không có quyền xóa sự kiện.');
        }

        $event = SuKien::findOrFail($id);
        $hasRegistrations = $event->dangKys()->exists();
        $event->delete();

        return redirect()->route('admin.index', ['tab' => 'events'])->with(
            $hasRegistrations ? 'warning' : 'success',
            $hasRegistrations
                ? 'Sự kiện đã có người đăng ký nên không bị xóa; hệ thống đã chuyển sự kiện sang trạng thái đã hủy.'
                : 'Đã xóa sự kiện.'
        );
    }

    /**
     * Đặt vé / Đăng ký tham gia sự kiện: Lưu vào bảng dang_ky trong CSDL
     */
    public function bookTicket(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->route('Auth.index')->with('warning', 'Vui lòng đăng nhập để đăng ký vé.');
        }

        $event = SuKien::findOrFail($id);

        // Kiểm tra sự kiện có bị hủy không
        if ($event->trang_thai === 'da_huy') {
            return back()->with('warning', 'Sự kiện này đã bị hủy, không thể đăng ký vé.');
        }

        if ($event->trang_thai !== 'cong_khai' || $event->thoi_gian_bat_dau <= now()) {
            return back()->with('warning', 'Sự kiện này hiện không còn nhận đăng ký.');
        }

        // Kiểm tra tình trạng còn vé
        if ($event->so_luong_toi_da > 0 && $event->so_luong_da_dang_ky >= $event->so_luong_toi_da) {
            return back()->with('warning', 'Sự kiện này đã hết vé!');
        }

        // Kiểm tra xem đã từng đăng ký chưa
        $existingTicket = DangKy::where('nguoi_dung_id', $user->id)
            ->where('su_kien_id', $id)
            ->first();

        if ($existingTicket) {
            if ($existingTicket->trang_thai === 'da_huy') {
                $existingTicket->trang_thai = 'da_xac_nhan';
                $existingTicket->thoi_gian_dang_ky = now();
                $existingTicket->save();

                return redirect()->route('Dashboard.myTicket')->with('success', 'Bạn đã kích hoạt lại vé thành công! Mã vé: ' . $existingTicket->ma_ve);
            }

            return redirect()->route('Dashboard.myTicket')->with('warning', 'Bạn đã đăng ký vé cho sự kiện này rồi! Mã vé: ' . $existingTicket->ma_ve);
        }

        // Tạo mã vé duy nhất
        $maVe = 'QQQ-' . date('Y') . '-' . strtoupper(Str::random(6));

        $ticket = DangKy::create([
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $id,
            'ma_ve' => $maVe,
            'thoi_gian_dang_ky' => now(),
            'trang_thai' => 'da_xac_nhan',
            'ghi_chu' => 'Đăng ký vé trực tuyến trên website',
        ]);

        return redirect()->route('Dashboard.myTicket')->with('success', 'Chúc mừng! Đăng ký vé thành công! Mã vé của bạn là ' . $maVe);
    }

    /** Tạo slug không trùng để dùng cho URL và báo cáo. */
    private function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'su-kien';
        $slug = $base;
        $suffix = 2;

        while (SuKien::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    /** Lưu ảnh được chọn từ máy tính; ảnh tải lên được ưu tiên hơn link ảnh. */
    private function saveUploadedImage(Request $request, array $data): array
    {
        unset($data['hinh_anh_tai_len']);

        if (!$request->hasFile('hinh_anh_tai_len')) {
            if (!empty($data['hinh_anh'])) {
                $filename = basename($data['hinh_anh']);
                $path = public_path('uploads/su_kien/' . $filename);

                if (File::isFile($path)) {
                    $data['hinh_anh'] = $filename;
                } else {
                    unset($data['hinh_anh']);
                }
            }

            return $data;
        }

        $image = $request->file('hinh_anh_tai_len');
        $directory = public_path('uploads/su_kien');
        File::ensureDirectoryExists($directory);

        $filename = Str::uuid() . '.' . $image->extension();
        $image->move($directory, $filename);
        $data['hinh_anh'] = $filename;

        return $data;
    }
}
