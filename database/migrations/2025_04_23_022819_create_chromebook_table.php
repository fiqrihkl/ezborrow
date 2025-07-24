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
        Schema::create('chromebook', function (Blueprint $table) {
    $table->id('id_chromebook');
    $table->string('kode_chromebook')->unique();
    $table->string('merek');
    $table->string('nomor_loker');
    $table->enum('status', ['Tersedia', 'Dipinjam']);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chromebook');
    }
};
