# QQQ — HỆ THỐNG QUẢN TRỊ & ĐẶT VÉ SỰ KIỆN TRỰC TUYẾN

**Đồ án môn học: Lập trình mã nguồn mở**  
**Trường Đại học Công Thương TP.HCM (HUIT) — Khoa Công nghệ Thông tin**

---

## 1. Giới thiệu Đề tài
**QQQ** là nền tảng trực tuyến kết nối ban tổ chức sự kiện với người tham dự. Hệ thống giải quyết trọn vẹn bài toán từ khâu xuất bản, phân loại danh mục, quảng bá sự kiện, quản lý số lượng chỗ ngồi, đặt vé trực tuyến tự động sinh mã vé duy nhất, đến phân hệ duyệt vé và kiểm soát dữ liệu chuyên nghiệp dành cho Quản trị viên (Admin).

---

## 2. Thông tin Nhóm Sinh viên & Bảng phân công nhiệm vụ
*(Căn cứ theo Bảng phân công công việc chi tiết PCCV_EventVN.xlsx)*

| STT | Họ và tên | Công việc phân công chi tiết | Kết quả bàn giao (Deliverable) | Tiến độ |
|:---:|:---|:---|:---|:---:|
| **1** | **Nguyễn Nhật Minh Quân** | • Cài Laragon, PHP/Composer; tạo database `quan_ly_su_kien`; cấu hình `.env.example`<br>• Hoàn thiện migration tiếng Việt: `nguoi_dung`, `danh_muc`, `su_kien`, `dang_ky`, `yeu_thich`; khóa ngoại và unique<br>• Khai báo Model Eloquent, `$fillable`, casts và quan hệ 1-n, n-n<br>• Tạo Seeder/Factory: admin, user, danh mục, sự kiện, đăng ký và yêu thích mẫu<br>• Cài rule xóa: chặn xóa danh mục còn event; event có đăng ký chỉ được chuyển đã hủy | • Laravel kết nối MySQL và chạy trang chủ<br>• `php artisan migrate:fresh --seed` chạy không lỗi<br>• Model truy xuất đúng quan hệ<br>• Dữ liệu demo đủ để trình bày<br>• Ràng buộc xóa đúng nghiệp vụ | **Hoàn thành** |
| **2** | **Lương Văn Quan** | • Lập bảng map HTML mockup sang layout, partial, component và Blade view<br>• Tạo `layouts/app`, navbar, footer, alert và input component bằng Blade + Bootstrap<br>• Chuyển trang chủ, danh sách và chi tiết sự kiện sang Blade responsive<br>• Chuyển Vé của tôi, Yêu thích và Hồ sơ sang Blade<br>• Chụp ảnh màn hình chức năng; review responsive và thống nhất UI trước demo | • Danh sách chuyển đổi view rõ ràng<br>• Layout dùng chung cho toàn site<br>• Guest xem danh sách và chi tiết event<br>• Giao diện user hoàn chỉnh<br>• Bộ ảnh báo cáo và UI đã QA | **Hoàn thành** |
| **3** | **Trịnh Hữu Kiến Quốc** | • Đăng ký, đăng nhập, đăng xuất với `NguoiDung`, Hash và Laravel Auth<br>• Tạo middleware `role:admin`; bảo vệ route và hiển thị quyền trong Blade<br>• CRUD đầy đủ `danh_muc` và `su_kien`: Resource Controller, Form Request, validate, upload poster tùy chọn<br>• Tìm kiếm, lọc danh mục/trạng thái, phân trang Bootstrap với `withQueryString`<br>• Đăng ký vé phía server: chặn trùng, hết chỗ, event chưa công khai/đã bắt đầu<br>• Vé của tôi, hủy vé, giữ trạng thái cancelled và cho phép đăng ký lại<br>• Test rule chính, README, ERD, Use Case, phân công, script demo và freeze tính năng | • User/Admin đăng nhập được<br>• User không truy cập được `/admin`<br>• Admin tạo/xem/sửa/xóa đúng hai bảng<br>• Danh sách có search/filter/pagination<br>• Đăng ký vé đúng quy tắc nghiệp vụ<br>• User quản lý vé của mình<br>• Bản demo ổn định và hồ sơ hoàn chỉnh | **Hoàn thành** |

