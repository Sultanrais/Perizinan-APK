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
        Schema::create('persyaratans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->boolean('wajib')->default(true);
            $table->string('tipe_file')->nullable(); // pdf, image, doc, etc
            $table->integer('max_size')->nullable(); // in KB
            $table->timestamps();
        });

        // Pivot table untuk perizinan dan persyaratan
        Schema::create('perizinan_persyaratan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perizinan_id')->constrained()->onDelete('cascade');
            $table->foreignId('persyaratan_id')->constrained()->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinan_persyaratan');
        Schema::dropIfExists('persyaratans');
    }
};
