<!-- resources/views/chromebook/edit.blade.php -->

@extends('layouts.app')

@section('title', 'Edit Chromebook')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Chromebook</h5>
        <small class="text-muted float-end">Perbarui data perangkat</small>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('chromebook.update', $chromebook->id_chromebook) }}">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label class="form-label" for="kode_chromebook">Kode Chromebook</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-barcode"></i></span>
              <input
                type="text"
                id="kode_chromebook"
                name="kode_chromebook"
                class="form-control"
                value="{{ old('kode_chromebook', $chromebook->kode_chromebook) }}"
                required
              />
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
                value="{{ old('merek', $chromebook->merek) }}"
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
                value="{{ old('nomor_loker', $chromebook->nomor_loker) }}"
                required
              />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Status</label>
            <input
              type="text"
              class="form-control"
              value="{{ $chromebook->status }}"
              disabled
            />
          </div>

          <button type="submit" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
