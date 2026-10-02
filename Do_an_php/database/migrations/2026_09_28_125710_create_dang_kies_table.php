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
        Schema::create('dang_ky', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->foreignId('su_kien_id')->constrained('su_kien')->onDelete('restrict');
            $table->string('ma_ve')->unique();
            $table->timestamp('thoi_gian_dang_ky')->useCurrent();
            $table->string('trang_thai')->default('da_xac_nhan');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
            $table->unique(['nguoi_dung_id', 'su_kien_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dang_ky');
    }
};
