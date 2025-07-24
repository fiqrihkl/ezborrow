<?php

// app/Models/Chromebook.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chromebook extends Model
{
    use HasFactory;

    // Tentukan nama tabel dan primary key jika tidak sesuai dengan konvensi
    protected $table = 'chromebook';
    protected $primaryKey = 'id_chromebook';
    
    // Tentukan kolom yang bisa diisi massal
    protected $fillable = [
        'kode_chromebook',
        'merek',
        'nomor_loker',
        'status'
    ];
    
    // Tentukan bahwa kita ingin menggunakan timestamps
    public $timestamps = true;
}
