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
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
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

    <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" class="mb-3">
        @csrf
        <div class="mb-3">
            <label for="excel_file" class="form-label">Pilih File Excel untuk Diimpor</label>
            <input type="file" class="form-control" name="excel_file" id="excel_file" required>
        </div>
        <button type="submit" class="btn btn-success">Impor Data Siswa</button>
    </form>

    <a href="{{ route('siswa.create') }}" class="btn btn-primary mb-3">+ Tambah Siswa</a>

    <div class="card">
        <h5 class="card-header">Data Siswa</h5>
        <div class="card-body">

            <div class="row mb-3 align-items-center">
                <div class="col-md-4 mb-2" id="exportButtons"></div>

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

                <div class="col-md-4 mb-2">
                    <input type="search" id="searchBox" class="form-control" placeholder="Cari Data...">
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

            <div class="table-responsive text-nowrap">
                <table id="siswa-table" class="table table-bordered table-striped" style="width:100%">
                    <thead class="table-light">
                        <tr class="text-nowrap">
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Jenis Kelamin</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody> </table>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
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
// Skrip paginasi kustom tidak perlu diubah
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
                    // Hapus kolom NIK (indeks 4) dari ekspor
                    columns: [0, 1, 2, 3, 4]
                },
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
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
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'DATA SISWA',
                orientation: 'portrait',
                pageSize: 'A4',
                className: 'btn btn-danger me-2',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                },
                customize: function (doc) {
                    doc.styles.tableHeader.fillColor = '#10bc69';
                    doc.styles.tableHeader.color = 'white';
                    doc.styles.tableHeader.fontSize = 10;
                    doc.defaultStyle.fontSize = 9;

                    // Sesuaikan lebar kolom karena NIK dihapus
                    doc.content[1].table.widths = ['5%', '45%', '20%', '20%', '10%'];

                    doc.content[1].table.body.forEach(function(row) {
                        row.forEach(function(cell) {
                            cell.border = [true, true, true, true];
                            cell.alignment = 'center';
                        });
                    });

                    doc.content[1].layout = {
                        hLineWidth: function(i, node) { return 0.5; },
                        vLineWidth: function(i, node) { return 0.5; },
                        hLineColor: function(i, node) { return '#000000'; },
                        vLineColor: function(i, node) { return '#000000'; },
                        paddingLeft: function(i, node) { return 4; },
                        paddingRight: function(i, node) { return 4; },
                        paddingTop: function(i, node) { return 2; },
                        paddingBottom: function(i, node) { return 2; }
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