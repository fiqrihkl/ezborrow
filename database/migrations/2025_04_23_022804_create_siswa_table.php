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
        Schema::create('siswa', function (Blueprint $table) {
    $table->id('id_siswa');
    $table->string('nama_lengkap');
    $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
    $table->string('nisn')->unique();
    $table->string('nik')->unique();
    $table->string('kelas');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
