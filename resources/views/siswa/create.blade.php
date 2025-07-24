@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Form Tambah Siswa</h5>
        <small class="text-muted float-end">Masukkan data siswa</small>
      </div>
      <div class="card-body">
        <form method="POST" action="/siswa">
          @csrf

          <div class="mb-3">
            <label class="form-label" for="nama_lengkap">Nama Lengkap</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-user"></i></span>
              <input
                type="text"
                class="form-control"
                id="nama_lengkap"
                name="nama_lengkap"
                placeholder="Nama Lengkap"
                value="{{ old('nama_lengkap') }}"
                required
              />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><svg  xmlns="http://www.w3.org/2000/svg"  width="15"  height="15"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-friends"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 22v-5l-1 -1v-4a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4l-1 1v5" /><path d="M17 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 22v-4h-2l2 -6a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1l2 6h-2v4" /></svg></span>
              <select class="form-select" name="jenis_kelamin" id="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
    <label class="form-label" for="nip">NISN</label>
    <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="bx bx-id-card"></i></span>
        <input
            type="number"
            id="nisn"
            name="nisn"
            class="form-control @error('nisn') is-invalid @enderror"
            placeholder="Nomor Induk Siswa Nasional"
            value="{{ old('nisn') }}"
            required
        />
        @error('nisn')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

          <div class="mb-3">
            <label class="form-label" for="nik">NIK</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-credit-card"></i></span>
              <input
                type="number"
                id="nik"
                name="nik"
                class="form-control"
                placeholder="Nomor Induk Kependudukan"
                value="{{ old('nik') }}"
                required
              />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="kelas">Kelas</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-building-house"></i></span>
              <input
                type="text"
                id="kelas"
                name="kelas"
                class="form-control"
                placeholder="Contoh: 7 A"
                value="{{ old('kelas') }}"
                required
              />
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
