<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingTable extends Migration
{
    public function up()
    {
        Schema::create('tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perizinan_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracking');
    }
}
