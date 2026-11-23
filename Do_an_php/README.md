# EVENTVN — HỆ THỐNG QUẢN TRỊ & ĐẶT VÉ SỰ KIỆN TRỰC TUYẾN

**Đồ án môn học: Lập trình Web với PHP & Laravel**  
**Trường Đại học Công Thương TP.HCM (HUIT) — Khoa Công nghệ Thông tin**

---

## 1. Giới thiệu Đề tài
**EventVN** là nền tảng trực tuyến kết nối ban tổ chức sự kiện với người tham dự. Hệ thống giải quyết trọn vẹn bài toán từ khâu xuất bản, phân loại danh mục, quảng bá sự kiện, quản lý số lượng chỗ ngồi, đặt vé trực tuyến tự động sinh mã vé duy nhất, đến phân hệ duyệt vé và kiểm soát dữ liệu chuyên nghiệp dành cho Quản trị viên (Admin).

---

## 2. Thông tin Nhóm Sinh viên & Bảng phân công nhiệm vụ
*(Căn cứ theo Bảng phân công công việc chi tiết PCCV_EventVN.xlsx)*

| STT | Họ và tên | Công việc phân công chi tiết | Kết quả bàn giao (Deliverable) | Tiến độ |
|:---:|:---|:---|:---|:---:|
| **1** | **Nguyễn Nhật Minh Quân** | • Cài Laragon, PHP/Composer; tạo database `quan_ly_su_kien`; cấu hình `.env.example`<br>• Hoàn thiện migration tiếng Việt: `nguoi_dung`, `danh_muc`, `su_kien`, `dang_ky`, `yeu_thich`; khóa ngoại và unique<br>• Khai báo Model Eloquent, `$fillable`, casts và quan hệ 1-n, n-n<br>• Tạo Seeder/Factory: admin, user, danh mục, sự kiện, đăng ký và yêu thích mẫu<br>• Cài rule xóa: chặn xóa danh mục còn event; event có đăng ký chỉ được chuyển đã hủy | • Laravel kết nối MySQL và chạy trang chủ<br>• `php artisan migrate:fresh --seed` chạy không lỗi<br>• Model truy xuất đúng quan hệ<br>• Dữ liệu demo đủ để trình bày<br>• Ràng buộc xóa đúng nghiệp vụ | **Hoàn thành** |
| **2** | **Lương Văn Quan** | • Lập bảng map HTML mockup sang layout, partial, component và Blade view<br>• Tạo `layouts/app`, navbar, footer, alert và input component bằng Blade + Bootstrap<br>• Chuyển trang chủ, danh sách và chi tiết sự kiện sang Blade responsive<br>• Chuyển Vé của tôi, Yêu thích và Hồ sơ sang Blade<br>• Chụp ảnh màn hình chức năng; review responsive và thống nhất UI trước demo | • Danh sách chuyển đổi view rõ ràng<br>• Layout dùng chung cho toàn site<br>• Guest xem danh sách và chi tiết event<br>• Giao diện user hoàn chỉnh<br>• Bộ ảnh báo cáo và UI đã QA | **Hoàn thành** |
| **3** | **Trịnh Hữu Kiến Quốc** | • Đăng ký, đăng nhập, đăng xuất với `NguoiDung`, Hash và Laravel Auth<br>• Tạo middleware `role:admin`; bảo vệ route và hiển thị quyền trong Blade<br>• CRUD đầy đủ `danh_muc` và `su_kien`: Resource Controller, Form Request, validate, upload poster tùy chọn<br>• Tìm kiếm, lọc danh mục/trạng thái, phân trang Bootstrap với `withQueryString`<br>• Đăng ký vé phía server: chặn trùng, hết chỗ, event chưa công khai/đã bắt đầu<br>• Vé của tôi, hủy vé, giữ trạng thái cancelled và cho phép đăng ký lại<br>• Test rule chính, README, ERD, Use Case, phân công, script demo và freeze tính năng | • User/Admin đăng nhập được<br>• User không truy cập được `/admin`<br>• Admin tạo/xem/sửa/xóa đúng hai bảng<br>• Danh sách có search/filter/pagination<br>• Đăng ký vé đúng quy tắc nghiệp vụ<br>• User quản lý vé của mình<br>• Bản demo ổn định và hồ sơ hoàn chỉnh | **Hoàn thành** |

