<header id="header" class="header d-flex align-items-center sticky-top">
  <div class="container-fluid container-xl position-relative d-flex align-items-center">

    <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
      <h1 class="sitename">EZBorrow</h1>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li>
          <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
        </li>
        <li>
          <a href="{{ route('peminjaman.index') }}" class="{{ request()->is('peminjaman*') ? 'active' : '' }}">Peminjaman</a>
        </li>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

    <a class="btn-getstarted" href="{{ route('login') }}">LOGIN</a>

  </div>
</header>
