<!-- resources/views/chromebook/create.blade.php -->
@extends('layouts.app')

@section('title', 'Tambah Chromebook')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Form Tambah Chromebook</h5>
        <small class="text-muted float-end">Input data perangkat</small>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('chromebook.store') }}">
          @csrf

          <div class="mb-3">
    <label class="form-label" for="nip">Kode Chromebook</label>
    <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="bx bx-id-card"></i></span>
        <input
            type="text"
            id="kode_chromebook"
            name="kode_chromebook"
            class="form-control @error('kode_chromebook') is-invalid @enderror"
            placeholder="Contoh: CB-0001"
            value="{{ old('kode_chromebook') }}"
            required
        />
        @error('kode_chromebook')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

          <div class="mb-3">
            <label class="form-label" for="merek">Merek</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-laptop"></i></span>
              <input
                type="text"
                id="merek"
                name="merek"
                class="form-control"
                placeholder="Contoh: Acer, Axio, Zyrex"
                value="{{ old('merek') }}"
                required
              />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="nomor_loker">Nomor Loker</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
              <input
                type="text"
                id="nomor_loker"
                name="nomor_loker"
                class="form-control"
                placeholder="Contoh: 16"
                value="{{ old('nomor_loker') }}"
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
