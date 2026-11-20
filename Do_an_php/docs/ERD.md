# ERD hệ thống QQQ

Các bảng nghiệp vụ dùng tên tiếng Việt theo migration. `dang_ky` và `yeu_thich` là hai bảng nối giữa người dùng và sự kiện.

```mermaid
erDiagram
    NGUOI_DUNG ||--o{ SU_KIEN : tao
    DANH_MUC ||--o{ SU_KIEN : phan_loai
    NGUOI_DUNG ||--o{ DANG_KY : dang_ky
    SU_KIEN ||--o{ DANG_KY : co_ve
    NGUOI_DUNG ||--o{ YEU_THICH : luu
    SU_KIEN ||--o{ YEU_THICH : duoc_yeu_thich

    NGUOI_DUNG {
        bigint id PK
        string ho_ten
        string email UK
        string mat_khau
        string vai_tro
    }
    DANH_MUC {
        bigint id PK
        string ten_danh_muc
        string trang_thai
    }
    SU_KIEN {
        bigint id PK
        bigint danh_muc_id FK
        bigint nguoi_tao_id FK
        string ten_su_kien
        datetime thoi_gian_bat_dau
        datetime thoi_gian_ket_thuc
        int so_luong_toi_da
        string trang_thai
    }
    DANG_KY {
        bigint id PK
        bigint nguoi_dung_id FK
        bigint su_kien_id FK
        string ma_ve UK
        string trang_thai
    }
    YEU_THICH {
        bigint id PK
        bigint nguoi_dung_id FK
        bigint su_kien_id FK
    }
```

Ràng buộc duy nhất: `nguoi_dung.email`, `su_kien.slug`, `dang_ky.ma_ve`, cặp `(dang_ky.nguoi_dung_id, dang_ky.su_kien_id)` và cặp `(yeu_thich.nguoi_dung_id, yeu_thich.su_kien_id)`.
