@extends('layouts.app')

@section('title', 'Daftar Peminjaman Chromebook')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Statistik --}}
  <div class="row">
    @foreach ([
      ['label' => 'Jumlah Siswa', 'value' => $jumlahSiswa, 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>', 'color' => 'bg-primary'],
      ['label' => 'Jumlah Chromebook', 'value' => $jumlahChromebook, 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19l18 0" /><path d="M5 6m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" /></svg>', 'color' => 'bg-success'],
      ['label' => 'Dipinjam', 'value' => $jumlahDipinjam, 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 10v11l-5 -3l-5 3v-11a3 3 0 0 1 3 -3h4a3 3 0 0 1 3 3z" /><path d="M11 3h5a3 3 0 0 1 3 3v11" /></svg>', 'color' => 'bg-warning'],
      ['label' => 'Dikembalikan', 'value' => $jumlahDikembalikan, 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3.06 13a9 9 0 1 0 .49 -4.087" /><path d="M3 4.001v5h5" /><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>', 'color' => 'bg-info'],
    ] as $item)
    <div class="col-md-6 col-xl-3 mb-3 d-flex">
      <div class="card text-white shadow card-hover {{ $item['color'] }} h-100 w-100">
        <div class="card-body d-flex align-items-center justify-content-center" style="gap: 1rem; padding: 1.5rem;">
          <div class="icon-hover">
            {!! $item['svg'] !!}
          </div>
          <div class="text-start">
            <div>{{ $item['label'] }}</div>
            <div style="font-size: 2rem; font-weight: bold;">{{ $item['value'] }}</div>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Grafik --}}
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card h-100">
        <div class="card-body px-0">
          <div class="d-flex p-4 pt-3">
            <div class="fs-5 fw-bold">Sirkulasi Peminjaman Chromebook</div>
          </div>
          <div id="incomeChart" style="min-height: 350px;"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabel Data Peminjaman --}}
  <div class="card">
    <h5 class="card-header">Data Peminjaman Chromebook</h5>
    <div class="card-body">

      {{-- Export + Filter + Search --}}
      <div class="row mb-3 align-items-center">
        <div class="col-md-4 mb-2" id="exportButtons"></div>

        <div class="col-md-4 mb-2">
          <select id="filterKelas" class="form-select">
            <option value="">Filter Kelas</option>
            @php $kelasList = $peminjaman->pluck('siswa.kelas')->unique()->sort(); @endphp
            @foreach($kelasList as $kelas)
              @if($kelas)
                <option value="{{ $kelas }}">{{ $kelas }}</option>
              @endif
            @endforeach
          </select>
        </div>

        <div class="col-md-4 mb-2">
          <select id="filterStatus" class="form-select">
            <option value="">Filter Status</option>
            <option value="Dipinjam">Dipinjam</option>
            <option value="Dikembalikan">Dikembalikan</option>
          </select>
        </div>
        
        <div class="col-md-4 mb-2">
          <input type="search" id="searchBox" class="form-control" placeholder="Cari Data...">
        </div>

        <div class="col-md-4 mb-2">
          <input type="text" id="filterWaktuPeminjaman" class="form-control datepicker" placeholder="Filter Waktu Peminjaman">
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-md-3">
          <label>Show Entries
            <select id="showEntries" class="form-select form-select-sm">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </label>
        </div>
      </div>

      {{-- Table --}}
      <div class="table-responsive text-nowrap">
        <table id="peminjaman-table" class="table table-bordered table-striped" style="width:100%">
          <thead>
            <tr class="text-nowrap">
              <th>No</th>
              <th>Nama Siswa</th>
              <th>Kelas</th>
              <th>Guru</th>
              <th>Mata Pelajaran</th>
              <th>Kode Chromebook</th>
              <th>Nomor Loker</th>
              <th>Waktu Pinjam</th>
              <th>Waktu Kembali</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>

</div>

{{-- CSS Card Hover --}}
<style>
  .card-hover:hover {
    transform: translateY(-5px) scale(1.03);
  }
  .icon-hover:hover {
    transform: scale(1.1);
  }
  div.dataTables_filter {
  display: none;
}

</style>