---

## 3. Danh sách Chức năng Hệ thống

### Phân hệ Khách & Người dùng (User / Public)
1. **Trang chủ & Khám phá sự kiện:**
   * Tìm kiếm từ khóa đa trường (theo tên sự kiện, mô tả tóm tắt, địa điểm tổ chức).
   * Lọc theo danh mục thể loại (Âm nhạc, Công nghệ, Thể thao, Ẩm thực, Hội thảo...).
   * Lọc theo 4 trạng thái sự kiện: *Sắp diễn ra, Đang diễn ra, Đã kết thúc, Sự kiện yêu thích*.
   * Phân trang tự động chuẩn Bootstrap với phương thức `withQueryString()`.
2. **Xem chi tiết sự kiện:**
   * Hiển thị đầy đủ banner poster, đơn vị tổ chức, thời gian bắt đầu/kết thúc, giá vé.
   * Thanh tiến độ (Progress bar) trực quan thể hiện tỷ lệ số chỗ đã đăng ký / quy mô tối đa.
   * **Tích hợp API Bản đồ Google Maps:** Nhúng bản đồ vị trí trực quan theo địa điểm tổ chức, hỗ trợ liên kết mở ứng dụng Google Maps chỉ đường.
   * Danh sách các sự kiện liên quan cùng thể loại.
3. **Đăng ký & Đặt vé sự kiện (Core Feature):**
   * Kiểm tra điều kiện ngặt nghèo: sự kiện đã hủy, sự kiện đã qua thời gian bắt đầu, sự kiện đã hết chỗ.
   * Chống trùng lặp: mỗi người dùng chỉ được đăng ký 1 vé cho mỗi sự kiện.
   * Tự động sinh mã vé ngẫu nhiên theo định dạng chuẩn: `QQQ-YYYY-XXXXXX` (Ví dụ: `QQQ-2026-B8D2F1`).
4. **Quản lý Vé của tôi & Vé điện tử QR MoMo (E-Ticket):**
   * Danh sách toàn bộ vé đã đăng ký kèm mã vé, ngày đăng ký, trạng thái duyệt.
   * **Vé điện tử phong cách MoMo / Ticketbox:** Tự động sinh mã QR động trực tiếp từ mã vé định danh, giao diện cuống vé răng cưa chuẩn thực tế, tích hợp nút **In / Tải vé PDF** (`window.print`).
   * Chức năng **Hủy vé an toàn**: cập nhật trạng thái `da_huy`, bảo lưu lịch sử giao dịch và cho phép đăng ký lại khi cần.
5. **Sự kiện yêu thích (Bookmark):**
   * Nút tim lưu yêu thích tại tất cả các thẻ sự kiện.
   * Trang quản lý danh sách yêu thích riêng cho từng tài khoản.
6. **Hồ sơ cá nhân & Bảo mật:**
   * Xem và chỉnh sửa thông tin cá nhân (họ tên, email, số điện thoại, ngày sinh, địa chỉ, tiểu sử).
   * Đổi mật khẩu tài khoản với cơ chế xác thực mật khẩu cũ và băm mật khẩu mới bằng thuật toán Bcrypt.

### Phân hệ Quản trị viên (Admin Portal)
1. **Bảng điều khiển Thống kê & Biểu đồ trực quan (Dashboard Tab):**
   * Thẻ đo lường tức thời: Tổng số sự kiện, sự kiện sắp diễn ra, tổng số vé đã đặt, tổng số thành viên.
   * **Trực quan hóa dữ liệu (Chart.js):**
     * **Biểu đồ tròn (Doughnut Chart):** Phân bổ sự kiện theo từng thể loại danh mục.
     * **Biểu đồ cột (Bar Chart):** Top các sự kiện thu hút số lượng lượt đăng ký vé cao nhất.
