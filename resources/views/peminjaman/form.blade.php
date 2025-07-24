@extends('layouts.scan')

@section('title', 'Peminjaman Chromebook')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<style>
  .select2-container--default .select2-selection--single {
    border: 1px solid #d9dee3;
    border-radius: 0.375rem;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    background-color: #fff;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 1.5;
    padding-left: 0;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    top: 8px;
    right: 10px;
  }

  .input-group .select2-container {
    flex: 1 1 auto;
    width: 1% !important;
  }

  .input-group-text {
    background-color: #f5f5f9;
    border: 1px solid #d9dee3;
    border-right: none;
  }

  .input-group .select2-container--default .select2-selection--single {
    border-left: none;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Form Peminjaman Chromebook</h5>
        <small class="text-muted float-end">Isi data peminjaman</small>
      </div>
      <div class="card-body">
        <form action="{{ route('peminjaman.store') }}" method="POST">
          @csrf

          <!-- Kode Chromebook -->
          <div class="mb-3">
            <label class="form-label">Kode Chromebook</label>
            <p class="form-control-plaintext">{{ $chromebook->kode_chromebook }}</p>
            <input type="hidden" name="kode_chromebook" value="{{ $chromebook->kode_chromebook }}">
          </div>

          <!-- Nomor Loker -->
          <div class="mb-3">
            <label class="form-label">Nomor Loker</label>
            <p class="form-control-plaintext">{{ $chromebook->nomor_loker }}</p>
            <input type="hidden" name="nomor_loker" value="{{ $chromebook->nomor_loker }}">
          </div>

          <!-- Nama Siswa -->
          <div class="mb-3">
            <label class="form-label" for="nama_lengkap">Nama Siswa</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-user"></i></span>
              <select id="nama_lengkap" name="id_siswa" class="form-select">
                <option value="">Pilih Nama Siswa...</option>
                @foreach($siswa as $s)
                  <option value="{{ $s->id_siswa }}" data-kelas="{{ $s->kelas }}">{{ $s->nama_lengkap }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- Kelas -->
          <div class="mb-3">
            <label class="form-label" for="kelas">Kelas</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-building-house"></i></span>
              <input type="text" id="kelas" name="kelas" class="form-control" readonly>
            </div>
          </div>

          <!-- Guru Rekomendasi -->
          <div class="mb-3">
            <label class="form-label" for="guru">Guru Rekomendasi</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-chalkboard"></i></span>
              <select id="guru" name="id_guru" class="form-select">
                <option value="">Pilih Nama Guru...</option>
                @foreach($guru as $g)
                  <option value="{{ $g->id_guru }}" data-mata-pelajaran="{{ $g->jabatan }}">{{ $g->nama_guru }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- Mata Pelajaran -->
          <div class="mb-3">
            <label class="form-label" for="mata_pelajaran">Mata Pelajaran</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-book"></i></span>
              <input type="text" id="mata_pelajaran" name="mata_pelajaran" class="form-control" readonly>
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn btn-primary">Pinjam Chromebook</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function () {
    $('#nama_lengkap').select2({
      placeholder: "Pilih Nama Siswa...",
      allowClear: true,
      dropdownParent: $('#nama_lengkap').parent() // fix z-index with input-group
    });

    $('#guru').select2({
      placeholder: "Pilih Nama Guru...",
      allowClear: true,
      dropdownParent: $('#guru').parent()
    });

    $('#nama_lengkap').on('change', function () {
      const kelas = $(this).find('option:selected').data('kelas');
      $('#kelas').val(kelas);
    });

    $('#guru').on('change', function () {
      const pelajaran = $(this).find('option:selected').data('mata-pelajaran');
      $('#mata_pelajaran').val(pelajaran);
    });
  });
</script>
@endpush
