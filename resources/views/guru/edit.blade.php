<!-- resources/views/guru/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Guru')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Data Guru</h5>
        <small class="text-muted float-end">Perbarui informasi guru</small>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('guru.update', $guru->id_guru) }}">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label class="form-label" for="nama_guru">Nama Guru</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-user"></i></span>
              <input
                type="text"
                id="nama_guru"
                name="nama_guru"
                class="form-control"
                value="{{ old('nama_guru', $guru->nama_guru) }}"
                required
              />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="nip">NIP</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-id-card"></i></span>
              <input
                type="text"
                id="nip"
                name="nip"
                class="form-control"
                value="{{ old('nip', $guru->nip) }}"
                required
              />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><svg  xmlns="http://www.w3.org/2000/svg"  width="15"  height="15"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-friends"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 22v-5l-1 -1v-4a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4l-1 1v5" /><path d="M17 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 22v-4h-2l2 -6a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1l2 6h-2v4" /></svg></i></span>
              <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                <option value="Laki-laki" {{ $guru->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ $guru->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="jabatan">Pengampuh Pelajaran</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-book"></i></span>
              <input
                type="text"
                id="jabatan"
                name="jabatan"
                class="form-control"
                value="{{ old('jabatan', $guru->jabatan) }}"
                required
              />
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
