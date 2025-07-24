@extends('layouts.landing')

@section('title', 'Beranda')

@section('content')

<!-- Hero Section -->
<section id="hero" class="hero section">
  <div class="container">
    <div class="row gy-4">
      <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="fade-up">
        <h1>EZBorrow</h1>
        <p><b>Mudah. Cepat. Aman. Solusi modern untuk peminjaman Chromebook siswa!</b></p>
        <p></p>
        <div class="d-flex">
          <a href="{{ route('peminjaman.index') }}" class="btn-get-started">Pinjam Sekarang</a>
        </div>
      </div>
      <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="100">
        <img src="{{ asset('landing/img/ezborrow-700p.png') }}" class="img-fluid animated" alt="">
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section id="about" class="about section">
  <div class="container section-title" data-aos="fade-up">
    <span>Tentang Aplikasi<br></span>
    <h2>Tentang Aplikasi</h2>
  </div>

  <div class="container">
    <div class="row gy-4">
      <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="100">
        <img src="{{ asset('landing/img/landing-700p.png') }}" class="img-fluid" alt="">
      </div>
      <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="200">
        <h3>Peminjaman Chromebook</h3>
        <p class="fst">
          EZBorrow adalah aplikasi inovatif yang memudahkan proses peminjaman Chromebook di lingkungan sekolah. Hanya dengan memindai QR Code yang terpasang pada perangkat, siswa dapat melakukan peminjaman dalam hitungan detik — tanpa formulir manual, tanpa antrian panjang. Dengan sistem pencatatan otomatis dan antarmuka yang sederhana, EZBorrow membantu sekolah mengelola inventaris Chromebook dengan lebih efisien, terorganisir, dan transparan.
      </div>
    </div>
  </div>
</section>

@endsection
