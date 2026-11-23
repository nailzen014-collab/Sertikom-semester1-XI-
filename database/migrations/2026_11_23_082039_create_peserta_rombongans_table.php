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
        Schema::create('peserta_rombongan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->string('nama');
            $table->string('kelas')->nullable();
            $table->timestamps();
        });

        Schema::table('tiket', function (Blueprint $table) {
            $table->foreign('peserta_id')
                ->references('id')
                ->on('peserta_rombongan')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->dropForeign(['peserta_id']);
        });

        Schema::dropIfExists('peserta_rombongan');
    }
};
