<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\DataTables;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa dengan pencarian dan filter.
     */
    public function index(Request $request)
    {
        $query = Siswa::query();

        // Filter pencarian jika ada
        if ($request->has('search') && $request->search !== '') {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
                ->orWhere('nisn', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kelas
        if ($request->has('kelas') && $request->kelas !== '') {
            $query->where('kelas', $request->kelas);
        }

        // Urutkan data terbaru di atas
        $siswa = $query->orderBy('created_at', 'desc')->get();

        // Ambil kelas yang unik
        $kelasList = Siswa::select('kelas')->distinct()->get();

        return view('siswa.index', compact('siswa', 'kelasList'));
    }

    /**
     * Menampilkan form tambah siswa.
     */
    public function create()
    {
        return view('siswa.create');
    }

    /**
     * Menyimpan data siswa baru.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nisn' => 'required',
            'kelas' => 'required',
        ]);
        try {
            // Cek apakah NISN sudah ada di database
            if (Siswa::where('nisn', $request->nisn)->exists()) {
                return redirect()->route('siswa.index')->with('error', 'NISN sudah terdaftar, silakan periksa kembali.');
            }
            // Simpan data siswa baru
            Siswa::create($request->all());

            return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('siswa.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Menampilkan form untuk edit siswa.
     */
    public function edit(Siswa $siswa)
    {
        return view('siswa.edit', compact('siswa'));
    }

    /**
     * Melakukan update data siswa.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nisn' => 'required',
            'kelas' => 'required',
        ]);

        // Cek jika ada siswa lain yang menggunakan NISN yang sama
        if (Siswa::where('nisn', $request->nisn)
            ->where('id_siswa', '!=', $siswa->id_siswa)
            ->exists()
        ) {
            return redirect()->route('siswa.index')->with('error', 'NISN sudah terdaftar, silakan periksa kembali.');
        }

        try {
            $siswa->update($request->all());
            return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('siswa.index')->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    /**
     * Menghapus data siswa.
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect('/siswa')->with('success', 'Data siswa berhasil dihapus');
    }

    /**
     * Fungsi Import Data Siswa dari Excel.
     * Jika data dengan NISN yang sama sudah ada, maka akan di-update.
     * Jika belum ada, maka akan dibuat data baru.
     */
    public function import(Request $request)
    {
        // 1. Validasi file Excel
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            // 2. Ambil file yang diupload dan baca menggunakan PhpSpreadsheet
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            $createdCount = 0;
            $updatedCount = 0;
            $failedRows = [];

            // 3. Looping data dari Excel, mulai dari baris kedua
            foreach (array_slice($data, 1) as $rowIndex => $row) {
                // ## PERUBAHAN DISINI ##
                // Sesuaikan dengan format baru: | No | NISN | Nama Lengkap | Jenis Kelamin | Kelas |
                // NISN sekarang ada di kolom B
                $nisn = $row['B'] ?? null; 

                // Skip baris jika NISN (kunci utama) kosong
                if (empty($nisn)) {
                    $failedRows[] = $rowIndex + 2;
                    continue;
                }

                try {
                    // 4. Gunakan updateOrCreate() dengan mapping kolom yang baru
                    $siswa = Siswa::updateOrCreate(
                        ['nisn' => $nisn],
                        [
                            'nama_lengkap'  => $row['C'] ?? null, // Kolom C
                            'jenis_kelamin' => $row['D'] ?? null, // Kolom D
                            'kelas'         => $row['E'] ?? null, // Kolom E
                        ]
                    );

                    // 5. Hitung data yang dibuat atau diupdate
                    if ($siswa->wasRecentlyCreated) {
                        $createdCount++;
                    } elseif ($siswa->wasChanged()) {
                        $updatedCount++;
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    $failedRows[] = $rowIndex + 2;
                }
            }

            // 6. Buat pesan feedback yang informatif
            $message = "Proses impor selesai. ";
            if ($createdCount > 0) {
                $message .= "$createdCount data baru ditambahkan. ";
            }
            if ($updatedCount > 0) {
                $message .= "$updatedCount data berhasil diperbarui. ";
            }
            if (empty($failedRows) && $createdCount == 0 && $updatedCount == 0) {
                return redirect()->route('siswa.index')->with('info', 'Tidak ada data yang diimpor atau diperbarui.');
            }

            if (!empty($failedRows)) {
                $message .= "Gagal mengimpor data pada baris: " . implode(', ', $failedRows) . ".";
                return redirect()->route('siswa.index')->with('warning', $message);
            }

            return redirect()->route('siswa.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('siswa.index')->with('error', 'Gagal membaca file Excel. Pastikan formatnya benar. Error: ' . $e->getMessage());
        }
    }

    /**
     * Mengambil data untuk DataTables.
     */
    public function getData(Request $request)
    {
        $query = \App\Models\Siswa::query();

        if ($request->has('kelas') && $request->kelas != '') {
            $query->where('kelas', $request->kelas);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editUrl = url('/siswa/' . $row->id_siswa . '/edit');
                $deleteUrl = url('/siswa/' . $row->id_siswa);
                return '
                <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button type="submit" onclick="return confirm(\'Hapus data ini?\')" class="btn btn-sm btn-danger">Hapus</button>
                </form>
            ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}