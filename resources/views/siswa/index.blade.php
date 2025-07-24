@extends('layouts.app')

@section('title', 'DATA SISWA')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Upload Excel -->
    <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" class="mb-3">
        @csrf
        <div class="mb-3">
            <label for="excel_file" class="form-label">Pilih File Excel untuk Diimpor</label>
            <input type="file" class="form-control" name="excel_file" id="excel_file" required>
        </div>
        <button type="submit" class="btn btn-success">Impor Data Siswa</button>
    </form>

    <!-- Tambah Siswa -->
    <a href="{{ route('siswa.create') }}" class="btn btn-primary mb-3">+ Tambah Siswa</a>

    <!-- Data Siswa -->
    <div class="card">
        <h5 class="card-header">Data Siswa</h5>
        <div class="card-body">

            <!-- Export, Filter, Search -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-4 mb-2" id="exportButtons"></div>

                <!-- Filter Kelas -->
                <div class="col-md-4 mb-2">
                    @php
                        $kelasList = \App\Models\Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
                    @endphp
                    <select id="filterKelas" class="form-select">
                        <option value="">Filter Kelas</option>
                        @foreach($kelasList as $kelas)
                            @if($kelas)
                                <option value="{{ $kelas }}">{{ $kelas }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Search Box -->
                <div class="col-md-4 mb-2">
                    <input type="search" id="searchBox" class="form-control" placeholder="Cari Data...">
                </div>
            </div>

            <!-- Show Entries -->
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

            <!-- Table -->
            <div class="table-responsive text-nowrap">
                <table id="siswa-table" class="table table-bordered table-striped" style="width:100%">
                    <thead class="table-light">
                        <tr class="text-nowrap">
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Jenis Kelamin</th>
                            <th>NISN</th>
                            <th>NIK</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody> <!-- Diisi via Ajax -->
                </table>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- DataTables + Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
$.fn.dataTable.ext.pager.ellipses = function(page, pages) {
    var visible = 3;
    var interval = Math.floor(visible / 2);
    var start = page > interval ? Math.max(Math.min(page - interval, pages - visible), 0) : 0;
    var end = page > interval ? Math.min(page + interval + (visible % 2), pages) : visible;

    var buttons = [];
    if (start > 0) {
        buttons.push(0);
        if (start > 1) buttons.push('ellipsis');
    }
    for (var i = start; i < end; i++) {
        buttons.push(i);
    }
    if (end < pages) {
        if (end < pages - 1) buttons.push('ellipsis');
        buttons.push(pages - 1);
    }

    return buttons;
};

$(document).ready(function () {
    var table = $('#siswa-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        
        ajax: {
            url: '{{ route("siswa.data") }}',
            data: function (d) {
                d.kelas = $('#filterKelas').val();
                d.search = {
                    value: $('#searchBox').val(),
                    regex: false
                };
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_lengkap', name: 'nama_lengkap' },
            { data: 'jenis_kelamin', name: 'jenis_kelamin' },
            { data: 'nisn', name: 'nisn' },
            { data: 'nik', name: 'nik' },
            { data: 'kelas', name: 'kelas' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        responsive: true,
        pagingType: 'ellipses',
        pageLength: 10,
        language: {
            search: "",
            searchPlaceholder: "Cari...",
            emptyTable: "Tidak ada data tersedia",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                previous: "Sebelumnya",
                next: "Berikutnya",
            },
        },
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'DATA SISWA',
                className: 'btn btn-success me-2',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
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
                extend: 'csvHtml5',
                title: 'DATA SISWA',
                className: 'btn btn-primary me-2',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'DATA SISWA',
                orientation: 'potrait',
                pageSize: 'A4',
                className: 'btn btn-danger me-2',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                },
                customize: function (doc) {
                // Mengatur ukuran font untuk header dan konten
                doc.styles.tableHeader.fillColor = '#10bc69';
                doc.styles.tableHeader.color = 'white';
                doc.styles.tableHeader.fontSize = 10;
                doc.defaultStyle.fontSize = 9;

                // Menyesuaikan lebar kolom
                doc.content[1].table.widths = ['5%', '45%', '15%', '13%', '19%', '7%'];

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

    table.buttons().container().appendTo('#exportButtons');

    $('#searchBox').on('keyup', function () {
        table.draw();
    });

    $('#showEntries').on('change', function () {
        table.page.len(this.value).draw();
    });

    $('#filterKelas').on('change', function () {
        table.ajax.reload();
    });
});
</script>
@endpush
