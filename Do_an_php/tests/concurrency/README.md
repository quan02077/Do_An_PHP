# Kiểm thử đồng thời trên MySQL thật

Bộ test PHPUnit chạy bằng SQLite trong bộ nhớ, nên không kiểm chứng được khóa dòng
(`lockForUpdate`) khi có nhiều kết nối cùng lúc. Thư mục này chứa một harness riêng:
mỗi yêu cầu là **một tiến trình PHP độc lập với kết nối MySQL riêng**, tất cả chờ đến
cùng một mốc thời gian rồi gửi yêu cầu qua **HTTP kernel thật** của Laravel
(route → middleware `auth.custom`/`admin` → controller).

## Cách chạy

Yêu cầu: MySQL/MariaDB đang chạy (XAMPP), đã `composer install`, không có config cache.

```bash
bash tests/concurrency/run.sh            # tự tạo DB qqq_concurrency_test, chạy, rồi xóa
bash tests/concurrency/run.sh --keep-db  # giữ lại DB test để xem dữ liệu
```

Trên Windows chạy bằng Git Bash. Mất khoảng 1–2 phút. Thoát với mã 0 nếu đạt.

**An toàn dữ liệu:** harness không đọc hay ghi `.env`; cấu hình DB được ghi đè bằng biến
môi trường. Tên database bắt buộc kết thúc bằng `_test` (kiểm tra ở cả `run.sh` lẫn PHP),
nên không thể chạy nhầm vào `quan_ly_su_kien`.

## Các kịch bản

| Kịch bản | Mô tả | Kỳ vọng |
|---|---|---|
| Đối chứng | Cùng logic đặt vé nhưng **bỏ** `lockForUpdate`, 20 user / 5 chỗ | Bán vượt sức chứa — chứng minh harness bắt được race |
| A1 | 20 user / 5 chỗ, nới cửa sổ race 300 ms | Đúng 5 vé, 15 yêu cầu báo "hết vé" |
| A2 | 30 user / 1 chỗ, không thêm trễ | Đúng 1 vé |
| B | 1 user gửi 10 yêu cầu cùng lúc | Đúng 1 vé, các yêu cầu còn lại báo "đã đăng ký rồi" |
| C | 10 admin cùng check-in một mã vé | Đúng 1 lần check-in thành công |
| D | User hủy vé đúng lúc admin quét QR check-in cùng vé (hủy ngủ 1,5 s sau khi đọc vé, check-in xuất phát sau 0,3 s) | Chỉ một bên thành công, trạng thái cuối khớp bên đó |

"Nới cửa sổ race" nghĩa là tiến trình ngủ ngay sau truy vấn đếm số vé (giữa bước kiểm tra
còn chỗ và bước insert) hoặc, với kịch bản D, ngay sau khi đọc dòng vé (giữa bước kiểm tra
trạng thái và bước lưu `da_huy`), thực hiện bằng `DB::listen` — **không sửa mã nguồn ứng dụng**.
Mọi kịch bản còn kiểm tra không có phản hồi 5xx hay exception.

## Kết quả đã ghi nhận (MariaDB 10.4.32, InnoDB, REPEATABLE-READ)

- Đối chứng: 20 vé / 5 chỗ (bán vượt, đúng kỳ vọng).
- A1: 5 thành công, 15 "hết vé"; thời gian phản hồi trải từ ~2 s đến ~9 s, cho thấy các
  yêu cầu thực sự xếp hàng chờ khóa dòng sự kiện.
- A2: 1/1. B: 1 vé, 9 "đã đăng ký rồi". C: 1 check-in, 9 "đã check-in trước đó".
- D (lỗi do Codex review phát hiện): **trước khi sửa**, cả hủy vé lẫn check-in đều báo thành công
  và vé cuối cùng là `da_huy` — khách đã vào cửa nhưng vé bị hủy. **Sau khi** `cancelTicket` khóa dòng
  vé trong transaction: hủy thành công, check-in chờ khóa rồi báo "vé đã bị hủy", trạng thái cuối `da_huy`.

## Giới hạn

- Không đi qua web server (Apache/`artisan serve`); điều này không ảnh hưởng tới khóa CSDL.
- Chưa có kịch bản admin khôi phục vé đã hủy song song với user đặt chỗ cuối cùng
  (hai luồng cùng khóa dòng `su_kien` nên được kỳ vọng an toàn, nhưng chưa đo).

## Tệp

- `run.sh` — tạo DB test, migrate, chạy các kịch bản, so sánh với kỳ vọng, dọn dẹp.
- `setup.php` — xóa dữ liệu nghiệp vụ trong DB test, tạo admin, sự kiện, N người dùng.
- `worker.php` — một tiến trình gửi một yêu cầu (`book`, `checkin` hoặc `naive`).
- `boot.php` — nạp ứng dụng và chặn database không có hậu tố `_test`.
