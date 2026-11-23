<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tiket_paket_id')->constrained('tiket_paket')->restrictOnDelete();
            $table->foreignId('diskon_id')->nullable()->constrained('diskon')->nullOnDelete();
            $table->string('kategori')->index();
            $table->date('tanggal_kunjungan');
            $table->unsignedSmallInteger('jumlah_tiket');
            $table->unsignedInteger('harga_satuan');
            $table->unsignedInteger('potongan_diskon')->default(0);
            $table->unsignedInteger('total_bayar');
            $table->string('status')->default('menunggu_pembayaran')->index();
            $table->timestamps();

            $table->index(['tiket_paket_id', 'tanggal_kunjungan', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
