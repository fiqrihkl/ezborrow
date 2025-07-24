<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel users (admin)
        Schema::create('users', function (Blueprint $table) {
    $table->id('id_user'); // sesuai konvensi kamu
    $table->string('name');
    $table->string('email')->unique();
    $table->string('username')->unique(); // tambah ini
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
        // Insert akun admin ke dalam tabel users
        DB::table('users')->insert([
            'name' => 'Admin', // Nama admin
            'email' => 'admin@example.com', // Email admin
            'username' => 'admin', // username admin
            'password' => Hash::make('password123'), // Password admin (hash)
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tabel reset password
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabel sessions (login tracking)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('id_user')->nullable()->index(); // ganti dari user_id
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
