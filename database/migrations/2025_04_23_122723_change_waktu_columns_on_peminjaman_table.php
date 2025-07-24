<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dateTime('waktu_peminjaman')->nullable()->change();
            $table->dateTime('waktu_pengembalian')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('waktu_peminjaman')->nullable()->change();
            $table->string('waktu_pengembalian')->nullable()->change();
        });
    }
};
