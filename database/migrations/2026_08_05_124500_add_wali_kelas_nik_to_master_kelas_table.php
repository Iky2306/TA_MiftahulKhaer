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
        if (!Schema::hasColumn('master_kelas', 'wali_kelas_nik')) {
            Schema::table('master_kelas', function (Blueprint $table) {
                $table->string('wali_kelas_nik')->nullable()->after('qr_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('master_kelas', 'wali_kelas_nik')) {
            Schema::table('master_kelas', function (Blueprint $table) {
                $table->dropColumn('wali_kelas_nik');
            });
        }
    }
};