**Lưu ý tiến độ:** Cột “Hoàn thành” phản ánh bảng phân công ban đầu, không thay cho biên bản nghiệm thu. Bộ ảnh chụp màn hình và kiểm tra responsive trên thiết bị thật vẫn cần nhóm bổ sung trước khi nộp.

---

## 3. Danh sách Chức năng Hệ thống

### Phân hệ Khách & Người dùng (User / Public)
1. **Trang chủ & Khám phá sự kiện:**
   * Tìm kiếm bằng truy vấn Eloquent theo tên sự kiện, mô tả, địa điểm và tên danh mục.
   * Lọc theo danh mục thể loại (Âm nhạc, Công nghệ, Thể thao, Ẩm thực, Hội thảo...).
   * Lọc theo 4 trạng thái sự kiện: *Sắp diễn ra, Đang diễn ra, Đã kết thúc, Sự kiện yêu thích*.
   * Phân trang 12 sự kiện/trang bằng `paginate()->withQueryString()`; giữ từ khóa và bộ lọc khi đổi trang.
   * Ô tìm kiếm cập nhật danh sách bằng AJAX sau khi ngừng gõ; biểu mẫu GET vẫn hoạt động khi JavaScript tắt.
   * Trang chi tiết cập nhật số chỗ qua Laravel Reverb khi có lượt đặt hoặc hủy vé, không cần tải lại trang.
2. **Xem chi tiết sự kiện:**
   * Hiển thị đầy đủ banner poster, đơn vị tổ chức, thời gian bắt đầu/kết thúc, giá vé.
   * Thanh tiến độ (Progress bar) trực quan thể hiện tỷ lệ số chỗ đã đăng ký / quy mô tối đa.
   * **Bản đồ địa điểm:** Nhúng iframe Google Maps theo địa điểm tổ chức; không dùng API key hoặc gọi Google Maps API từ server.
   * Danh sách các sự kiện liên quan cùng thể loại.
3. **Đăng ký & Đặt vé sự kiện (Core Feature):**
   * Kiểm tra điều kiện ngặt nghèo: sự kiện đã hủy, sự kiện đã qua thời gian bắt đầu, sự kiện đã hết chỗ.
   * Chống trùng lặp: mỗi người dùng chỉ được đăng ký 1 vé cho mỗi sự kiện.
   * Tự động sinh mã vé `QQQ-YYYY-ID-RANDOM12`; ID giúp bảo đảm mã không trùng, phần ngẫu nhiên khiến mã khó đoán.
4. **Quản lý Vé của tôi & Vé điện tử QR MoMo (E-Ticket):**
   * Danh sách toàn bộ vé đã đăng ký kèm mã vé, ngày đăng ký, trạng thái duyệt.
   * **Vé điện tử phong cách MoMo / Ticketbox:** Tự động sinh mã QR động trực tiếp từ mã vé định danh, giao diện cuống vé răng cưa chuẩn thực tế, tích hợp nút **In / Tải vé PDF** (`window.print`).
   * Chức năng **Hủy vé an toàn**: cập nhật trạng thái `da_huy`, bảo lưu lịch sử giao dịch và cho phép đăng ký lại khi cần. Không hủy được vé đã check-in (`da_tham_gia`) hoặc khi sự kiện đã bắt đầu.
   * Hủy vé bằng AJAX: badge và nút đổi ngay, hiện thông báo toast, không tải lại trang; khi tắt JavaScript vẫn hủy được qua form thường.
5. **Sự kiện yêu thích (Bookmark):**
   * Nút tim lưu yêu thích tại tất cả các thẻ sự kiện; bấm bằng AJAX (không tải lại trang), controller trả JSON khi request AJAX và vẫn redirect khi tắt JavaScript.
   * Trang quản lý danh sách yêu thích riêng cho từng tài khoản.
6. **Hồ sơ cá nhân & Bảo mật:**
   * Xem và chỉnh sửa thông tin cá nhân (họ tên, email, số điện thoại, ngày sinh, địa chỉ, tiểu sử).
   * Đổi mật khẩu bằng `Hash::check` và lưu hash mới; quên mật khẩu dùng liên kết email có token hết hạn.

