#!/usr/bin/env bash
# Kiểm thử đồng thời trên MySQL/MariaDB thật.
#
#   bash tests/concurrency/run.sh            # chạy toàn bộ kịch bản, tự tạo và xóa DB test
#   bash tests/concurrency/run.sh --keep-db  # giữ lại DB test sau khi chạy để xem dữ liệu
#
# Biến môi trường tùy chọn: PHP, MYSQL, DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD,
# CONC_DB (mặc định qqq_concurrency_test; bắt buộc kết thúc bằng _test).
# Không đọc/ghi .env của bạn: cấu hình DB được ghi đè bằng biến môi trường.
set -u

HERE="$(cd "$(dirname "$0")" && pwd)"
PROJECT="$(cd "$HERE/../.." && pwd)"
PHP="${PHP:-$( [ -x /c/xampp/php/php.exe ] && echo /c/xampp/php/php.exe || echo php )}"
MYSQL="${MYSQL:-$( [ -x /c/xampp/mysql/bin/mysql.exe ] && echo /c/xampp/mysql/bin/mysql.exe || echo mysql )}"
CONC_DB="${CONC_DB:-qqq_concurrency_test}"
KEEP_DB=0; [ "${1:-}" = "--keep-db" ] && KEEP_DB=1

case "$CONC_DB" in *_test) ;; *) echo "CONC_DB phải kết thúc bằng _test (đang là '$CONC_DB')." >&2; exit 2 ;; esac

export DB_CONNECTION=mysql DB_DATABASE="$CONC_DB" \
       DB_HOST="${DB_HOST:-127.0.0.1}" DB_PORT="${DB_PORT:-3306}" \
       DB_USERNAME="${DB_USERNAME:-root}" DB_PASSWORD="${DB_PASSWORD:-}" \
       APP_ENV=testing SESSION_DRIVER=array CACHE_STORE=array \
       BROADCAST_CONNECTION=log QUEUE_CONNECTION=sync
export MYSQL_PWD="$DB_PASSWORD"

sql() { "$MYSQL" -u"$DB_USERNAME" -h"$DB_HOST" -P"$DB_PORT" -N "$@"; }
count_tickets() { sql "$CONC_DB" -e "SELECT COUNT(*) FROM dang_ky WHERE su_kien_id=$1 AND trang_thai='$2'"; }

TMP="$(mktemp -d)"
cleanup() {
  rm -rf "$TMP"
  if [ "$KEEP_DB" = 0 ]; then sql -e "DROP DATABASE IF EXISTS \`$CONC_DB\`" 2>/dev/null; fi
}
trap cleanup EXIT

if [ -f "$PROJECT/bootstrap/cache/config.php" ]; then
  echo "Có bootstrap/cache/config.php: biến môi trường sẽ bị bỏ qua. Chạy 'php artisan config:clear' trước." >&2
  exit 2
fi

echo "MySQL: $(sql -e 'SELECT VERSION()')  |  DB test: $CONC_DB"
sql -e "DROP DATABASE IF EXISTS \`$CONC_DB\`; CREATE DATABASE \`$CONC_DB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" || exit 1
(cd "$PROJECT" && "$PHP" artisan migrate --force -q) || { echo "migrate thất bại" >&2; exit 1; }

FAILED=0

# scenario <tên> <mode> <capacity> <workers> <delayMs> <same_user 0|1> <trạng_thái_cần_đếm> <số_mong_đợi>
scenario() {
  local name=$1 mode=$2 cap=$3 n=$4 delay=$5 same=$6 status=$7 expected=$8
  local nusers=$n; [ "$same" = 1 ] && nusers=1

  local setup; setup=$("$PHP" "$HERE/setup.php" "$cap" "$nusers") || { echo "setup lỗi: $setup"; FAILED=1; return; }
  local ev admin users arg actor=""
  ev=$(echo "$setup" | sed -E 's/.*"event":([0-9]+).*/\1/')
  admin=$(echo "$setup" | sed -E 's/.*"admin":([0-9]+).*/\1/')
  read -r -a users <<< "$(echo "$setup" | sed -E 's/.*"users":\[([^]]*)\].*/\1/' | tr ',' ' ')"
  arg=$ev

  if [ "$mode" = checkin ]; then
    # Đặt sẵn một vé, sau đó n tiến trình admin cùng check-in đúng mã vé đó.
    "$PHP" "$HERE/worker.php" book "${users[0]}" "$ev" 0 0 > /dev/null
    arg=$(sql "$CONC_DB" -e "SELECT ma_ve FROM dang_ky WHERE su_kien_id=$ev LIMIT 1")
    actor=$admin
  fi

  local dir="$TMP/$name"; mkdir -p "$dir"
  local start; start=$("$PHP" -r 'printf("%.6f", microtime(true) + 8);')
  for i in $(seq 0 $((n - 1))); do
    local u=${actor:-${users[$i]:-}}
    [ "$same" = 1 ] && u=${users[0]}
    # Mỗi tiến trình ghi file riêng: ghi nối cùng một file từ nhiều tiến trình trên Windows bị mất dòng.
    "$PHP" "$HERE/worker.php" "$mode" "$u" "$arg" "$start" "$delay" > "$dir/w$i.json" 2>&1 &
  done
  wait

  local actual; actual=$(count_tickets "$ev" "$status")
  local verdict="PASS"; [ "$actual" = "$expected" ] || verdict="FAIL"
  local errors; errors=$(cat "$dir"/w*.json | grep -c '"error"\|"status":5')
  [ "$errors" = 0 ] || verdict="FAIL"
  if [ "$mode" = naive ]; then
    verdict="ĐỐI CHỨNG (vượt sức chứa là đúng kỳ vọng)"; expected="> $cap"
  elif [ "$verdict" = FAIL ]; then
    FAILED=1
  fi

  echo
  echo "### $name"
  echo "    mode=$mode sức_chứa=$cap tiến_trình=$n trễ=${delay}ms cùng_user=$same"
  cat "$dir"/w*.json | sed -E 's/,"ms":[0-9]+//' | sort | uniq -c | sed 's/^/    /'
  echo "    thời gian phản hồi (ms): $(cat "$dir"/w*.json | grep -o '"ms":[0-9]*' | cut -d: -f2 | sort -n | sed -n '1p;$p' | paste -sd'-')"
  echo "    DB: $status = $actual (mong đợi $expected, lỗi 5xx/exception = $errors) => $verdict"
}

