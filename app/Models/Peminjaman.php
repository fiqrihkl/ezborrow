<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';

    protected $fillable = [
        'kode_chromebook',
        'id_siswa',
        'id_guru',
        'waktu_peminjaman',
        'waktu_pengembalian',
    ];

    // Format waktu secara otomatis sebagai objek DateTime
    protected $casts = [
        'waktu_peminjaman' => 'datetime',
        'waktu_pengembalian' => 'datetime',
    ];

    // Format tanggal saat dikirim sebagai JSON (global override)
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d/m/Y H:i:s');
    }

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    // Relasi ke guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    // Relasi ke chromebook
    public function chromebook()
    {
        return $this->belongsTo(Chromebook::class, 'kode_chromebook', 'kode_chromebook');
    }
}