### Phân hệ Quản trị viên (Admin Portal)
1. **Bảng điều khiển Thống kê & Biểu đồ trực quan (Dashboard Tab):**
   * Thẻ đo lường tức thời: Tổng số sự kiện, sự kiện sắp diễn ra, tổng số vé đã đặt, tổng số thành viên.
   * **Trực quan hóa dữ liệu (Chart.js):**
     * **Biểu đồ tròn (Doughnut Chart):** Phân bổ sự kiện theo từng thể loại danh mục.
     * **Biểu đồ cột (Bar Chart):** Top các sự kiện thu hút số lượng lượt đăng ký vé cao nhất.
2. **Quản lý Sự kiện (Events Tab - CRUD Modal):**
   * Danh sách bảng sự kiện trực quan có hình ảnh, danh mục, thời gian, số lượng vé và trạng thái.
   * Tìm kiếm theo tên, mô tả, địa điểm, danh mục và phân trang 10 sự kiện/trang. Danh mục, lượt đăng ký cũng phân trang 10 dòng/trang.
   * **Thêm mới sự kiện:** Popup modal hỗ trợ upload ảnh poster trực tiếp từ máy tính hoặc chọn ảnh thumbnail có sẵn.
   * **Chỉnh sửa sự kiện:** Đổ dữ liệu tự động vào form modal qua Javascript Data Attributes.
   * **Xóa sự kiện an toàn:** Nếu sự kiện đã có người đặt vé, hệ thống tự động chuyển trạng thái sang `da_huy` để tránh lỗi vi phạm khóa ngoại MySQL.
3. **Quản lý Danh mục (Categories Tab - CRUD Modal):**
   * Quản lý phân loại sự kiện (Tên danh mục, mô tả, trạng thái hoạt động / tạm dừng).
   * Quy tắc nghiệp vụ chặn xóa danh mục khi đang có sự kiện trực thuộc.
4. **Duyệt đăng ký vé & Quét mã QR Check-in (Registrations Tab):**
   * Theo dõi danh sách tất cả các lượt đăng ký vé của người dùng trong hệ thống.
   * **Soát vé & Check-in QR trực tiếp:**
     * Tích hợp máy quét Camera/Webcam chuẩn HTML5: Quét trực tiếp mã QR trên điện thoại của khách tại cổng sự kiện.
     * Ô nhập nhanh mã vé hỗ trợ soát vé thủ công.
     * Gửi yêu cầu đến `POST /admin/check-in` có middleware đăng nhập và Admin, cập nhật trạng thái `da_tham_gia` và cảnh báo vé đã check-in.
   * Bộ đếm và tên người vừa check-in cập nhật trên các màn Admin đang mở qua kênh Reverb riêng cho Admin.
   * Thao tác 1 chạm: **Duyệt** vé (`da_xac_nhan`), **Check-in** vé (`da_tham_gia`) hoặc **Hủy** vé (`da_huy`).
   * Luồng trạng thái được kiểm tra ở server, giao diện chỉ hiện nút hợp lệ: `cho_duyet → da_xac_nhan | da_huy`; `da_xac_nhan → da_tham_gia | da_huy`; `da_huy → da_xac_nhan` chỉ khi sự kiện còn chỗ, còn công khai và chưa bắt đầu; `da_tham_gia` là trạng thái cuối.
5. **Cơ chế Phân quyền (Security & Middleware):**
   * Middleware `AuthenticateCustom` (`auth.custom`): Chặn khách vãng lai truy cập vào các trang cá nhân và đặt vé.
   * Middleware `AdminMiddleware` (`admin`): Kiểm tra quyền hạn `vai_tro === 'admin'`, chặn người dùng thường truy cập trái phép vào URL `/admin`.

---

## 4. Sơ đồ Cơ sở dữ liệu (Database Schema)

Xem sơ đồ quan hệ trong [`docs/ERD.md`](docs/ERD.md), sơ đồ chức năng trong [`docs/UseCase.puml`](docs/UseCase.puml) và khung thuyết trình ở [`docs/BAO_CAO_DEMO.md`](docs/BAO_CAO_DEMO.md). Bảng phân công ở mục 2; cần chụp ảnh màn hình từ bản demo chạy trên máy nhóm trước khi nộp báo cáo.