# Đối chứng: bỏ khóa thì PHẢI bán vượt (nếu không, harness không đủ tin cậy).
scenario "Đối chứng: không lockForUpdate, 20 user / 5 chỗ" naive 5 20 300 0 da_xac_nhan 5
CONTROL_OVERSOLD=$(count_tickets 1 da_xac_nhan)

scenario "A1: 20 user / 5 chỗ, nới cửa sổ race 300ms" book 5 20 300 0 da_xac_nhan 5
scenario "A2: 30 user / 1 chỗ, không thêm trễ"       book 1 30 0   0 da_xac_nhan 1
scenario "B: 1 user gửi 10 yêu cầu cùng lúc"          book 100 10 0 1 da_xac_nhan 1
scenario "C: 10 admin check-in cùng một mã vé"        checkin 10 10 0 0 da_tham_gia 1

# D: user hủy vé đúng lúc admin quét QR check-in cùng vé đó.
# Tiến trình hủy ngủ 1500ms sau khi đọc dòng vé; check-in xuất phát trễ hơn 300ms.
# Đúng: chỉ MỘT bên thành công và trạng thái cuối khớp với bên đó. Sai: cả hai báo thành công.
race_cancel_checkin() {
  local name="D: hủy vé song song với check-in cùng vé"
  local setup; setup=$("$PHP" "$HERE/setup.php" 10 1) || { echo "setup lỗi"; FAILED=1; return; }
  local ev admin user
  ev=$(echo "$setup" | sed -E 's/.*"event":([0-9]+).*/\1/')
  admin=$(echo "$setup" | sed -E 's/.*"admin":([0-9]+).*/\1/')
  user=$(echo "$setup" | sed -E 's/.*"users":\[([0-9]+).*/\1/')
  "$PHP" "$HERE/worker.php" book "$user" "$ev" 0 0 > /dev/null
  local tid code
  tid=$(sql "$CONC_DB" -e "SELECT id FROM dang_ky WHERE su_kien_id=$ev LIMIT 1")
  code=$(sql "$CONC_DB" -e "SELECT ma_ve FROM dang_ky WHERE id=$tid")

  local dir="$TMP/race_cd"; mkdir -p "$dir"
  local start; start=$("$PHP" -r 'printf("%.6f", microtime(true) + 8);')
  local later; later=$("$PHP" -r 'printf("%.6f", $argv[1] + 0.3);' "$start")
  "$PHP" "$HERE/worker.php" cancel "$user" "$tid" "$start" 1500 > "$dir/cancel.json" 2>&1 &
  "$PHP" "$HERE/worker.php" checkin "$admin" "$code" "$later" 0 > "$dir/checkin.json" 2>&1 &
  wait

  local final; final=$(sql "$CONC_DB" -e "SELECT trang_thai FROM dang_ky WHERE id=$tid")
  local cancel_ok=0 checkin_ok=0
  grep -q '"result":"success"' "$dir/cancel.json" && cancel_ok=1
  grep -q '"ok":true' "$dir/checkin.json" && checkin_ok=1
  local verdict=FAIL
  if [ "$cancel_ok" = 1 ] && [ "$checkin_ok" = 0 ] && [ "$final" = da_huy ]; then verdict=PASS; fi
  if [ "$cancel_ok" = 0 ] && [ "$checkin_ok" = 1 ] && [ "$final" = da_tham_gia ]; then verdict=PASS; fi
  [ "$verdict" = PASS ] || FAILED=1

  echo
  echo "### $name"
  echo "    hủy    : $(sed -E 's/,"ms":[0-9]+//' "$dir/cancel.json")"
  echo "    check-in: $(sed -E 's/,"ms":[0-9]+//' "$dir/checkin.json")"
  echo "    DB: trạng thái cuối = $final (hủy thành công=$cancel_ok, check-in thành công=$checkin_ok) => $verdict"
}
race_cancel_checkin

echo
if [ "$CONTROL_OVERSOLD" -le 5 ]; then
  echo "CẢNH BÁO: nhóm đối chứng không bán vượt ($CONTROL_OVERSOLD/5) — kết quả các kịch bản khác chưa đủ chứng minh."
  FAILED=1
else
  echo "Đối chứng bán vượt $CONTROL_OVERSOLD/5 vé như dự kiến => harness bắt được race."
fi
[ "$FAILED" = 0 ] && echo "KẾT QUẢ: ĐẠT" || echo "KẾT QUẢ: KHÔNG ĐẠT"
exit "$FAILED"
