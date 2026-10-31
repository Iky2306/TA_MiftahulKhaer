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
        if (!Schema::hasColumn('master_kelas', 'kurikulum')) {
            Schema::table('master_kelas', function (Blueprint $table) {
                $table->string('kurikulum', 100)->default('Kurikulum Merdeka')->after('wali_kelas_nik');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('master_kelas', 'kurikulum')) {
            Schema::table('master_kelas', function (Blueprint $table) {
                $table->dropColumn('kurikulum');
            });
        }
    }
};
