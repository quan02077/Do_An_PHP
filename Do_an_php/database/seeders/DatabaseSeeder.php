<?php

namespace Database\Seeders;

use App\Models\DanhMuc;
use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use App\Models\YeuThich;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tắt kiểm tra khóa ngoại để xóa dữ liệu cũ nếu chạy lại
        Schema::disableForeignKeyConstraints();
        YeuThich::truncate();
        DangKy::truncate();
        SuKien::truncate();
        DanhMuc::truncate();
        NguoiDung::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Tạo Người dùng mẫu (NguoiDung)
        $admin = NguoiDung::create([
            'ho_ten' => 'Ban Quản Trị QQQ',
            'email' => 'admin@qqq.vn',
            'mat_khau' => '123456',
            'so_dien_thoai' => '0901234567',
            'vai_tro' => 'admin',
            'trang_thai' => 'hoat_dong',
            'dia_chi' => 'Hà Nội',
            'tieu_su' => 'Đơn vị quản trị và kiểm duyệt các sự kiện toàn quốc.',
        ]);

        $user1 = NguoiDung::create([
            'ho_ten' => 'Nguyễn Minh Quân',
            'email' => 'quan@gmail.com',
            'mat_khau' => '123456',
            'so_dien_thoai' => '0988776655',
            'vai_tro' => 'user',
            'trang_thai' => 'hoat_dong',
            'dia_chi' => 'TP. Hồ Chí Minh',
            'tieu_su' => 'Yêu thích âm nhạc, công nghệ và các hoạt động tình nguyện.',
        ]);

        $user2 = NguoiDung::create([
            'ho_ten' => 'Trần Hoàng Nam',
            'email' => 'nam@gmail.com',
            'mat_khau' => '123456',
            'so_dien_thoai' => '0912345678',
            'vai_tro' => 'user',
            'trang_thai' => 'hoat_dong',
            'dia_chi' => 'Đà Nẵng',
            'tieu_su' => 'Chuyên viên phát triển phần mềm và đam mê chạy marathon.',
        ]);

        // 3. Tạo Danh mục sự kiện (DanhMuc) - 6 danh mục đáp ứng tiêu chí tối thiểu
        $dmCongNghe = DanhMuc::create([
            'ten_danh_muc' => 'Công nghệ',
            'mo_ta' => 'Hội thảo công nghệ, trí tuệ nhân tạo, lập trình và chuyển đổi số.',
            'trang_thai' => 'hoat_dong',
        ]);

        $dmAmNhac = DanhMuc::create([
            'ten_danh_muc' => 'Âm nhạc & Giải trí',
            'mo_ta' => 'Đêm nhạc acoustic, liveshow, festival và các lễ hội âm nhạc đỉnh cao.',
            'trang_thai' => 'hoat_dong',
        ]);

        $dmKhoiNghiep = DanhMuc::create([
            'ten_danh_muc' => 'Hội thảo & Khởi nghiệp',
            'mo_ta' => 'Diễn đàn đầu tư mạo hiểm, pitching dự án và kết nối doanh nhân.',
            'trang_thai' => 'hoat_dong',
        ]);

        $dmTheThao = DanhMuc::create([
            'ten_danh_muc' => 'Thể thao & Sức khỏe',
            'mo_ta' => 'Giải chạy marathon, thể thao điện tử, yoga và lối sống khỏe mạnh.',
            'trang_thai' => 'hoat_dong',
        ]);

        $dmNgheThuat = DanhMuc::create([
            'ten_danh_muc' => 'Nghệ thuật & Triển lãm',
            'mo_ta' => 'Triển lãm tranh, điêu khắc, nghệ thuật thị giác và thời trang.',
            'trang_thai' => 'hoat_dong',
        ]);

        $dmGiaoDuc = DanhMuc::create([
            'ten_danh_muc' => 'Giáo dục & Du học',
            'mo_ta' => 'Hội thảo săn học bổng, triển lãm du học quốc tế, tư vấn tuyển sinh và phát triển kỹ năng mềm.',
            'trang_thai' => 'hoat_dong',
        ]);

        // 4. Tạo Danh sách Sự kiện (SuKien)
        // Lưu ý nghiệp vụ: trang_thai trong CSDL lưu vòng đời ('nhap', 'cong_khai', 'da_huy')
        // Trạng thái theo thời gian ('sap_dien_ra', 'dang_dien_ra', 'da_ket_thuc') được tính động từ thời gian bắt đầu/kết thúc
        $eventsData = [
            [
                'danh_muc_id' => $dmCongNghe->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => 'Vietnam Tech Summit 2026: Tương Lai Kỷ Nguyên AI',
                'slug' => 'vietnam-tech-summit-2026',
                'mo_ta' => "Sự kiện công nghệ thường niên quy tụ hơn 50 diễn giả hàng đầu từ Google, Microsoft, VinAI cùng 2.000 kỹ sư công nghệ.\n\nNội dung chính:\n- Xu hướng ứng dụng Generative AI trong doanh nghiệp.\n- Bảo mật và Điện toán đám mây thế hệ mới.\n- Triển lãm sản phẩm công nghệ đột phá 2026.",
                'hinh_anh' => '001.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->addDays(5)->setHour(8)->setMinute(30),
                'thoi_gian_ket_thuc' => Carbon::now()->addDays(5)->setHour(17)->setMinute(0),
                'dia_diem' => 'Trung tâm Hội chợ và Triển lãm Sài Gòn (SECC), Q.7, TP.HCM',
                'ban_to_chuc' => 'Hiệp hội Công nghệ & Đổi mới sáng tạo VN',
                'so_luong_toi_da' => 500,
                'gia_ve' => 0,
                'trang_thai' => 'cong_khai',
                'noi_bat' => true,
            ],
            [
                'danh_muc_id' => $dmAmNhac->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => "Đêm Nhạc Acoustic 'Mùa Thu Cho Em'",
                'slug' => 'dem-nhac-acoustic-mua-thu-cho-em',
                'mo_ta' => "Một buổi tối lắng đọng cùng những thanh âm mộc mạc của guitar và piano, đưa bạn qua những bản tình ca vượt thời gian.\n\nNghệ sĩ tham gia:\n- Ban nhạc Mộc Acoustic\n- Ca sĩ khách mời đặc biệt từ Indie Underground.",
                'hinh_anh' => '002.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->subHours(1),
                'thoi_gian_ket_thuc' => Carbon::now()->addHours(3),
                'dia_diem' => 'Nhà Hát Lớn Hà Nội, Số 1 Tràng Tiền, Hoàn Kiếm, Hà Nội',
                'ban_to_chuc' => 'Nhà Hát Ca Múa Nhạc Trẻ',
                'so_luong_toi_da' => 150,
                'gia_ve' => 250000,
                'trang_thai' => 'cong_khai',
                'noi_bat' => true,
            ],
            [
                'danh_muc_id' => $dmKhoiNghiep->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => 'Startup Pitching & Funding Day 2026',
                'slug' => 'startup-pitching-funding-day-2026',
                'mo_ta' => "Cơ hội nhận gói vốn đầu tư lên đến 500.000 USD từ hơn 20 quỹ đầu tư mạo hiểm hàng đầu Đông Nam Á.\n\nCác đội thi sẽ có 5 phút thuyết trình trước hội đồng thẩm định và kết nối trực tiếp 1-1.",
                'hinh_anh' => '003.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->addDays(12)->setHour(9)->setMinute(0),
                'thoi_gian_ket_thuc' => Carbon::now()->addDays(12)->setHour(16)->setMinute(30),
                'dia_diem' => 'Dreamplex Coworking Space, Q. Bình Thạnh, TP.HCM',
                'ban_to_chuc' => 'Quỹ Khởi nghiệp Việt Nam (VSV)',
                'so_luong_toi_da' => 200,
                'gia_ve' => 150000,
                'trang_thai' => 'cong_khai',
                'noi_bat' => true,
            ],
            [
                'danh_muc_id' => $dmTheThao->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => "Giải Chạy Marathon 'Vì Nụ Cười Xanh 2026'",
                'slug' => 'giai-chay-marathon-vi-nu-cuoi-xanh-2026',
                'mo_ta' => "Giải chạy thường niên nâng cao tinh thần rèn luyện sức khỏe và quyên góp quỹ trồng cây phủ xanh đô thị.\n\nCự ly thi đấu: 5km, 10km và 21km (Bán Marathon). Áo chạy và huy chương hoàn thành cho toàn bộ vận động viên.",
                'hinh_anh' => '004.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->addDays(20)->setHour(5)->setMinute(0),
                'thoi_gian_ket_thuc' => Carbon::now()->addDays(20)->setHour(11)->setMinute(0),
                'dia_diem' => 'Công viên Yên Sở, Hoàng Mai, Hà Nội',
                'ban_to_chuc' => 'Green Smile Club & Liên đoàn Điền kinh',
                'so_luong_toi_da' => 1000,
                'gia_ve' => 350000,
                'trang_thai' => 'cong_khai',
                'noi_bat' => false,
            ],
            [
                'danh_muc_id' => $dmNgheThuat->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => "Triển Lãm Nghệ Thuật Số 'Ánh Sáng & Tương Lai'",
                'slug' => 'trien-lam-nghe-thuat-so-anh-sang-tuong-lai',
                'mo_ta' => "Không gian trải nghiệm nghệ thuật thị giác đa chiều với hệ thống máy chiếu 3D mapping và âm thanh vòm đỉnh cao.\n\nNơi nghệ thuật thị giác giao thoa cùng công nghệ thực tế ảo.",
                'hinh_anh' => '005.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->subDays(1)->setHour(10)->setMinute(0),
                'thoi_gian_ket_thuc' => Carbon::now()->addDays(14)->setHour(21)->setMinute(0),
                'dia_diem' => 'Bảo tàng Mỹ thuật TP.HCM, Số 97A Phó Đức Chính, Q.1, TP.HCM',
                'ban_to_chuc' => 'CLB Nghệ Thuật Hiện Đại Sài Gòn',
                'so_luong_toi_da' => 300,
                'gia_ve' => 80000,
                'trang_thai' => 'cong_khai',
                'noi_bat' => false,
            ],
            [
                'danh_muc_id' => $dmCongNghe->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => 'Workshop: Xây Dựng Ứng Dụng Web Hiện Đại với Laravel & Vue.js',
                'slug' => 'workshop-xay-dung-ung-dung-web-laravel-vuejs',
                'mo_ta' => "Buổi thực hành trực tiếp cầm tay chỉ việc giúp bạn nắm vững kiến thức xây dựng ứng dụng web hiện đại, kiến trúc MVC, RESTful API và tối ưu database.",
                'hinh_anh' => '006.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->addDays(7)->setHour(14)->setMinute(0),
                'thoi_gian_ket_thuc' => Carbon::now()->addDays(7)->setHour(17)->setMinute(30),
                'dia_diem' => 'Hội trường B1, Trường Đại học Bách Khoa, Hai Bà Trưng, Hà Nội',
                'ban_to_chuc' => 'Cộng đồng Lập trình viên Việt Nam',
                'so_luong_toi_da' => 120,
                'gia_ve' => 0,
                'trang_thai' => 'cong_khai',
                'noi_bat' => true,
            ],
            [
                'danh_muc_id' => $dmAmNhac->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => 'Lễ Hội Ẩm Thực & Âm Nhạc Đường Phố 2026',
                'slug' => 'le-hoi-am-thuc-am-nhac-duong-pho-2026',
                'mo_ta' => "Hơn 80 gian hàng ẩm thực đặc sản ba miền cùng sân khấu ca nhạc đường phố sôi động kéo dài suốt 3 ngày cuối tuần.",
                'hinh_anh' => '007.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->subHours(4),
                'thoi_gian_ket_thuc' => Carbon::now()->addDays(2),
                'dia_diem' => 'Khu du lịch Văn Thánh, 48/10 Điện Biên Phủ, Bình Thạnh, TP.HCM',
                'ban_to_chuc' => 'Ban Quản Lý Du Lịch & Văn Hóa Ẩm Thực',
                'so_luong_toi_da' => 800,
                'gia_ve' => 50000,
                'trang_thai' => 'cong_khai',
                'noi_bat' => false,
            ],
            [
                'danh_muc_id' => $dmKhoiNghiep->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => 'Diễn Đàn Doanh Nhân Trẻ & Chuyển Đổi Số',
                'slug' => 'dien-dan-doanh-nhan-tre-chuyen-doi-so',
                'mo_ta' => "Sự kiện đã kết thúc tốt đẹp với hơn 300 doanh nghiệp tham dự và 15 biên bản hợp tác chiến lược được ký kết.",
                'hinh_anh' => '008.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->subDays(10)->setHour(8)->setMinute(0),
                'thoi_gian_ket_thuc' => Carbon::now()->subDays(10)->setHour(17)->setMinute(0),
                'dia_diem' => 'Khách sạn Sheraton Sài Gòn, 88 Đồng Khởi, Q.1, TP.HCM',
                'ban_to_chuc' => 'Hội Doanh Nhân Trẻ TP.HCM',
                'so_luong_toi_da' => 300,
                'gia_ve' => 500000,
                'trang_thai' => 'cong_khai',
                'noi_bat' => false,
            ],
            [
                'danh_muc_id' => $dmTheThao->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => 'Giải Đấu Thể Thao Điện Tử Sinh Viên Toàn Quốc',
                'slug' => 'giai-dau-the-thao-dien-tu-sinh-vien-toan-quoc',
                'mo_ta' => "Vòng chung kết toàn quốc các bộ môn Esports được mong chờ nhất với tổng giải thưởng lên đến 200.000.000 VNĐ.",
                'hinh_anh' => '009.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->subDays(20)->setHour(9)->setMinute(0),
                'thoi_gian_ket_thuc' => Carbon::now()->subDays(18)->setHour(20)->setMinute(0),
                'dia_diem' => 'Nhà thi đấu Hồ Xuân Hương, Số 2 Hồ Xuân Hương, Q.3, TP.HCM',
                'ban_to_chuc' => 'Hội Thể thao Điện tử & Giải trí Việt Nam',
                'so_luong_toi_da' => 600,
                'gia_ve' => 0,
                'trang_thai' => 'cong_khai',
                'noi_bat' => false,
            ],
            [
                'danh_muc_id' => $dmAmNhac->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => 'Đêm Hòa Nhạc Giao Hưởng Mùa Xuân',
                'slug' => 'dem-hoa-nhac-giao-huong-mua-xuan',
                'mo_ta' => "Thưởng thức các bản giao hưởng bất hủ của Beethoven, Mozart và Tchaikovsky dưới sự chỉ huy của nhạc trưởng quốc tế.",
                'hinh_anh' => '010.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->addDays(8)->setHour(19)->setMinute(30),
                'thoi_gian_ket_thuc' => Carbon::now()->addDays(8)->setHour(22)->setMinute(0),
                'dia_diem' => 'Nhà Hát Thành Phố, Số 7 Công Trường Lam Sơn, Q.1, TP.HCM',
                'ban_to_chuc' => 'Dàn nhạc Giao hưởng Vũ kịch TP.HCM (HBSO)',
                'so_luong_toi_da' => 250,
                'gia_ve' => 400000,
                'trang_thai' => 'cong_khai',
                'noi_bat' => true,
            ],
            // 11. Sự kiện ĐẦY CHỖ (Hết vé) - Dành riêng để test/demo nghiệp vụ hết vé
            [
                'danh_muc_id' => $dmGiaoDuc->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => 'Hội Thảo Hướng Nghiệp & Học Bổng Toàn Phần Du Học 2026',
                'slug' => 'hoi-thao-huong-nghiep-hoc-bong-toan-phan-du-hoc-2026',
                'mo_ta' => "Sự kiện đặc biệt giới thiệu hơn 100 suất học bổng toàn phần từ các trường đại học hàng đầu Anh, Úc, Mỹ và Canada.\n\nSự kiện đã kín toàn bộ số lượng chỗ tham gia của đợt 1.",
                'hinh_anh' => '006.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->addDays(9)->setHour(8)->setMinute(30),
                'thoi_gian_ket_thuc' => Carbon::now()->addDays(9)->setHour(12)->setMinute(0),
                'dia_diem' => 'Khách sạn Caravelle Sài Gòn, 19 Công Trường Lam Sơn, Q.1, TP.HCM',
                'ban_to_chuc' => 'Tổ Chức Giáo Dục Quốc Tế & Hợp Tác Du Học',
                'so_luong_toi_da' => 2, // Chỉ giới hạn 2 chỗ để test đầy vé (100%)
                'gia_ve' => 0,
                'trang_thai' => 'cong_khai',
                'noi_bat' => true,
            ],
            // 12. Sự kiện ĐÃ HỦY - Dành riêng để test/demo nghiệp vụ hủy sự kiện
            [
                'danh_muc_id' => $dmAmNhac->id,
                'nguoi_tao_id' => $admin->id,
                'ten_su_kien' => '[ĐÃ HỦY] Festival Âm Nhạc Bãi Biển Mùa Hè 2026',
                'slug' => 'festival-am-nhac-bai-bien-mua-he-2026-da-huy',
                'mo_ta' => "THÔNG BÁO HỦY CHÍNH THỨC: Do điều kiện thời tiết mưa bão bất thường và để đảm bảo an toàn tuyệt đối cho người tham gia, Ban Tổ Chức xin thông báo chính thức hủy sự kiện này. Toàn bộ người đăng ký sẽ được xử lý hoàn tiền hoặc nhận voucher ưu tiên cho sự kiện tiếp theo.",
                'hinh_anh' => '002.jpg',
                'thoi_gian_bat_dau' => Carbon::now()->addDays(14)->setHour(16)->setMinute(0),
                'thoi_gian_ket_thuc' => Carbon::now()->addDays(14)->setHour(23)->setMinute(0),
                'dia_diem' => 'Bãi Sau, TP. Vũng Tàu, Bà Rịa - Vũng Tàu',
                'ban_to_chuc' => 'Công Ty Giải Trí Sóng Xanh',
                'so_luong_toi_da' => 1000,
                'gia_ve' => 300000,
                'trang_thai' => 'da_huy',
                'noi_bat' => false,
            ],
        ];

        $createdEvents = [];
        foreach ($eventsData as $item) {
            $createdEvents[] = SuKien::create($item);
        }

        // 5. Tạo Vé đăng ký mẫu (DangKy)
        // Vé cho Sự kiện Tech Summit (Sự kiện 0)
        DangKy::create([
            'nguoi_dung_id' => $user1->id,
            'su_kien_id' => $createdEvents[0]->id,
            'ma_ve' => 'QQQ-2026-TECH01',
            'trang_thai' => 'da_xac_nhan',
            'ghi_chu' => 'Đăng ký vé tham dự hội thảo công nghệ AI.',
        ]);

        DangKy::create([
            'nguoi_dung_id' => $user2->id,
            'su_kien_id' => $createdEvents[0]->id,
            'ma_ve' => 'QQQ-2026-TECH02',
            'trang_thai' => 'da_xac_nhan',
        ]);

        // Vé cho Sự kiện Acoustic Night (Sự kiện 1)
        DangKy::create([
            'nguoi_dung_id' => $user1->id,
            'su_kien_id' => $createdEvents[1]->id,
            'ma_ve' => 'QQQ-2026-ACOU01',
            'trang_thai' => 'da_xac_nhan',
            'ghi_chu' => 'Vé hàng ghế VIP.',
        ]);

        // Vé cho Sự kiện Marathon (Sự kiện 3)
        DangKy::create([
            'nguoi_dung_id' => $user2->id,
            'su_kien_id' => $createdEvents[3]->id,
            'ma_ve' => 'QQQ-2026-MARA01',
            'trang_thai' => 'da_xac_nhan',
        ]);

        // LÀM ĐẦY CHỖ Sự kiện 10 (Hội thảo du học - max 2 chỗ): Đăng ký đủ 2 người (100% full)
        DangKy::create([
            'nguoi_dung_id' => $user1->id,
            'su_kien_id' => $createdEvents[10]->id,
            'ma_ve' => 'QQQ-2026-DUHOC1',
            'trang_thai' => 'da_xac_nhan',
            'ghi_chu' => 'Học bổng du học Anh Quốc.',
        ]);

        DangKy::create([
            'nguoi_dung_id' => $user2->id,
            'su_kien_id' => $createdEvents[10]->id,
            'ma_ve' => 'QQQ-2026-DUHOC2',
            'trang_thai' => 'da_xac_nhan',
            'ghi_chu' => 'Học bổng du học Úc.',
        ]);

        // Vé của Sự kiện Đã Hủy (Sự kiện 11): Có 1 vé của user2 ở trạng thái 'da_huy'
        DangKy::create([
            'nguoi_dung_id' => $user2->id,
            'su_kien_id' => $createdEvents[11]->id,
            'ma_ve' => 'QQQ-2026-FEST01',
            'trang_thai' => 'da_huy',
            'ghi_chu' => 'Vé đã hủy do sự kiện bị hủy bởi ban tổ chức.',
        ]);

        // 6. Tạo Danh sách Yêu thích mẫu (YeuThich)
        YeuThich::create([
            'nguoi_dung_id' => $user1->id,
            'su_kien_id' => $createdEvents[0]->id,
        ]);

        YeuThich::create([
            'nguoi_dung_id' => $user1->id,
            'su_kien_id' => $createdEvents[1]->id,
        ]);

        YeuThich::create([
            'nguoi_dung_id' => $user1->id,
            'su_kien_id' => $createdEvents[2]->id,
        ]);

        YeuThich::create([
            'nguoi_dung_id' => $user1->id,
            'su_kien_id' => $createdEvents[10]->id, // Yêu thích sự kiện du học
        ]);

        YeuThich::create([
            'nguoi_dung_id' => $user2->id,
            'su_kien_id' => $createdEvents[3]->id,
        ]);

        $this->command->info('Đã nạp thành công toàn bộ dữ liệu mẫu (Users, 6 Danh mục, 12 Sự kiện gồm Đầy chỗ & Đã hủy, Vé và Yêu thích)!');
    }
}
