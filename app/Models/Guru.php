<?php

// app/Models/Guru.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    // Tentukan nama tabel jika tidak mengikuti konvensi
    protected $table = 'guru';  // Pastikan nama tabel di database sesuai

    // Tentukan primary key (kolom id di tabel guru)
    protected $primaryKey = 'id_guru';  // Sesuaikan dengan kolom primary key di tabel

    // Nonaktifkan penggunaan timestamps jika tidak diperlukan
    public $timestamps = true;  // Set true jika Anda ingin menggunakan timestamps (created_at dan updated_at)

    // Tentukan kolom-kolom yang dapat diisi massal
    protected $fillable = [
        'nama_guru',
        'nip',
        'jenis_kelamin',
        'jabatan',
    ];

    // Jika Anda tidak menggunakan timestamps, Anda bisa menonaktifkan properti ini:
    // public $timestamps = false; 

    // Jika menggunakan "soft deletes", Anda bisa menambahkan properti berikut:
    // use Illuminate\Database\Eloquent\SoftDeletes;
    // protected $dates = ['deleted_at'];
}
