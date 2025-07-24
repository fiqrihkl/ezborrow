@extends('layouts.scan')

@section('title', 'Pengembalian Chromebook')

@section('content')
<div class="container" style="padding: 20px;">
    <h1>Konfirmasi Pengembalian Chromebook</h1>

    <!-- Card for return information -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Pengembalian Chromebook</h5>
        </div>
        <div class="card-body">
            <p>Anda sedang mengembalikan Chromebook dengan informasi berikut:</p>
            <ul class="list-unstyled">
                <li><strong>Nama Siswa:</strong> {{ $namaSiswa }}</li>
                <li><strong>Kelas:</strong> {{ $peminjaman->siswa->kelas ?? 'Tidak diketahui' }}</li>
                <li><strong>Kode Chromebook:</strong> {{ $chromebook->kode_chromebook }}</li>
                <li><strong>Merek:</strong> {{ $chromebook->merek }}</li>
                <li><strong>Nomor Loker:</strong> {{ $chromebook->nomor_loker }}</li>
            </ul>

            <form id="return-form" action="{{ route('peminjaman.return.store') }}" method="POST">
    @csrf
    <input type="hidden" name="kode_chromebook" value="{{ $chromebook->kode_chromebook }}">
    <button type="submit" class="btn btn-primary w-100">Kembalikan</button>
</form>

            <!-- Audio "Terima Kasih" -->
            <audio id="thankyou-sound" src="{{ asset('sounds/thanks.m4a') }}" preload="auto"></audio>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('return-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Cegah submit langsung

    const audio = document.getElementById('thankyou-sound');
    const form = this;

    // Putar audio
    audio.play().catch((e) => {
        console.warn("Audio gagal diputar:", e);
    });

    // Tampilkan SweetAlert
    Swal.fire({
        icon: 'success',
        title: 'Terima Kasih!',
        text: 'Pengembalian berhasil. Silakan simpan Chromebook ke loker.',
        timer: 5000,
        showConfirmButton: false,
        timerProgressBar: true,
        didOpen: () => {
            // Form bisa disubmit saat alert terbuka atau tunggu selesai
            setTimeout(() => {
                form.submit();
            }, 5000); // Delay sedikit biar audio jalan dulu (opsional)
        }
    });
});
</script>
@endpush