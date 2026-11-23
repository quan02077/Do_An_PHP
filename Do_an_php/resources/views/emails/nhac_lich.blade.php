<!doctype html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nhắc lịch sự kiện — {{ config('app.name', 'QQQ') }}</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1f2937;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f5f7;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background-color:#ffffff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;">
          <tr>
            <td style="background-color:#111827;padding:24px 32px;text-align:left;">
              <span style="display:inline-block;background-color:#ffffff;color:#111827;font-weight:700;font-size:16px;width:32px;height:32px;line-height:32px;text-align:center;border-radius:8px;vertical-align:middle;">Q</span>
              <span style="color:#ffffff;font-size:18px;font-weight:700;margin-left:10px;vertical-align:middle;">{{ config('app.name', 'QQQ') }}</span>
            </td>
          </tr>

          <tr>
            <td style="padding:32px;">
              <h1 style="margin:0 0 16px 0;font-size:20px;font-weight:700;color:#111827;">
                Nhắc lịch sự kiện sắp diễn ra
              </h1>
              <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#374151;">
                Xin chào <strong>{{ $nguoiDung->ho_ten ?? 'bạn' }}</strong>,
              </p>
              <p style="margin:0 0 20px 0;font-size:15px;line-height:1.6;color:#374151;">
                Sự kiện bạn đã đăng ký sắp bắt đầu. Vui lòng kiểm tra lại thông tin vé dưới đây để tham dự đúng giờ:
              </p>

              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:24px;">
                <tr>
                  <td style="padding:16px 20px;font-size:14px;line-height:1.8;color:#374151;">
                    <div><strong>Sự kiện:</strong> {{ $suKien->ten_su_kien ?? 'Sự kiện' }}</div>
                    <div><strong>Thời gian bắt đầu:</strong> {{ optional($suKien->thoi_gian_bat_dau)->format('d/m/Y H:i') ?? 'Đang cập nhật' }}</div>
                    <div><strong>Địa điểm:</strong> {{ $suKien->dia_diem ?? 'Đang cập nhật' }}</div>
                    <div><strong>Mã vé:</strong> <span style="font-family:monospace;font-weight:700;color:#111827;">{{ $dangKy->ma_ve }}</span></div>
                  </td>
                </tr>
              </table>

              <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 auto 16px auto;">
                <tr>
                  <td align="center" style="border-radius:8px;background-color:#111827;">
                    <a href="{{ $eventUrl }}" target="_blank" rel="noopener" style="display:inline-block;padding:12px 24px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                      Xem chi tiết sự kiện
                    </a>
                  </td>
                  <td style="width:12px;"></td>
                  <td align="center" style="border-radius:8px;border:1px solid #d1d5db;background-color:#ffffff;">
                    <a href="{{ $myTicketUrl }}" target="_blank" rel="noopener" style="display:inline-block;padding:11px 20px;font-size:14px;font-weight:600;color:#111827;text-decoration:none;border-radius:8px;">
                      Vé của tôi
                    </a>
                  </td>
                </tr>
              </table>

              <p style="margin:16px 0 0 0;font-size:13px;line-height:1.6;color:#6b7280;">
                Hãy chuẩn bị sẵn mã QR trong mục "Vé của tôi" để check-in nhanh tại cổng sự kiện.
              </p>
            </td>
          </tr>

          <tr>
            <td style="background-color:#f9fafb;padding:16px 32px;text-align:center;border-top:1px solid #e5e7eb;">
              <p style="margin:0;font-size:12px;color:#9ca3af;">
                © {{ date('Y') }} {{ config('app.name', 'QQQ') }}. Email nhắc lịch tự động, vui lòng không phản hồi.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>
