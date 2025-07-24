<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    // Nama tabel
    protected $table = 'siswa';

    // Primary key tabel
    protected $primaryKey = 'id_siswa';

    // Jika tabel menggunakan kolom created_at & updated_at
    public $timestamps = true;

    // Kolom-kolom yang bisa diisi secara massal
    protected $fillable = [
        'nama_lengkap', 
        'jenis_kelamin', 
        'nisn', 
        'nik', 
        'kelas',
    ];

    // Relasi: satu siswa bisa melakukan banyak peminjaman
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_siswa', 'id_siswa');
    }

    // Optional: Anda bisa menambahkan fungsi untuk mengatur format data lainnya, jika perlu
    // Contohnya: Menangani format atau transformasi data sebelum disimpan atau diambil
}
