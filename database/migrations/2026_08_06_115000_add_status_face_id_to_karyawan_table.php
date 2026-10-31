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
        if (!Schema::hasColumn('karyawan', 'status_face_id')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->char('status_face_id', 1)->default('1')->after('status_jam_kerja');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('karyawan', 'status_face_id')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropColumn('status_face_id');
            });
        }
    }
};