Cơ sở dữ liệu gồm 5 bảng chính được thiết kế chuẩn hóa quan hệ (Database: `quan_ly_su_kien`):

```text
  ┌──────────────┐          1 : N          ┌──────────────┐
  │   danh_muc   │ ─────────────────────── │   su_kien    │
  └──────────────┘                         └──────────────┘
                                                  │ 1
                                                  │
                                                  │ N
  ┌──────────────┐          1 : N          ┌──────────────┐
  │  nguoi_dung  │ ─────────────────────── │   dang_ky    │
  └──────────────┘                         └──────────────┘
         │ 1
         │
         │ N
  ┌──────────────┐
  │  yeu_thich   │ ──── (NguoiDung - SuKien)
  └──────────────┘
```

* **`nguoi_dung`**: Lưu thông tin tài khoản, vai trò (`admin` / `user`), mật khẩu mã hóa Bcrypt.
* **`danh_muc`**: Phân loại sự kiện (triển lãm, âm nhạc, công nghệ...), trạng thái hoạt động.
* **`su_kien`**: Tên sự kiện, slug URL, ngày giờ bắt đầu/kết thúc, địa điểm, quy mô vé, giá vé, hình ảnh, trạng thái.
* **`dang_ky`**: Khóa ngoại liên kết `nguoi_dung_id` và `su_kien_id`, mã vé độc nhất `ma_ve`, trạng thái vé (`cho_duyet`, `da_xac_nhan`, `da_tham_gia`, `da_huy`).
* **`yeu_thich`**: Bảng quan hệ N - N lưu trữ các sự kiện được người dùng đánh dấu yêu thích.

---

## 5. Cấu trúc Thư mục Dự án

```text
Do_an_php/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php         # Bảng điều khiển admin, CRUD danh mục, duyệt vé
│   │   │   ├── AuthController.php          # Đăng nhập, đăng ký, đăng xuất, quên mật khẩu
│   │   │   ├── DashboardController.php     # Vé của tôi, yêu thích, hồ sơ cá nhân
│   │   │   ├── EventController.php         # Chi tiết sự kiện, đặt vé, CRUD sự kiện admin
│   │   │   └── HomeController.php          # Trang chủ, tìm kiếm, lọc & phân trang
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php         # Gác cổng phân quyền vai trò admin
│   │   │   └── AuthenticateCustom.php      # Gác cổng kiểm tra đăng nhập
│   │   └── Requests/
│   │       ├── EventRequest.php            # Validate form tạo và sửa sự kiện dùng chung
│   │       ├── ChangePasswordRequest.php   # Validate đổi mật khẩu
│   │       └── UpdateProfileRequest.php    # Validate cập nhật thông tin cá nhân
│   └── Models/
│       ├── DangKy.php                      # Model Lượt đăng ký vé
│       ├── DanhMuc.php                     # Model Danh mục sự kiện
│       ├── NguoiDung.php                   # Model Người dùng / Tài khoản
│       ├── SuKien.php                      # Model Sự kiện
│       └── YeuThich.php                    # Model Sự kiện yêu thích
├── database/
│   ├── migrations/                         # 5 file migration tiếng Việt chuẩn
│   └── seeders/
│       └── DatabaseSeeder.php              # Nạp dữ liệu demo 1 Admin, 2 User, 12 Sự kiện
├── public/
│   ├── css/style.css                       # Tùy biến giao diện Bootstrap
│   └── uploads/su_kien/                    # Thư mục lưu trữ hình ảnh sự kiện
├── resources/
│   └── views/
│       ├── admin/                          # GIAO DIỆN QUẢN TRỊ ADMIN
│       │   ├── tabs/
│       │   │   ├── stats.blade.php         # Tab Thống kê số liệu
│       │   │   ├── events.blade.php        # Tab Bảng danh sách sự kiện
│       │   │   ├── registrations.blade.php # Tab Bảng duyệt đăng ký vé
│       │   │   └── categories.blade.php    # Tab Bảng danh mục sự kiện
│       │   ├── admin.blade.php             # Khung sườn tổng quan Admin
│       │   └── modals.blade.php            # Toàn bộ modal Thêm / Sửa sự kiện & danh mục
│       ├── auth/
│       │   └── auth.blade.php              # Trang Đăng nhập & Đăng ký
│       ├── layouts/
│       │   └── app.blade.php               # Bố cục Master Layout dùng chung
│       ├── partials/
│       │   ├── alerts.blade.php            # Khối hiển thị Flash Message tự động
│       │   ├── breadcrumb.blade.php        # Điều hướng phân cấp Breadcrumb
│       │   ├── footer.blade.php            # Chân trang Web
│       │   └── nav.blade.php               # Thanh điều hướng Navbar
│       └── user/                           # GIAO DIỆN NGƯI DÙNG & CÔNG KHAI
│           ├── card_event.blade.php        # Component Thẻ sự kiện dùng chung
│           ├── event_detail.blade.php      # Trang Chi tiết sự kiện & Đặt vé
│           ├── favorite.blade.php          # Trang Danh sách yêu thích
│           ├── index.blade.php             # Trang chủ danh sách sự kiện
│           ├── myTicket.blade.php          # Trang Vé của tôi & Hủy vé
│           └── profile.blade.php           # Trang Hồ sơ cá nhân & Đổi mật khẩu
└── routes/
    └── web.php                             # Định tuyến hệ thống phân nhóm rõ ràng
```

