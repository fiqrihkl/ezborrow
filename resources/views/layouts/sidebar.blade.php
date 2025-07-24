<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="/dashboard" class="app-brand-link">
      <span class="demo menu-text fw-bolder ms-2">EZBorrow</span>
    </a>
  </div>
  <div class="menu-inner-shadow"></div>
  <ul class="menu-inner py-1">
    <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
      <a href="/dashboard" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div>Dashboard</div>
      </a>
    </li>

    <!-- Kelola Data -->
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Kelola Data</span></li>
    <!-- Siswa -->
    <li class="menu-item {{ request()->is('siswa*') ? 'active' : '' }}">
      <a href="{{ route('siswa.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-collection"></i>
        <div>Siswa</div>
      </a>
    </li>

    <!-- Guru -->
    <li class="menu-item {{ request()->is('guru*') ? 'active' : '' }}">
      <a href="{{ route('guru.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-crown"></i>
        <div>Guru</div>
      </a>
    </li>

    <!-- Chromebook -->
    <li class="menu-item {{ request()->is('chromebook*') ? 'active' : '' }}">
      <a href="{{ route('chromebook.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-table"></i>
        <div>Chromebook</div>
      </a>
    </li>
    
    <!-- Auth Logout -->
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Auth</span></li>
    <li class="menu-item">
  <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Yakin ingin logout?')">
    @csrf
    <button type="submit" class="menu-link border-0 bg-transparent w-100 text-start">
      <i class="menu-icon tf-icons bx bx-log-out"></i>
      <div>Logout</div>
    </button>
  </form>
</li>

    <!-- Tambahkan menu lainnya di sini -->
  </ul>
</aside>
