<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class GuruController extends Controller
{
    // Menampilkan semua data guru dengan pencarian dan filter
    public function index(Request $request)
    {
        $query = Guru::query();

        // Filter pencarian jika ada
        if ($request->has('search') && $request->search !== '') {
            $query->where('nama_guru', 'like', '%' . $request->search . '%')
                ->orWhere('nip', 'like', '%' . $request->search . '%');
        }

        // Urutkan data terbaru di atas
        $gurus = $query->orderBy('created_at', 'desc')->get();

        return view('guru.index', compact('gurus'));
    }

    // Menampilkan form tambah guru
    public function create()
    {
        return view('guru.create');
    }
    // Menyimpan data guru baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'jabatan' => 'required|string|max:100',
        ]);

        try {
            // Cek manual apakah NIP sudah ada
            if (Guru::where('nip', $request->nip)->exists()) {
                // Kirim pesan error ke halaman index
                return redirect()->route('guru.index')->with('error', 'Gagal menambahkan data karena NIP sudah ada.');
            }

            // Simpan data guru baru
            Guru::create($request->all());

            return redirect()->route('guru.index')->with('success', 'Data guru berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('guru.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    // Menampilkan form edit data guru
    public function edit(Guru $guru)
    {
        return view('guru.edit', compact('guru'));
    }

    // Menyimpan perubahan data guru
    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'jabatan' => 'required|string|max:100',
        ]);

        // Cek manual apakah ada guru lain yang sudah menggunakan NIP tersebut
        if (Guru::where('nip', $request->nip)
            ->where('id_guru', '!=', $guru->id_guru)
            ->exists()
        ) {
            return redirect()->route('guru.index')->with('error', 'Gagal memperbarui data karena NIP sudah digunakan oleh guru lain.');
        }

        try {
            $guru->update($request->all());
            return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('guru.index')->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }


    // Menghapus data guru
    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect('/guru')->with('success', 'Data guru berhasil dihapus');
    }

    // Fungsi Import Data Guru dari Excel
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
            return redirect()->route('guru.index')->with('error', 'File yang diupload tidak valid.');
        }

        // Membaca file Excel
        try {
            $spreadsheet = IOFactory::load($file);
        } catch (\Exception $e) {
            return redirect()->route('guru.index')->with('error', 'Gagal membaca file Excel. Pastikan file formatnya benar.');
        }

        // Ambil data dari sheet pertama
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Import data ke database, lewat loop
        foreach ($data as $index => $row) {
            if ($index == 0) continue; // Skip header

            // Validasi data dan simpan ke database
            try {
                Guru::create([
                    'nama_guru' => $row[0], // Kolom pertama = Nama Guru
                    'nip' => $row[1], // Kolom kedua = NIP
                    'jenis_kelamin' => $row[2], // Kolom ketiga = Jenis Kelamin
                    'jabatan' => $row[3], // Kolom keempat = Jabatan
                ]);
            } catch (\Exception $e) {
                // Tangani jika ada kesalahan pada data baris tertentu
                continue; // Anda bisa menambahkan log atau pemberitahuan jika ingin melaporkan kesalahan pada data
            }
        }

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diimpor!');
    }

    public function getData(Request $request)
    {
        $query = \App\Models\Guru::query();

        if ($request->jabatan) {
            $query->where('jabatan', $request->jabatan);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editUrl = route('guru.edit', $row->id_guru);
                $deleteUrl = route('guru.destroy', $row->id_guru);

                $csrf = csrf_field();
                $method = method_field('DELETE');

                $button = <<<HTML
                <a href="{$editUrl}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{$deleteUrl}" method="POST" style="display:inline-block" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                    {$csrf}
                    {$method}
                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                </form>
            HTML;

                return $button;
            })
            ->rawColumns(['aksi']) // supaya tombol HTML tidak di-escape
            ->make(true);
    }
}
