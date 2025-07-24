<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chromebook;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Peminjaman;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index()
    {
        return view('peminjaman.index'); // Halaman scan QR
    }

    public function result(Request $request)
    {
        $kodeChromebook = $request->input('kode_chromebook');
        $chromebook = Chromebook::where('kode_chromebook', $kodeChromebook)->first();

        if ($chromebook) {
            // Jika Chromebook sedang dipinjam, arahkan ke halaman pengembalian
            if ($chromebook->status === 'Dipinjam') {
                return redirect()->route('peminjaman.return', ['kode_chromebook' => $kodeChromebook]);
            }

            // Jika tersedia, tampilkan form peminjaman
            $siswa = Siswa::all();
            $guru = Guru::all();
            return view('peminjaman.form', compact('chromebook', 'siswa', 'guru'));
        }

        return redirect()->route('peminjaman.index')->with('error', 'Chromebook tidak ditemukan.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_chromebook' => 'required',
            'id_siswa' => 'required',
            'id_guru' => 'required',
        ]);

        $sudahMeminjam = Peminjaman::where('id_siswa', $request->id_siswa)
            ->whereNull('waktu_pengembalian')
            ->exists();

        if ($sudahMeminjam) {
            return redirect()->route('peminjaman.index')
                ->with('error', 'Setiap siswa hanya bisa meminjam 1 Chromebook. Kembalikan dulu sebelum meminjam lagi.');
        }

        $chromebook = Chromebook::where('kode_chromebook', $request->kode_chromebook)->first();

        if ($chromebook && $chromebook->status === 'Tersedia') {
            Peminjaman::create([
                'kode_chromebook' => $request->kode_chromebook,
                'id_siswa' => $request->id_siswa,
                'id_guru' => $request->id_guru,
                'waktu_peminjaman' => now(),
            ]);

            $chromebook->update(['status' => 'Dipinjam']);

            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil. Silakan gunakan Chromebook dengan bijak.');
        }

        return redirect()->route('peminjaman.index')
            ->with('error', 'Gagal melakukan peminjaman. Chromebook tidak tersedia atau sedang dipinjam.');
    }

    public function showForm(Request $request)
    {
        $kodeChromebook = $request->kode_chromebook;
        $chromebook = Chromebook::where('kode_chromebook', $kodeChromebook)->first();
        $siswa = Siswa::all();
        $guru = Guru::all();

        if ($chromebook) {
            return view('peminjaman.form', compact('chromebook', 'siswa', 'guru'));
        }

        return redirect()->route('peminjaman.index')->with('error', 'Chromebook tidak ditemukan.');
    }

    public function showReturnForm($kode_chromebook)
    {
        $chromebook = Chromebook::where('kode_chromebook', $kode_chromebook)->first();

        if (!$chromebook) {
            return redirect()->route('peminjaman.index')->with('error', 'Chromebook tidak ditemukan.');
        }

        $peminjaman = Peminjaman::with('siswa')
            ->where('kode_chromebook', $kode_chromebook)
            ->whereNull('waktu_pengembalian')
            ->first();

        if (!$peminjaman) {
            return redirect()->route('peminjaman.index')
                ->with('error', 'Chromebook belum dipinjam atau sudah dikembalikan.');
        }

        $namaSiswa = $peminjaman->siswa->nama_lengkap ?? 'Tidak diketahui';

        return view('peminjaman.return', compact('chromebook', 'peminjaman', 'namaSiswa'));
    }

    public function returnChromebook(Request $request)
    {
        $request->validate(['kode_chromebook' => 'required']);

        $chromebook = Chromebook::where('kode_chromebook', $request->kode_chromebook)->first();
        $peminjaman = Peminjaman::where('kode_chromebook', $request->kode_chromebook)
            ->whereNull('waktu_pengembalian')
            ->first();

        if (!$peminjaman) {
            return redirect()->route('peminjaman.index')
                ->with('error', 'Gagal mengembalikan. Chromebook belum dipinjam atau sudah dikembalikan.');
        }

        $peminjaman->update(['waktu_pengembalian' => now()]);
        $chromebook->update(['status' => 'Tersedia']);

        return redirect()->route('peminjaman.index');
    }

    public function getData(Request $request)
    {
        $query = Peminjaman::with(['siswa', 'guru', 'chromebook']);

        // Filter berdasarkan kelas siswa
        if ($request->filled('kelas')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }

        // Filter berdasarkan status (Dipinjam/Dikembalikan)
        if ($request->filled('status')) {
            if ($request->status === 'Dipinjam') {
                $query->whereNull('waktu_pengembalian');
            } elseif ($request->status === 'Dikembalikan') {
                $query->whereNotNull('waktu_pengembalian');
            }
        }

        // Filter berdasarkan waktu peminjaman (hanya tanggal)
        if ($request->filled('waktu_peminjaman')) {
            try {
                $tanggal = Carbon::parse($request->waktu_peminjaman)->format('Y-m-d');
                $query->whereDate('waktu_peminjaman', $tanggal);
            } catch (\Exception $e) {
                // Abaikan jika format tanggal tidak valid
            }
        }

        // Filter berdasarkan rentang tanggal peminjaman
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            try {
                $mulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
                $selesai = Carbon::parse($request->tanggal_selesai)->endOfDay();
                $query->whereBetween('waktu_peminjaman', [$mulai, $selesai]);
            } catch (\Exception $e) {
                // Abaikan jika salah satu tanggal tidak valid
            }
        }

        // // (Opsional) Filter berdasarkan waktu pengembalian (jika ada)
        // if ($request->filled('waktu_pengembalian')) {
        //     try {
        //         $tanggalPengembalian = Carbon::parse($request->waktu_pengembalian)->format('Y-m-d');
        //         $query->whereDate('waktu_pengembalian', $tanggalPengembalian);
        //     } catch (\Exception $e) {
        //         // Abaikan jika format tidak valid
        //     }
        // }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                return $row->waktu_pengembalian
                    ? '<span class="badge bg-success">Dikembalikan</span>'
                    : '<span class="badge bg-danger">Dipinjam</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }
}