2. **Quản lý Sự kiện (Events Tab - CRUD Modal):**
   * Danh sách bảng sự kiện trực quan có hình ảnh, danh mục, thời gian, số lượng vé và trạng thái.
   * **Thêm mới sự kiện:** Popup modal hỗ trợ upload ảnh poster trực tiếp từ máy tính hoặc chọn ảnh thumbnail có sẵn.
   * **Chỉnh sửa sự kiện:** Đổ dữ liệu tự động vào form modal qua Javascript Data Attributes.
   * **Xóa sự kiện an toàn:** Nếu sự kiện đã có người đặt vé, hệ thống tự động chuyển trạng thái sang `da_huy` để tránh lỗi vi phạm khóa ngoại MySQL.
3. **Quản lý Danh mục (Categories Tab - CRUD Modal):**
   * Quản lý phân loại sự kiện (Tên danh mục, mô tả, trạng thái hoạt động / tạm dừng).
   * Quy tắc nghiệp vụ chặn xóa danh mục khi đang có sự kiện trực thuộc.
4. **Duyệt đăng ký vé & Quét mã QR Check-in (Registrations Tab):**
   * Theo dõi danh sách tất cả các lượt đăng ký vé của người dùng trong hệ thống.
   * **Soát vé & Check-in QR trực tiếp (O2O Scanner & RESTful API):**
     * Tích hợp máy quét Camera/Webcam chuẩn HTML5: Quét trực tiếp mã QR trên điện thoại của khách tại cổng sự kiện.
     * Ô nhập nhanh mã vé hỗ trợ soát vé thủ công.
     * Tự động xác thực qua API `POST /admin/check-in`, cập nhật trạng thái `da_tham_gia` theo thời gian thực và cảnh báo ngăn chặn hành vi sử dụng vé đã check-in.
   * Thao tác 1 chạm: **Duyệt** vé (`da_xac_nhan`), **Check-in** vé (`da_tham_gia`) hoặc **Hủy** vé (`da_huy`).
5. **Cơ chế Phân quyền (Security & Middleware):**
   * Middleware `AuthenticateCustom` (`auth.custom`): Chặn khách vãng lai truy cập vào các trang cá nhân và đặt vé.
   * Middleware `AdminMiddleware` (`admin`): Kiểm tra quyền hạn `vai_tro === 'admin'`, chặn người dùng thường truy cập trái phép vào URL `/admin`.

---

## 4. Sơ đồ Cơ sở dữ liệu (Database Schema)

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
* **`dang_ky`**: Khóa ngoại liên kết `nguoi_dung_id` và `su_kien_id`, mã vé độc nhất `ma_ve`, trạng thái duyệt (`cho_duyet`, `da_xac_nhan`, `da_huy`).
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

### Bước 2: Cài đặt thư viện phụ thuộc (Composer)
```bash
composer install
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

### Bước 4: Khởi tạo Application Key
```bash
php artisan key:generate
```

### Bước 5: Chạy Migration và Nạp dữ liệu mẫu (Seeder)
```bash
php artisan migrate:fresh --seed
```

### Bước 6: Khởi chạy máy chủ phát triển
```bash
php artisan serve
```

Truy cập hệ thống tại: **`http://127.0.0.1:8000`**

---

## 7. Tài khoản Thử nghiệm (Demo Accounts)

Hệ thống đã nạp sẵn tài khoản với đầy đủ dữ liệu mô phỏng để phục vụ việc chấm điểm và kiểm thử:

| Loại tài khoản | Email đăng nhập | Mật khẩu | Quyền hạn và Phạm vi thử nghiệm |
|:---|:---|:---|:---|
| **Quản trị viên (Admin)** | `admin@gmail.com` | `123456` | Toàn quyền vào `/admin`: Quản lý sự kiện, danh mục, upload ảnh poster, duyệt/hủy vé |
| **Người dùng (User 1)** | `user@gmail.com` | `123456` | Trải nghiệm đặt vé, xem vé trong *Vé của tôi*, hủy vé, lưu yêu thích, sửa hồ sơ |
| **Người dùng (User 2)** | `user2@gmail.com` | `123456` | Tài khoản thứ 2 để thử nghiệm đăng ký vé song song và kiểm tra số lượng chỗ |
