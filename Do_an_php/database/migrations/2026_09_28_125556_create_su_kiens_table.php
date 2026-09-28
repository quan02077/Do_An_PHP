<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('su_kien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('danh_muc_id')->constrained('danh_muc')->onDelete('cascade');
            $table->foreignId('nguoi_tao_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->string('ten_su_kien');
            $table->string('slug')->unique();
            $table->longText('mo_ta')->nullable();
            $table->string('hinh_anh')->nullable();
            $table->dateTime('thoi_gian_bat_dau');
            $table->dateTime('thoi_gian_ket_thuc');
            $table->string('dia_chi')->nullable();
            $table->string('ban_to_chuc')->nullable();
            $table->integer('so_luong_toi_da')->default(0);
            $table->decimal('gia_ve', 12, 2)->default(0);
            $table->string('trang_thai')->default('sap_dien_ra');
            $table->boolean('noi_bat')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('su_kien');
    }
};