---

## 6. Hướng dẫn Cài đặt & Khởi chạy

### Bước 1: Clone dự án về máy
```bash
git clone https://github.com/quan02077/Do_An_PHP.git
cd Do_An_PHP/Do_an_php
```

### Bước 2: Cài đặt thư viện PHP và JavaScript
```bash
composer install
npm install
npm run build
```

### Bước 3: Cấu hình tệp môi trường (.env)
```bash
cp .env.example .env
```
Mở file `.env` và kiểm tra cấu hình kết nối CSDL MySQL (XAMPP/Laragon mặc định cổng `3306`, user `root`, password trống):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quan_ly_su_kien
DB_USERNAME=root
DB_PASSWORD=
```

Đặt `APP_URL` đúng địa chỉ đang mở, ví dụ `http://127.0.0.1:8000`. Quên mật khẩu gửi email tiếng Việt chứa liên kết token có thời hạn (60 phút) qua **Gmail OAuth2** (`MAIL_MAILER=gmail_oauth2`, hỗ trợ cả `MAIL_GMAIL_MODE=smtp` cho SMTP XOAUTH2 và `MAIL_GMAIL_MODE=api` cho Gmail REST API):
```env
MAIL_MAILER=gmail_oauth2
MAIL_GMAIL_MODE=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
GMAIL_OAUTH_CLIENT_ID=your_google_client_id
GMAIL_OAUTH_CLIENT_SECRET=your_google_client_secret
GMAIL_OAUTH_REFRESH_TOKEN=your_google_refresh_token
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```
Nếu dùng Gmail App Password/Mailtrap có thể đổi sang `MAIL_MAILER=smtp`, hoặc chạy demo offline với `MAIL_MAILER=log` rồi mở `storage/logs/laravel.log` để lấy liên kết đặt lại mật khẩu.

### Tính năng Quản lý người dùng, Nhắc lịch tự động & Đánh giá sau sự kiện
- **Quản lý người dùng (Tab `Người dùng` trong `/admin`)**: tìm kiếm theo họ tên/email/SĐT, lọc vai trò & trạng thái, khóa/mở khóa (`bi_khoa`/`hoat_dong`) và đổi vai trò (`user`/`admin`). Không cho xóa tài khoản để bảo toàn lịch sử vé; không cho tự khóa/tự hạ quyền và không khóa/hạ quyền admin cuối cùng. **Khi khóa tài khoản, các vé `cho_duyet`/`da_xac_nhan` trước đó vẫn được giữ nguyên** (chỉ chặn đăng nhập, tự đăng xuất ở request kế tiếp nếu đang đăng nhập và chặn thao tác mới).
- **Nhắc lịch trước ngày diễn ra (`su-kien:nhac-lich`)**: đã đăng ký chạy tự động mỗi giờ trong `routes/console.php` (`withoutOverlapping`), ghi dấu `dang_ky.da_nhac_lich_luc` sau khi gửi thành công để tránh gửi trùng.
  - Chạy tay khi demo: `php artisan su-kien:nhac-lich --gio=48`
  - Chạy lịch định kỳ trên máy dev: `php artisan schedule:work`
  - Xem nội dung email khi dùng `MAIL_MAILER=log` trong `storage/logs/laravel.log`.
