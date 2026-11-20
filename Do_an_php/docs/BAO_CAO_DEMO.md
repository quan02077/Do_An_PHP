# Khung báo cáo và demo nhóm QQQ (40 phút)

## 1. Lý do chọn đề tài (3 phút)

Quản lý sự kiện thủ công khó theo dõi số chỗ, danh sách đăng ký và trạng thái tham gia. Hệ thống QQQ tập trung vào luồng Admin tạo sự kiện, thành viên đăng ký vé và Admin check-in.

## 2. Chức năng và thiết kế (10 phút)

- Giới thiệu 2 vai trò Admin và User; dùng [`UseCase.puml`](UseCase.puml) để trình bày quyền của từng vai trò.
- Trình bày các bảng `nguoi_dung`, `danh_muc`, `su_kien`, `dang_ky`, `yeu_thich` theo [`ERD.md`](ERD.md).
- Nêu rõ Laravel Auth, middleware, Eloquent, migration, Blade, validation, tìm kiếm và phân trang.
- Đối chiếu phân công từng thành viên ở bảng trong README; từng người tự trình bày phần code mình thực hiện.

## 3. Điểm nổi bật (5 phút)

- Vé có mã QR và check-in chỉ dành cho Admin.
- Số chỗ còn lại và bộ đếm check-in cập nhật qua Reverb giữa hai trình duyệt đang mở.
- Giới hạn số chỗ; đăng ký đồng thời được khóa theo dòng sự kiện trong giao dịch MySQL.
- Sự kiện có người đăng ký được chuyển sang đã hủy thay vì xóa dữ liệu vé.

## 4. Demo (12 phút)

1. Khách tìm và lọc sự kiện; chuyển trang, giữ từ khóa.
2. Mở cùng trang chi tiết ở hai trình duyệt. Thành viên đăng ký vé ở cửa sổ thứ nhất; cửa sổ thứ hai thấy số chỗ đổi không cần F5. Hủy vé và quan sát số chỗ tăng lại.
3. Mở trang Admin ở hai cửa sổ. Quét hoặc nhập mã vé ở cửa sổ thứ nhất; cửa sổ thứ hai thấy bộ đếm và lượt check-in mới.
4. Admin tạo/sửa danh mục và sự kiện, xem danh sách đăng ký.
5. Thử một ca bị chặn: khách vào `/admin`, sự kiện hết chỗ hoặc vé check-in hai lần.

## 5. Trả lời câu hỏi code (10 phút)

Kịch bản demo chi tiết từng phút và bộ câu hỏi code dự kiến kèm gợi ý trả lời cho từng thành viên: [`DEMO.md`](DEMO.md).

Chuẩn bị mở sẵn `routes/web.php`, `AuthController`, `EventController`, `AdminController`, `HomeController`, middleware, migration và test. Giải thích đường đi từ route đến controller, model, database và Blade.

Với Reverb, giải thích `TicketCountsChanged` phát trên kênh công khai theo sự kiện, `TicketCheckedIn` phát trên kênh riêng Admin, và JavaScript dùng Laravel Echo để cập nhật giao diện. Event phát sau khi giao dịch lưu thành công; nếu Reverb không chạy, thao tác đặt vé vẫn phải hoạt động.

Nếu quét camera trên điện thoại, cần truy cập ứng dụng bằng HTTPS và Reverb bằng WSS hợp lệ. URL HTTP theo IP Wi-Fi giúp xem web và nhập mã vé, nhưng trình duyệt thường chặn quyền camera. Khi chưa chuẩn bị HTTPS, dùng webcam trên máy chạy `localhost` hoặc nhập mã vé thủ công trong demo.

## Ảnh cần chụp từ bản demo thật

Chưa có ảnh trong repo. Trước khi nộp, nhóm cần chụp: trang khách trên desktop và điện thoại, kết quả tìm kiếm có phân trang, chi tiết sự kiện, vé của thành viên, màn Admin quản lý sự kiện/danh mục, kết quả check-in và màn lỗi validation. Kiểm tra chữ không tràn và thao tác được ở độ rộng khoảng 375 px.
