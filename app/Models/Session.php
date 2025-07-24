<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    // Tentukan nama tabel jika tidak mengikuti konvensi
    protected $table = 'sessions';

    // Tentukan primary key (kolom id di tabel sessions)
    protected $primaryKey = 'id';

    // Nonaktifkan penggunaan timestamps jika tidak diperlukan
    public $timestamps = false;

    // Tentukan kolom-kolom yang dapat diisi massal
    protected $fillable = [
        'user_id', // kolom untuk relasi ke user
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
    ];

    // Relasi ke model User
    public function user()
    {
        // Menggunakan id_user sebagai foreign key di tabel sessions
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
