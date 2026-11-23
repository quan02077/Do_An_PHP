<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng danh_gia lưu đánh giá của người đã tham gia sau khi sự kiện kết thúc.
     */
    public function up(): void
    {
        Schema::create('danh_gia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
            $table->foreignId('su_kien_id')->constrained('su_kien')->cascadeOnDelete();
            $table->unsignedTinyInteger('so_sao');
            $table->text('noi_dung')->nullable();
            $table->string('trang_thai')->default('hien');
            $table->timestamps();

            $table->unique(['nguoi_dung_id', 'su_kien_id']);
        });
    }

    /**
     * Rollback bảng danh_gia.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_gia');
    }
};
