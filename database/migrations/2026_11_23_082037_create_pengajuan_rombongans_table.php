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
        Schema::create('pengajuan_rombongan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->unique()->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_sekolah');
            $table->string('penanggung_jawab');
            $table->string('no_hp', 20);
            $table->string('mode_tiket')->default('per_peserta');
            $table->string('status')->default('diajukan')->index();
            $table->text('catatan_admin')->nullable();
            $table->timestamp('ditinjau_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_rombongan');
    }
};
