<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached bg-navbar-theme">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>
            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
  <div class="navbar-nav w-100 d-flex justify-content-between align-items-center">
    <!-- Judul halaman -->
    <div class="fw-bold">
      @yield('title', 'Dashboard') <!-- Default 'Dashboard' jika title tidak didefinisikan -->
    </div>

    <!-- Tanggal dan Waktu -->
    <div class="text-muted small" id="navbar-date-time">
      <!-- Waktu akan muncul lewat JavaScript -->
    </div>
  </div>
</nav>