- **Đánh giá sau sự kiện**: chỉ thành viên (`hoat_dong`, không phải admin) đã check-in (`trang_thai = da_tham_gia`) của sự kiện **đã kết thúc** mới được tạo/sửa/xóa đánh giá (1–5 sao, tối đa 1 đánh giá/người/sự kiện). Điểm trung bình hiển thị trên thẻ sự kiện và trang chi tiết; admin có thể ẩn/hiện đánh giá vi phạm tại tab `Đánh giá` trong `/admin`.

Realtime dùng Reverb. `.env.example` chứa khóa mẫu chỉ dành cho demo local; khi chạy ngoài máy cá nhân hãy thay `REVERB_APP_KEY`, `REVERB_APP_SECRET` và `REVERB_APP_ID`. Đặt `BROADCAST_CONNECTION=reverb`, `REVERB_HOST=127.0.0.1`, `REVERB_PORT=8080`, `REVERB_SCHEME=http`. Nếu mở web bằng điện thoại cùng Wi-Fi, khởi chạy Reverb trên `0.0.0.0:8080`; JavaScript sẽ kết nối đến IP máy tính trong thanh địa chỉ. Cho phép cổng 8000 và 8080 qua tường lửa nếu cần.

Trình duyệt điện thoại thường không cấp camera cho trang `http://<IP-LAN>:8000` vì camera yêu cầu secure context. Để demo quét camera, dùng `localhost` trên chính máy có webcam hoặc triển khai HTTPS/WSS hợp lệ; khi dùng điện thoại qua HTTP LAN có thể nhập mã vé thủ công.

### Bước 4: Khởi tạo Application Key
```bash
php artisan key:generate
```

### Bước 5: Chạy Migration và Nạp dữ liệu mẫu (Seeder)
```bash
php artisan migrate:fresh --seed
```

Seeder cũng chép 10 ảnh mẫu từ `database/seeders/images` sang `public/uploads/su_kien` (thư mục `uploads` không nằm trong git). Nếu ảnh sự kiện bị trống, chạy lại lệnh trên. Ảnh admin tải lên không được commit.

### Bước 6: Khởi chạy web và Reverb ở hai cửa sổ terminal
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

Các event realtime dùng `ShouldBroadcastNow`, nên bản demo này không cần chạy `queue:work` cho việc phát số chỗ và check-in. Nếu đổi sang `ShouldBroadcast`, phải chạy worker hàng đợi.

Chạy kiểm thử với SQLite trong bộ nhớ sau khi cài `vendor`:
```bash
php artisan test
```

SQLite trong bộ nhớ không kiểm chứng được khóa dòng khi nhiều người đặt cùng lúc. Kiểm thử đồng thời trên MySQL/MariaDB thật (tự tạo và xóa database `qqq_concurrency_test`, không đụng `quan_ly_su_kien`; chạy bằng Git Bash):

```bash
bash tests/concurrency/run.sh
```

Chi tiết kịch bản và kết quả: `tests/concurrency/README.md`.

Truy cập hệ thống tại: **`http://127.0.0.1:8000`**

---

## 7. Tài khoản Thử nghiệm (Demo Accounts)

Hệ thống đã nạp sẵn tài khoản với đầy đủ dữ liệu mô phỏng để phục vụ việc chấm điểm và kiểm thử:

| Loại tài khoản | Email đăng nhập | Mật khẩu | Quyền hạn và Phạm vi thử nghiệm |
|:---|:---|:---|:---|
| **Quản trị viên (Admin)** | `admin@qqq.vn` | `123456` | Toàn quyền vào `/admin`: Quản lý sự kiện, danh mục, upload ảnh poster, duyệt/hủy vé |
| **Người dùng (User 1)** | `quan@gmail.com` | `123456` | Trải nghiệm đặt vé, xem vé trong *Vé của tôi*, hủy vé, lưu yêu thích, sửa hồ sơ |
| **Người dùng (User 2)** | `nam@gmail.com` | `123456` | Tài khoản thứ 2 để thử nghiệm đăng ký vé song song và kiểm tra số lượng chỗ |
