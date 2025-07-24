<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@yield('title', 'Landing Page')</title>

  <link href="{{ asset('assets/img/ezborrow-icon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/ezborrow-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts dan CSS -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <link href="{{ asset('landing/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('landing/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('landing/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('landing/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('landing/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('landing/css/main.css') }}" rel="stylesheet">

  <!-- SweetAlert2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" rel="stylesheet">

  @stack('styles')
</head>
<body class="@yield('body_class', 'index-page')">

  {{-- Header --}}
  @include('partials.landing-header')

  {{-- Main Content --}}
  <main class="main">
    @yield('content')
  </main>

  {{-- Footer --}}
  @include('partials.landing-footer')

  {{-- Script Vendor --}}
  <script src="{{ asset('landing/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('landing/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('landing/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('landing/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('landing/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('landing/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('landing/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('landing/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('landing/js/main.js') }}"></script>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

  <!-- QR Scanner JS -->
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

  @stack('scripts')
</body>
</html>
