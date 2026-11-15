# EventVN — Nền tảng Đặt vé & Quản lý Sự kiện Trực tuyến

Đồ án môn học **Lập trình Web với PHP & Laravel** — Trường Đại học Công Thương TP.HCM (HUIT).

---

## 👥 Nhóm sinh viên thực hiện
* **Nguyễn Nhật Minh Quân** — Phụ trách Nền tảng, Database Migration, Eloquent Model, Seeders & Ràng buộc toàn vẹn.
* **Nguyễn Minh Quan** — Phụ trách Chuyển đổi giao diện Blade, Layout chung, Responsive & Thống nhất UI.
* **Trịnh Kiến Quốc** — Phụ trách Xác thực (Auth), Middleware phân quyền, CRUD Admin & Nghiệp vụ đặt vé.

---

## 🚀 Công nghệ sử dụng
* **Backend:** PHP 8.2+, Laravel Framework 12 (mô hình MVC).
* **Database:** MySQL (Database: `quan_ly_su_kien`), Eloquent ORM.
* **Frontend:** Blade Template Engine, Bootstrap 5.3, Font Awesome 6.5.
* **Bảo mật:** CSRF Token Protection, Password Hashing (Bcrypt), Authentication & Role Middleware.

---

## ⚙️ Hướng dẫn cài đặt & Khởi chạy dự án

### 1. Yêu cầu môi trường
* PHP $\ge$ 8.2
* Composer 2.x
* MySQL (qua XAMPP hoặc Laragon)

### 2. Các bước cài đặt
```bash
# 1. Di chuyển vào thư mục dự án
cd Do_an_php

# 2. Cài đặt các thư viện phụ thuộc
composer install

# 3. Tạo file cấu hình môi trường
cp .env.example .env

# 4. Tạo khóa ứng dụng (Application Key)
php artisan key:generate

# 5. Tạo cơ sở dữ liệu và nạp dữ liệu mẫu (Seeder)
php artisan migrate:fresh --seed

# 6. Khởi chạy máy chủ phát triển
php artisan serve
```

Ứng dụng sẽ chạy tại địa chỉ: **`http://127.0.0.1:8000`**

---

## 🔑 Tài khoản thử nghiệm (Demo Accounts)

| Vai trò | Email đăng nhập | Mật khẩu | Chức năng chính |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@gmail.com` | `123456` | Toàn quyền Quản lý sự kiện, danh mục, duyệt và hủy vé |
| **Người dùng (User)** | `user@gmail.com` | `123456` | Xem chi tiết, đặt vé, lưu yêu thích, xem vé đã mua, đổi mật khẩu |
| **Người dùng 2** | `user2@gmail.com` | `123456` | Thử nghiệm đặt vé và tương tác song song |

---

## 🌟 Danh sách Chức năng chính

### 1. Phân hệ Khách & Người dùng (User / Public)
* **Trang chủ & Khám phá:** Tìm kiếm từ khóa theo tên/địa điểm, lọc theo danh mục, lọc theo trạng thái (Sắp diễn ra, Đang diễn ra, Đã kết thúc, Yêu thích), phân trang động.
* **Chi tiết sự kiện:** Xem chi tiết thời gian, địa điểm, ban tổ chức, số lượng chỗ còn lại, thanh tiến độ đăng ký.
* **Đăng ký / Đặt vé:** Hệ thống tự động sinh mã vé duy nhất theo định dạng `QQQ-YYYY-XXXXXX`, chống đặt trùng và chặn khi sự kiện hết chỗ.
* **Vé của tôi:** Quản lý danh sách vé đã đặt, xem trạng thái duyệt vé, hỗ trợ hủy vé an toàn (chuyển trạng thái `da_huy`, không xóa cứng dữ liệu).
* **Sự kiện yêu thích:** Lưu và quản lý danh sách sự kiện yêu thích cá nhân bằng nút tim.
* **Hồ sơ cá nhân:** Cập nhật thông tin tài khoản (họ tên, email, sđt, ngày sinh, địa chỉ) và đổi mật khẩu bảo mật.

### 2. Phân hệ Quản trị (Admin Portal)
* **Bảng điều khiển (Dashboard):** Thống kê số lượng sự kiện, vé đã đặt, thành viên và danh mục.
* **Quản lý Sự kiện (CRUD):** Thêm mới sự kiện kèm upload poster hoặc chọn ảnh có sẵn, chỉnh sửa và hủy sự kiện qua Modal.
* **Quản lý Danh mục (CRUD):** Thêm/sửa danh mục; ràng buộc nghiệp vụ chặn xóa danh mục khi đang có sự kiện liên kết.
* **Duyệt đăng ký vé:** Duyệt xác nhận vé hoặc hủy vé của người tham gia.
* **Bảo vệ bằng Middleware:** Chặn khách và người dùng thường truy cập trái phép vào trang quản trị.