{{-- Script ApexCharts --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  var labels = @json($labels);
  var data = @json($data);

  var options = {
    chart: {
      type: 'area',
      height: 300,
      id: 'incomeChart',
      toolbar: { show: false },
      animations: {
        enabled: true,
        easing: 'easeinout',
        speed: 800,
      }
    },
    series: [{
      name: 'Jumlah Peminjaman',
      data: data
    }],
    xaxis: {
      categories: labels,
    },
    colors: ['#00BFFF'],
    stroke: {
      curve: 'smooth',
      width: 3,
      dropShadow: {
        enabled: true,
        top: 5,
        blur: 4,
        opacity: 0.3
      }
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.4,
        opacityTo: 0,
        stops: [0, 90, 100]
      }
    },
    tooltip: {
      theme: 'light',
      y: {
        formatter: function (val) {
          return val + " peminjaman";
        }
      }
    }
  };

  setTimeout(() => {
    var chart = new ApexCharts(document.querySelector("#incomeChart"), options);
    chart.render();
  }, 200);
});
</script>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script>
$(document).ready(function() {
  // Initialize Datepicker
  $('#filterWaktuPeminjaman').datepicker({
    format: 'yyyy-mm-dd', // Format date sesuai kebutuhan
    autoclose: true,
  });

  var table = $('#peminjaman-table').DataTable({
    processing: true,
    serverSide: true,
    dom: 'Bfrtip',  // Menampilkan tombol export
    pageLength: 10,
    lengthChange: false,
    searching: true,
    order: [[7, 'desc']],
    ajax: {
      url: '{{ route("peminjaman.data") }}',
      data: function(d) {
        d.kelas = $('#filterKelas').val();
        d.status = $('#filterStatus').val();
        d.waktu_peminjaman = $('#filterWaktuPeminjaman').val();  // Kirim tanggal yang dipilih
      }
    },
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
      { data: 'siswa.nama_lengkap', name: 'siswa.nama_lengkap' },
      { data: 'siswa.kelas', name: 'siswa.kelas' },
      { data: 'guru.nama_guru', name: 'guru.nama_guru' },
      { data: 'guru.jabatan', name: 'guru.jabatan' },
      { data: 'kode_chromebook', name: 'kode_chromebook' },
      { data: 'chromebook.nomor_loker', name: 'chromebook.nomor_loker' },
      { data: 'waktu_peminjaman', name: 'waktu_peminjaman' },
      { data: 'waktu_pengembalian', name: 'waktu_pengembalian' },
      { data: 'status', name: 'status', orderable: false, searchable: false }
    ],
    buttons: [
      {
        extend: 'excelHtml5',
        title: 'DATA PEMINJAMAN CHROMEBOOK',
        className: 'btn btn-success me-2',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
        },
        customize: function (xlsx) {
        var sheet = xlsx.xl.worksheets['sheet1.xml'];
        // Menambahkan border pada setiap sel
        var rows = sheet.getElementsByTagName('row');
          for (var i = 0; i < rows.length; i++) {
          var row = rows[i];
            var cells = row.getElementsByTagName('c');
            for (var j = 0; j < cells.length; j++) {
            var cell = cells[j];
            cell.setAttribute('s', '25');
            }
          }
        }
      },
      {
        extend: 'pdfHtml5',
        title: 'DATA PEMINJAMAN CHROMEBOOK',
        orientation: 'landscape',
        pageSize: 'A4',
        className: 'btn btn-danger me-2',
        exportOptions: {
          columns: ':visible',
          modifier: {
            selected: true
          }
        },
        customize: function (doc) {
                // Mengatur ukuran font untuk header dan konten
                doc.styles.tableHeader.fillColor = '#10bc69';
                doc.styles.tableHeader.color = 'white';
                doc.styles.tableHeader.fontSize = 10;
                doc.defaultStyle.fontSize = 9;
                // Menambahkan border pada setiap sel dan memastikan garis vertikal ada
                doc.content[1].table.body.forEach(function(row, rowIndex) {
                    row.forEach(function(cell, colIndex) {
                        // Menambahkan border di setiap sisi sel (vertikal dan horizontal)
                        cell.border = [true, true, true, true];

                        // Menambahkan penataan untuk memastikan teks berada di tengah
                        cell.alignment = 'center';
                    });
                });

                // Mengatur garis horizontal ringan antara baris
                doc.content[1].layout = {
                    hLineWidth: function(i, node) {
                        return 0.5; // Ketebalan garis horizontal
                    },
                    vLineWidth: function(i, node) {
                        return 0.5; // Ketebalan garis vertikal
                    },
                    hLineColor: function(i, node) {
                        return '#000000'; // Warna garis horizontal
                    },
                    vLineColor: function(i, node) {
                        return '#000000'; // Warna garis vertikal
                    },
                    paddingLeft: function(i, node) { return 4; }, // Padding kiri sel
                    paddingRight: function(i, node) { return 4; }, // Padding kanan sel
                    paddingTop: function(i, node) { return 2; }, // Padding atas sel
                    paddingBottom: function(i, node) { return 2; } // Padding bawah sel
                };
        }
      }
    ]
  });

  // Tempatkan tombol export ke dalam elemen dengan ID #exportButtons
  table.buttons().container().appendTo('#exportButtons');

  // Filter Kelas
  $('#filterKelas').on('change', function () {
    table.ajax.reload();
  });

  // Filter Status
  $('#filterStatus').on('change', function () {
    table.ajax.reload();
  });

  // Filter Waktu Peminjaman
  $('#filterWaktuPeminjaman').on('change', function () {
    table.ajax.reload();
  });

  // Show Entries
  $('#showEntries').on('change', function () {
    table.page.len($(this).val()).draw();
  });

  // Search Box
  $('#searchBox').on('keyup', function() {
    table.search(this.value).draw();
  });
});
</script>

@endpush
@endsection
