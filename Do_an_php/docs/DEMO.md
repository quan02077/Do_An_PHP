# DEMO – Kịch bản trình diễn và câu hỏi code (QQQ)

Bổ sung cho [`BAO_CAO_DEMO.md`](BAO_CAO_DEMO.md) (khung 40 phút). File này đi sâu vào phần demo ~13 phút
và phần hỏi đáp code, chia theo người phụ trách.

## 1. Chuẩn bị trước buổi demo (làm trước 30 phút)

```powershell
cd Do_an_php
php artisan migrate:fresh --seed   # CHỈ trên database demo — xóa sạch dữ liệu hiện có
npm run build
php artisan config:clear
```

Hai cửa sổ terminal (`.env`: `BROADCAST_CONNECTION=reverb`):

```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

```powershell
php artisan reverb:start --host=0.0.0.0 --port=8080
```

Không cần `queue:work` (event dùng `ShouldBroadcastNow`). Nếu Reverb chết giữa buổi, đặt/hủy vé vẫn chạy,
chỉ mất cập nhật realtime → nói rõ đó là thiết kế (`ShouldRescue`), rồi F5.

**Tài khoản seed** (mật khẩu đều là `123456`): Admin `admin@qqq.vn`; User `quan@gmail.com`, `nam@gmail.com`.

**Mở sẵn 3 cửa sổ trình duyệt:**
- A: Chrome thường, đăng nhập `quan@gmail.com`.
- B: Chrome ẩn danh, đăng nhập `nam@gmail.com`, mở cùng trang chi tiết sự kiện với A.
- C: Edge (hoặc profile khác), đăng nhập `admin@qqq.vn`, tab Đăng ký.

**Kiểm tra nhanh:** `php artisan test` (66 test đạt). Camera: chỉ chắc chạy trên `localhost`; qua IP Wi-Fi
bằng HTTP trình duyệt điện thoại sẽ chặn camera → nhập mã vé thủ công.
Thư viện quét QR `html5-qrcode` ghim bản 2.3.8 kèm SRI, tải từ unpkg nên vẫn cần Internet — thử trước buổi demo.

## 2. Kịch bản ~13 phút

| Phút | Ai làm | Thao tác | Nói gì / chứng minh gì |
|---|---|---|---|
| 0–2 | Quan | Trang chủ ở A: gõ từ khóa (danh sách tự đổi), chọn danh mục, trạng thái, sang trang 2 | Tìm kiếm Eloquent theo tên/mô tả/địa điểm/danh mục; AJAX debounce; `paginate()->withQueryString()` giữ bộ lọc |
| 2–3 | Quan | Thu nhỏ trình duyệt / DevTools 375 px | Responsive, không cuộn ngang |
| 3–4 | Quan | Bấm trái tim ở thẻ sự kiện | AJAX, toast, không reload; tắt JS vẫn chạy qua form |
| 4–6 | Quốc | Chi tiết sự kiện ở A và B cạnh nhau; A đặt vé → B thấy số chỗ đổi | Transaction + `lockForUpdate`; Reverb kênh `events.{id}` |
| 6–7 | Quốc | A mở "Vé của tôi" → "Xem vé & QR" | Mã vé `QQQ-YYYY-ID-RANDOM12`, QR sinh từ mã vé |
| 7–8 | Quốc | A hủy vé (AJAX) → B thấy số chỗ tăng lại | Hủy khóa dòng vé; không hủy được vé đã check-in |
| 8–10 | Quốc | C: quét QR (webcam) hoặc nhập mã vé của B; quét lại lần 2 | Check-in chỉ Admin; lần 2 báo "đã check-in"; bộ đếm realtime kênh private `admin.checkins` |
| 10–11 | Quốc | C: tab Đăng ký — vé đã hủy chỉ có nút Duyệt, vé đã tham gia không còn nút | Luồng trạng thái kiểm tra ở server (`registrationStatusTransitions`) |
| 11–12 | Quân | C: tạo danh mục, tạo sự kiện có lỗi validation, sửa, xóa sự kiện đã có vé | Form Request tiếng Việt; sự kiện có vé chuyển `da_huy` thay vì xóa |
| 12–13 | Quân | A gõ `/admin`; tab Thống kê ở C | Middleware `admin` chặn; Eloquent đếm theo quan hệ |

Ca dự phòng nếu bị hỏi "hai người đặt cùng lúc": chạy `bash tests/concurrency/run.sh` (1–2 phút, MySQL thật)
hoặc mở `tests/concurrency/README.md` để đọc kết quả.

## 3. Câu hỏi code dự kiến

### Quân – database, migration, model, seeder
| Câu hỏi | Gợi ý trả lời | Mở file |
|---|---|---|
| Các bảng liên kết thế nào? | `danh_muc` 1-n `su_kien`; `nguoi_dung` n-n `su_kien` qua `dang_ky` (vé) và `yeu_thich`. Khóa ngoại `restrict` | `database/migrations/*`, `docs/ERD.md` |
| Làm sao chặn một người đặt 2 vé một sự kiện? | Unique (`nguoi_dung_id`, `su_kien_id`) trong migration `dang_ky` + kiểm tra trong controller | `2026_09_28_125710_create_dang_kies_table.php:23` |
| Mã vé có trùng không? | `ma_ve` unique ở DB; mã chứa ID vé (luôn duy nhất) + 12 ký tự ngẫu nhiên | `EventController::bookTicket` |
| `belongsToMany` khác `hasMany` ra sao? | `hasMany(DangKy)` lấy bản ghi vé; `belongsToMany(SuKien, 'dang_ky')` đi xuyên bảng trung gian lấy thẳng sự kiện | `app/Models/NguoiDung.php:64,72` |
| Số chỗ đã đăng ký tính ở đâu? | Accessor `getSoLuongDaDangKyAttribute` đếm vé khác `da_huy` | `app/Models/SuKien.php:144` |
| Seeder tạo gì? | 1 admin, 2 user, 6 danh mục, 12 sự kiện (có sự kiện đầy chỗ, đã hủy), vé và yêu thích mẫu | `database/seeders/DatabaseSeeder.php` |
| Ảnh mẫu lấy từ đâu khi `uploads/` không nằm trong git? | Ảnh mẫu lưu trong git tại `database/seeders/images` (ngoại lệ duy nhất trong `.gitignore`); `copySampleImages()` chép sang `public/uploads/su_kien` mỗi lần seed. Ảnh admin tải lên đặt tên UUID nên không bị ghi đè và không bị commit | `DatabaseSeeder::copySampleImages`, `.gitignore` |
| Mật khẩu lưu thế nào? | Cột `mat_khau` cast `hashed` → bcrypt tự động khi gán | `app/Models/NguoiDung.php` |

### Quan – giao diện Blade
| Câu hỏi | Gợi ý trả lời | Mở file |
|---|---|---|
| Layout dùng chung thế nào? | `layouts/app.blade.php` có `@yield`; trang con `@extends` + `@section`; navbar/footer là `@include` partial | `resources/views/layouts/app.blade.php` |
| Vì sao `{{ }}` chứ không `{!! !!}`? | `{{ }}` escape HTML chống XSS; dữ liệu đưa vào JS dùng `Illuminate\Support\Js::from(...)` (mã hóa JSON an toàn, có test chống chèn `<script>`) | `admin/tabs/stats.blade.php:69` |
| AJAX yêu thích / hủy vé chạy ra sao? | JS chặn submit, `fetch` POST kèm `X-CSRF-TOKEN` + `Accept: application/json`; controller thấy `expectsJson()` thì trả JSON, không thì redirect | `partials/ajax_actions.blade.php`, `DashboardController` |
| Sau khi tìm kiếm AJAX, nút tim mới có chạy không? | Có: listener gắn trên `document` (event delegation) nên áp dụng cả phần tử được chèn sau | `partials/ajax_actions.blade.php` |
| Phân trang hiển thị bằng gì? | `$events->links('pagination::bootstrap-5')` | `user/index.blade.php:110` |
| Responsive xử lý ở đâu? | Grid Bootstrap + CSS bổ sung `< 576px` (bộ lọc cuộn ngang, `min-w-0`, `text-break`) | `public/css/style.css` |

### Quốc – auth, middleware, CRUD, validation, nghiệp vụ vé, tìm kiếm, test
| Câu hỏi | Gợi ý trả lời | Mở file |
|---|---|---|
| Middleware admin hoạt động thế nào? | Alias `admin` khai báo trong `bootstrap/app.php`; `handle()` kiểm tra `Auth::user()->vai_tro === 'admin'`, sai thì redirect; route admin bọc trong group `['auth.custom','admin']` | `app/Http/Middleware/AdminMiddleware.php`, `routes/web.php` |
| Vì sao dùng Form Request? | Tách validate khỏi controller, thông báo tiếng Việt tập trung, controller chỉ nhận `$request->validated()` (chống mass assignment) | `app/Http/Requests/EventRequest.php` |
| Hai người đặt chỗ cuối cùng cùng lúc thì sao? | `DB::transaction` + `SuKien::lockForUpdate()`: yêu cầu thứ 2 chờ khóa, đếm lại thấy hết chỗ. Đã thử trên MySQL thật: 30 người / 1 chỗ → đúng 1 vé; bỏ khóa → bán 20 vé / 5 chỗ | `EventController::bookTicket`, `tests/concurrency/README.md` |
| User hủy vé đúng lúc Admin quét QR? | `cancelTicket` khóa dòng vé trong transaction; check-in cũng khóa → một bên chờ, bên sau thấy trạng thái mới. Lỗi này do review phát hiện, tái hiện được trên MySQL trước khi sửa (kịch bản D) | `DashboardController::cancelTicket` |
| Đăng nhập an toàn ra sao? | `Auth::attempt` (so hash), `session()->regenerate()` chống session fixation, `throttle:5,1` chống dò mật khẩu, logout chỉ POST + CSRF | `AuthController.php`, `routes/web.php` |
| Quên mật khẩu? | `Password::sendResetLink` gửi link token dùng một lần (demo `MAIL_MAILER=log` → xem `storage/logs/laravel.log`) | `AuthController` |
| Tìm kiếm + phân trang viết thế nào? | Eloquent gom điều kiện trong `where(function ...)`: `like` theo tên, mô tả, địa điểm và `orWhereHas('danhMuc', ...)` theo tên danh mục; lọc danh mục bằng `danh_muc_id`; cuối cùng `paginate(12)->withQueryString()` | `HomeController.php:33` |
| Luồng trạng thái vé? | Bảng chuyển hợp lệ trong `registrationStatusTransitions()`; khôi phục vé hủy chỉ khi còn chỗ; `da_tham_gia` là cuối | `AdminController.php` |
| Realtime làm sao? | Event `TicketCountsChanged` (kênh công khai `events.{id}`) và `TicketCheckedIn` (kênh private `admin.checkins`, `routes/channels.php` chỉ cho admin). `ShouldDispatchAfterCommit` → chỉ phát khi transaction lưu thành công; Laravel Echo nghe ở `resources/js/app.js` | `app/Events/*`, `routes/channels.php` |
| Test chạy thế nào? | `php artisan test`: 66 test trên SQLite in-memory (`RefreshDatabase`); riêng khóa đồng thời phải kiểm trên MySQL bằng `tests/concurrency/run.sh` | `tests/Feature/*` |
| Xóa sự kiện đã có vé? | Không xóa (vi phạm khóa ngoại/mất lịch sử) mà chuyển `da_huy` | `EventController::destroy` |
