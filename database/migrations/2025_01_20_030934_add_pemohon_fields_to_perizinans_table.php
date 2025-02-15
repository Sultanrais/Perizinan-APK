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
        Schema::table('perizinans', function (Blueprint $table) {
            $table->string('nama_pemohon')->after('user_id');
            $table->string('nik', 16)->after('nama_pemohon');
            $table->text('alamat')->after('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perizinans', function (Blueprint $table) {
            $table->dropColumn(['nama_pemohon', 'nik', 'alamat']);
        });
    }
};
