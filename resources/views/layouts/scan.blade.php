<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
  @yield('styles')
</head>
<body>
  <div class="container-xxl mt-4">
    @yield('content')
  </div>

  <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/bootstrap/bootstrap.js') }}"></script>
  @stack('scripts')
</body>
</html>
