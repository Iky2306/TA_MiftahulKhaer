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
        if (!Schema::hasTable('master_siswa')) {
            Schema::create('master_siswa', function (Blueprint $table) {
                $table->id();
                $table->string('nisn', 20)->nullable();
                $table->string('nama_siswa', 100);
                $table->string('kode_kelas', 50);
                $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->timestamps();

                $table->foreign('kode_kelas')
                      ->references('kode_kelas')
                      ->on('master_kelas')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_siswa');
    }
};
