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
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perizinan_id')->constrained('perizinans')->onDelete('cascade');
            $table->string('jenis_dokumen'); // KTP, SIUP, Bukti Pembayaran, dll
            $table->string('nama_file');
            $table->string('path');
            $table->string('ekstensi');
            $table->integer('ukuran')->comment('dalam bytes');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
