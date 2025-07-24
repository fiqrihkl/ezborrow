<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\DataTables;

class SiswaController extends Controller
{
    // Menampilkan daftar siswa dengan pencarian dan filter
    public function index(Request $request)
    {
        $query = Siswa::query();

        // Filter pencarian jika ada
        if ($request->has('search') && $request->search !== '') {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
                ->orWhere('nisn', 'like', '%' . $request->search . '%')
                ->orWhere('nik', 'like', '%' . $request->search . '%');
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



    // Menampilkan form tambah siswa
    public function create()
    {
        return view('siswa.create');
    }

    // Menyimpan data siswa baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nisn' => 'required',
            'nik' => 'required',
            'kelas' => 'required',
        ]);
        try {
            // Cek apakah NISN sudah ada di database
            if (Siswa::where('nisn', $request->nisn)->exists()) {
                //kirim pesan eror ke index
                return redirect()->route('siswa.index')->with('error', 'NISN sudah terdaftar, silakan periksa kembali.');
            }
            // Simpan data siswa baru
            Siswa::create($request->all());

            return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('siswa.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    // Menampilkan form untuk edit siswa
    public function edit(Siswa $siswa)
    {
        return view('siswa.edit', compact('siswa'));
    }

    // Melakukan update data siswa
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nisn' => 'required',
            'nik' => 'required',
            'kelas' => 'required',
        ]);

        // Cek jika ada siswa lain yang menggunakan NISN yang sama
        if (Siswa::where('nisn', $request->nisn)
            ->where('id_siswa', '!=', $siswa->id_siswa)
            ->exists()
        ) {
            return redirect()->route('siswa.index')->with('error', 'NISN sudah terdaftar, silakan periksa kembali.');
        }

        // Cek jika ada siswa lain yang menggunakan NIK yang sama
        if (Siswa::where('nik', $request->nik)
            ->where('id_siswa', '!=', $siswa->id_siswa)
            ->exists()
        ) {
            return redirect()->route('siswa.index')->with('error', 'NIK sudah terdaftar, silakan periksa kembali.');
        }

        try {
            $siswa->update($request->all());
            return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('siswa.index')->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }


    // Menghapus data siswa
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect('/siswa')->with('success', 'Data siswa berhasil dihapus');
    }

    // Fungsi Import Data Siswa dari Excel
    public function import(Request $request)
    {
        // Validasi file Excel
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,csv',
        ]);

        // Ambil file yang diupload
        $file = $request->file('excel_file');

        // Pastikan file tidak kosong
        if (!$file->isValid()) {
            return redirect()->route('siswa.index')->with('error', 'File yang diupload tidak valid.');
        }

        // Membaca file Excel
        try {
            $spreadsheet = IOFactory::load($file);
        } catch (\Exception $e) {
            return redirect()->route('siswa.index')->with('error', 'Gagal membaca file Excel. Pastikan file formatnya benar.');
        }

        // Ambil data dari sheet pertama
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Import data ke database, lewat loop
        foreach ($data as $index => $row) {
            if ($index == 0) continue; // Skip header

            // Validasi data dan simpan ke database
            try {
                Siswa::create([
                    'nama_lengkap' => $row[0], // Kolom pertama = nama_lengkap
                    'jenis_kelamin' => $row[1], // Kolom kedua = jenis_kelamin
                    'nisn' => $row[2], // Kolom ketiga = nisn
                    'nik' => $row[3], // Kolom keempat = nik
                    'kelas' => $row[4], // Kolom kelima = kelas
                ]);
            } catch (\Exception $e) {
                // Tangani jika ada kesalahan pada data baris tertentu
                continue; // Anda bisa menambahkan log atau pemberitahuan jika ingin melaporkan kesalahan pada data
            }
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diimpor!');
    }

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
