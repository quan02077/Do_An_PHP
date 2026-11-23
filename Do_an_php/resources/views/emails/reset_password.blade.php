<!doctype html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đặt lại mật khẩu — {{ config('app.name', 'QQQ') }}</title>
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
                Yêu cầu đặt lại mật khẩu
              </h1>
              <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#374151;">
                Xin chào <strong>{{ $user->ho_ten ?? 'bạn' }}</strong>,
              </p>
              <p style="margin:0 0 24px 0;font-size:15px;line-height:1.6;color:#374151;">
                Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản <strong>{{ $user->email }}</strong> tại {{ config('app.name', 'QQQ') }}. Nhấn vào nút bên dưới để tạo mật khẩu mới:
              </p>

              <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 auto 24px auto;">
                <tr>
                  <td align="center" style="border-radius:8px;background-color:#111827;">
                    <a href="{{ $url }}" target="_blank" rel="noopener" style="display:inline-block;padding:12px 28px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                      Đặt lại mật khẩu
                    </a>
                  </td>
                </tr>
              </table>

              <p style="margin:0 0 12px 0;font-size:14px;line-height:1.6;color:#4b5563;">
                Liên kết này có hiệu lực trong vòng <strong>{{ $expire }} phút</strong> và chỉ sử dụng được một lần.
              </p>
              <p style="margin:0 0 24px 0;font-size:14px;line-height:1.6;color:#4b5563;">
                Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này. Tài khoản của bạn vẫn an toàn và không có thay đổi nào được thực hiện.
              </p>

              <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">

              <p style="margin:0 0 8px 0;font-size:12px;line-height:1.5;color:#6b7280;">
                Nếu nút bấm không hoạt động, hãy sao chép và dán đường dẫn sau vào trình duyệt:
              </p>
              <p style="margin:0;font-size:12px;line-height:1.5;word-break:break-all;">
                <a href="{{ $url }}" style="color:#2563eb;text-decoration:underline;">{{ $url }}</a>
              </p>
            </td>
          </tr>

          <tr>
            <td style="background-color:#f9fafb;padding:16px 32px;text-align:center;border-top:1px solid #e5e7eb;">
              <p style="margin:0;font-size:12px;color:#9ca3af;">
                © {{ date('Y') }} {{ config('app.name', 'QQQ') }}. Email được gửi tự động, vui lòng không phản hồi.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>
