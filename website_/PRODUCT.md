# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack
Mã nguồn thuần HTML/CSS/JS (Bootstrap 5 CDN, Vanilla JS) phục vụ đồ án môn học PHP, có thể chạy trực tiếp trên trình duyệt hoặc triển khai qua web server PHP cục bộ (XAMPP / WampServer) mà không phụ thuộc vào Node.js build step. Sử dụng framework Bootstrap 5 cho toàn bộ giao diện, vì vậy không cần sử dụng Tailwind CSS nữa. Hãy xóa hết Tailwind CSS khỏi website.

## Users
- **Người tham dự sự kiện (Attendees)**: Người dùng đại chúng tại Việt Nam có nhu cầu tìm kiếm, khám phá các sự kiện (hội thảo công nghệ, đêm nhạc, văn hóa, thể thao), lưu sự kiện yêu thích, đăng ký vé trực tuyến và quản lý vé điện tử (mã vé định dạng EVN-2026).
- **Ban tổ chức / Quản trị viên (Organizers / Admins)**: Quản trị viên quản lý danh mục sự kiện (thêm, sửa, xóa), theo dõi danh sách đăng ký và duyệt vé người tham dự.

## Product Purpose
EventVN là nền tảng quản lý và đặt vé sự kiện trực tuyến tại Việt Nam, mang lại trải nghiệm duyệt tìm và đăng ký vé mượt mà, tiện lợi cho người tham gia, đồng thời cung cấp bảng điều khiển quản trị trực quan cho ban tổ chức trong phạm vi đồ án môn học PHP.

## Positioning
Nền tảng đặt vé sự kiện giao diện hiện đại, dễ tiếp cận, giữ cấu trúc thuần gọn nhẹ để kết hợp hoàn hảo với backend PHP/MySQL, không đòi hỏi cấu hình môi trường phức tạp mà vẫn đảm bảo tính thẩm mỹ và trải nghiệm người dùng thực tế.

## Operating Context
- Mở trực tiếp file HTML trên trình duyệt (Chrome, Edge, Firefox, Cốc Cốc) hoặc chạy qua máy chủ web PHP cục bộ (XAMPP `htdocs`, WampServer).
- Cơ sở dữ liệu MySQL (kết nối qua `ConnectionDB.php` hoặc MySQL đám mây).
- Lưu trữ dữ liệu tương tác linh hoạt (trình duyệt localStorage cho demo / đồng bộ CSDL PHP).

## Capabilities and Constraints
- **Khả năng**:
  - Trang chủ khám phá sự kiện: tìm kiếm, lọc theo thể loại, lưu yêu thích (bookmark).
  - Chi tiết sự kiện: lịch trình, thông tin diễn giả/địa điểm, form đăng ký vé.
  - Xác thực người dùng & phân quyền: đăng ký, đăng nhập, demo 1-chạm (Admin & Thành viên).
  - Trang cá nhân & Dashboard: xem vé điện tử đã đăng ký, hủy vé, quản lý sự kiện yêu thích.
  - Quản trị Admin: quản lý sự kiện (thêm mới, chỉnh sửa, xóa), duyệt vé tham dự.
- **Ràng buộc**:
  - Tuyệt đối giữ mã nguồn ở dạng thuần (HTML/CSS/JS + Bootstrap 5 CDN), không chuyển sang các framework SPA phức tạp (React/Vue/Next.js) cần quy trình build Node.js, nhằm phục vụ đúng mục tiêu đồ án môn học PHP.
  - Ngôn ngữ giao diện chính là tiếng Việt.

## Brand Commitments
- Tên thương hiệu: **EventVN** (Slogan: "Sự kiện & Hội thảo", "Khám phá & Đặt vé sự kiện Dễ dàng, Nhanh chóng").
- Nhận diện trực quan: Phông chữ Outfit (`Outfit, sans-serif`), gam màu chủ đạo Slate tối (`#0F172A`), màu nhấn Rose (`#F43F5E`) cho tương tác yêu thích và các nhãn trạng thái trực quan.

## Evidence on Hand
- Bộ trang giao diện hoàn chỉnh: [index.html](file:///d:/BaiTap/Test/website/index.html), [event-detail.html](file:///d:/BaiTap/Test/website/event-detail.html), [auth.html](file:///d:/BaiTap/Test/website/auth.html), [dashboard.html](file:///d:/BaiTap/Test/website/dashboard.html), [profile.html](file:///d:/BaiTap/Test/website/profile.html), [admin.html](file:///d:/BaiTap/Test/website/admin.html).
- Tệp định dạng CSS [css/style.css](file:///d:/BaiTap/Test/website/css/style.css), các tệp kịch bản JavaScript trong thư mục [js/](file:///d:/BaiTap/Test/website/js/) và tệp kết nối cơ sở dữ liệu [ConnectionDB.php](file:///d:/BaiTap/Test/website/ConnectionDB.php).
- Hướng dẫn vận hành tại [README.md](file:///d:/BaiTap/Test/website/README.md).

## Product Principles
- **Gọn nhẹ & Tương thích tối đa**: Giữ mã nguồn thuần, không phụ thuộc build tooling, chạy mượt mà trên môi trường PHP đồ án.
- **Thẩm mỹ cao cấp & Trực quan**: Giao diện chỉn chu, bố cục cân đối, mang lại cảm giác chuyên nghiệp như một sản phẩm thực tế.
- **Tập trung vào trải nghiệm cốt lõi**: Tối ưu hóa hành trình khám phá - đặt vé của người tham dự và quy trình duyệt vé của ban tổ chức.
