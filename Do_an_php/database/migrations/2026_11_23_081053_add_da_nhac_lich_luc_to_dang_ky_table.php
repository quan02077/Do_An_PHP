<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột đánh dấu thời điểm gửi email nhắc lịch để tránh gửi trùng.
     */
    public function up(): void
    {
        Schema::table('dang_ky', function (Blueprint $table) {
            $table->timestamp('da_nhac_lich_luc')->nullable()->after('ghi_chu');
        });
    }

    /**
     * Rollback cột da_nhac_lich_luc.
     */
    public function down(): void
    {
        Schema::table('dang_ky', function (Blueprint $table) {
            $table->dropColumn('da_nhac_lich_luc');
        });
    }
};
