<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Siswa;
use App\Models\Chromebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        // Statistik jumlah
        $jumlahSiswa = Siswa::count();
        $jumlahDipinjam = Peminjaman::whereNull('waktu_pengembalian')->count();
        $jumlahChromebook = Chromebook::count();
        $jumlahDikembalikan = Peminjaman::whereNotNull('waktu_pengembalian')->count();


        // Query peminjaman untuk tabel riwayat
        $peminjamanQuery = Peminjaman::with(['siswa', 'guru', 'chromebook'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('siswa', function ($q) use ($search) {
                        $q->where('nama_lengkap', 'like', "%$search%");
                    })->orWhere('kode_chromebook', 'like', "%$search%");
                });
            })
            ->when($status, function ($query, $status) {
                if ($status === 'dipinjam') {
                    $query->whereNull('waktu_pengembalian');
                } elseif ($status === 'dikembalikan') {
                    $query->whereNotNull('waktu_pengembalian');
                }
            })
            ->orderBy('waktu_peminjaman', 'desc');

        $peminjaman = $peminjamanQuery->paginate(10)->appends($request->only('search', 'status'));

        // Data untuk grafik peminjaman per kelas
        $peminjamanPerKelas = DB::table('peminjaman')
            ->join('siswa', 'peminjaman.id_siswa', '=', 'siswa.id_siswa')
            ->select('siswa.kelas as label', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('siswa.kelas')
            ->orderBy('siswa.kelas', 'asc')
            ->get();

        $labels = $peminjamanPerKelas->pluck('label')->toArray();
        $data = $peminjamanPerKelas->pluck('jumlah')->toArray();

        return view('dashboard', compact(
            'jumlahSiswa',
            'jumlahDipinjam',
            'jumlahChromebook',
            'jumlahDikembalikan', // Tambahkan ini
            'peminjaman',
            'labels',
            'data'
        ));
    }
}
