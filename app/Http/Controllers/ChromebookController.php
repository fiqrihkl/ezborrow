<?php

// app/Http/Controllers/ChromebookController.php

namespace App\Http\Controllers;

use App\Models\Chromebook;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class ChromebookController extends Controller
{
    // Menampilkan daftar Chromebook
    public function index(Request $request)
    {
        $query = Chromebook::query();

        //Filter pencarian
        if ($request->has('search') && $request->search !== '') {
            $query->where('kode_chromebook', 'like', '%' . $request->search . '%')
                ->orWhere('merek', 'like', '%' . $request->search . '%');
        }

        // Urutkan data terbaru di atas
        $chromebooks = $query->orderBy('created_at', 'desc')->get();

        return view('chromebook.index', compact('chromebooks'));
    }

    // Menampilkan form untuk tambah Chromebook
    public function create()
    {
        return view('chromebook.create');
    }

    // Menyimpan data chromebook baru
    public function store(Request $request)
    {
        // Cek duplikasi kode
        if (Chromebook::where('kode_chromebook', $request->kode_chromebook)->exists()) {
            return redirect()->route('chromebook.index')->with('error', 'Gagal menambahkan data karena Kode Chromebook sudah ada.');
        }

        // Validasi manual selain unique
        $request->validate([
            'kode_chromebook' => 'required',
            'merek' => 'required',
            'nomor_loker' => 'required',
        ]);

        // Simpan data
        Chromebook::create([
            'kode_chromebook' => $request->kode_chromebook,
            'merek' => $request->merek,
            'nomor_loker' => $request->nomor_loker,
            'status' => 'Tersedia',
        ]);

        return redirect()->route('chromebook.index')->with('success', 'Data Chromebook berhasil ditambahkan.');
    }


    // Menampilkan form untuk edit data Chromebook
    public function edit(Chromebook $chromebook)
    {
        return view('chromebook.edit', compact('chromebook'));
    }

    public function update(Request $request, Chromebook $chromebook)
    {
        // Validasi input, memastikan kode_chromebook unik kecuali untuk yang sedang diupdate
        $request->validate([
            'kode_chromebook' => 'required',
            'merek' => 'required',
            'nomor_loker' => 'required',
        ]);

        // Cek jika kode chromebook sudah ada
        if (Chromebook::where('kode_chromebook', $request->kode_chromebook)
            ->where('id_chromebook', '!=', $chromebook->id_chromebook)
            ->exists()
        ) {
            return redirect()->route('chromebook.index')->with('error', 'Gagal memperbarui data karena Kode Chromebook sudah tersedia');
        }

        try {
            $chromebook->update($request->all());
            return redirect()->route('chromebook.index')->with('success', 'Data chromebook berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('chromebook.index')->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }


    // fungisi hapus data
    public function destroy(Chromebook $chromebook)
    {
        // Hapus data Chromebook
        $chromebook->delete();

        // Redirect ke halaman daftar Chromebook dengan pesan sukses
        return redirect('/chromebook')->with('success', 'Chromebook berhasil dihapus');
    }
    // Fungsi Import Data Chromebook dari Excel
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
            return redirect()->route('chromebook.index')->with('error', 'File yang diupload tidak valid.');
        }

        // Membaca file Excel
        try {
            $spreadsheet = IOFactory::load($file);
        } catch (\Exception $e) {
            return redirect()->route('chromebook.index')->with('error', 'Gagal membaca file Excel. Pastikan file formatnya benar.');
        }

        // Ambil data dari sheet pertama
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Import data ke database, lewat loop
        foreach ($data as $index => $row) {
            if ($index == 0) continue; // Skip header

            // Validasi data dan simpan ke database
            try {
                Chromebook::create([
                    'kode_chromebook' => $row[0],
                    'merek' => $row[1],
                    'nomor_loker' => $row[2],
                    'status' => $row[3],
                ]);
            } catch (\Exception $e) {
                // Tangani jika ada kesalahan pada data baris tertentu
                continue; // Anda bisa menambahkan log atau pemberitahuan jika ingin melaporkan kesalahan pada data
            }
        }

        return redirect()->route('chromebook.index')->with('success', 'Data chromebook berhasil diimpor!');
    }

    public function getData(Request $request)
    {
        $query = \App\Models\Chromebook::query();

        // Filter Merek
        if ($request->merek) {
            $query->where('merek', $request->merek);
        }

        // Filter Loker
        if ($request->loker) {
            $query->where('nomor_loker', $request->loker);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editUrl = route('chromebook.edit', $row->id_chromebook);
                $deleteUrl = route('chromebook.destroy', $row->id_chromebook);

                // Tombol aksi langsung di dalam controller
                return '<a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                    <form action="' . $deleteUrl . '" method="POST" style="display:inline">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" onclick="return confirm(\'Apakah Anda yakin ingin menghapus?\')" class="btn btn-sm btn-danger">Hapus</button>
                    </form>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
