@extends('layouts.landing')

@section('title', 'Scan Chromebook')

@section('content')
<!-- Hero Section - Scan Page -->
<section id="hero" class="hero section">
  <div class="container">
    <div class="row gy-4">
      <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center text-center" data-aos="fade-up">
        <h1>Scan QR Code Chromebook</h1>
        <p class="mb-4">Silakan arahkan QR Code Chromebook ke kamera Anda dan jangan hilangkan sampai ada suara "Beep" dari perangkat</p>
        
        <!-- Kamera Preview -->
        <div id="preview" style="position: relative; width: 100%; max-width: 400px; height: 300px; margin: 0 auto;">
          <div id="scanner-overlay">
            <div class="laser"></div>
          </div>
        </div>

        <!-- Tombol Switch Camera di bawah kamera -->
        <div class="text-center mt-3">
          <button id="switch-camera" class="btn btn-primary">Ganti Kamera</button>
        </div>

        <!-- Form tersembunyi -->
        <form id="scan-form" method="GET" action="{{ url('/peminjaman/result') }}" style="display: none;">
            <input type="hidden" name="kode_chromebook" id="kode_chromebook">
        </form>

        <!-- Audio Beep -->
        <audio id="beep-sound" src="{{ asset('sounds/beep.m4a') }}" preload="auto"></audio>
      </div>

      <div class="col-lg-6 order-1 order-lg-2 hero-img text-center" data-aos="zoom-out" data-aos-delay="100">
        <img src="{{ asset('landing/img/scanqr.png') }}" class="img-fluid animated" alt="">
      </div>
    </div>
  </div>
</section>
@endsection

@push('styles')
<style>
    #preview video {
        transform: scaleX(-1);
        border-radius: 10px;
    }

    #scanner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 2px dashed rgba(255, 255, 255, 0.7);
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
        pointer-events: none;
        z-index: 10;
    }

    .laser {
        position: absolute;
        top: 0;
        left: 0;
        height: 2px;
        width: 100%;
        background: red;
        animation: scan-line 2s infinite;
        z-index: 11;
    }

    @keyframes scan-line {
        0% { top: 0; }
        50% { top: 100%; }
        100% { top: 0; }
    }
</style>
@endpush

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- QR Code Scanner -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    const html5QrCode = new Html5Qrcode("preview");
    let currentCameraIndex = 0;
    let cameras = [];
    let hasScanned = false;

    // Tampilkan alert dari session jika ada
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            timerProgressBar: true
        });
    @elseif(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false,
            timerProgressBar: true
        });
    @endif

    // Fungsi memulai kamera
    function startCamera(cameraId) {
        html5QrCode.start(
            cameraId,
            { fps: 10, qrbox: 250 },
            qrCodeMessage => {
                if (hasScanned) return;
                hasScanned = true;

                document.getElementById('beep-sound').play();

                Swal.fire({
                    title: 'QR Code Ditemukan',
                    text: `Kode Chromebook: ${qrCodeMessage}`,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    willClose: () => {
                        document.getElementById('kode_chromebook').value = qrCodeMessage;
                        document.getElementById('scan-form').submit();
                        html5QrCode.stop();
                    }
                });
            },
            errorMessage => {
                // Tidak perlu alert, cukup silent
                console.warn("Scan error:", errorMessage);
            }
        ).catch(err => {
            console.error("Gagal memulai kamera:", err);
        });
    }

    // Inisialisasi kamera
    Html5Qrcode.getCameras().then(devices => {
        if (devices.length > 0) {
            cameras = devices;
            startCamera(cameras[currentCameraIndex].id);
        } else {
            console.error("Tidak ada kamera ditemukan.");
        }
    }).catch(err => {
        console.error("Gagal mendapatkan kamera: ", err);
    });

    // Tombol Ganti Kamera
    document.getElementById('switch-camera').addEventListener('click', () => {
        if (cameras.length < 2) {
            alert("Hanya ada satu kamera yang tersedia.");
            return;
        }

        currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
        const newCameraId = cameras[currentCameraIndex].id;

        html5QrCode.stop().then(() => {
            hasScanned = false; // Reset agar bisa scan lagi
            startCamera(newCameraId);
        }).catch(err => {
            console.error("Gagal mengganti kamera:", err);
        });
    });
</script>
@endpush
