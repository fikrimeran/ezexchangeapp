<!DOCTYPE html>
<html lang="en">

<head>
<style>
  /* Sidebar background */
  .main-sidebar {
    background-color: #1f2937 !important; /* Dark slate gray */
  }

  /* Active menu item */
  .main-sidebar .nav-link.active {
    background-color: #3b82f6 !important; /* Blue accent */
    color: #ffffff !important; /* Keep text white */
  }

  /* Hover menu items */
  .main-sidebar .nav-link:hover {
    background-color: #374151 !important; /* Slightly lighter dark */
    color: #ffffff !important; /* Text remains white */
  }

  /* Sidebar text and icons */
  .main-sidebar .nav-link,
  .main-sidebar .brand-link,
  .main-sidebar .user-panel .info a {
    color: #ffffff !important;
  }

  /* Optional: subtle border between menu items */
  .main-sidebar .nav-item {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
</style>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AdminLTE 3 | Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('admin/plugins/fontawesome-free/css/all.min.css') }}" />
  <!-- Ionicons -->
  <link
    rel="stylesheet"
    href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"
  />
  <!-- Tempusdominus Bootstrap 4 -->
  <link
    rel="stylesheet"
    href="{{ asset('admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}"
  />
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}" />
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ asset('admin/plugins/jqvmap/jqvmap.min.css') }}" />
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.min.css') }}" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="{{ asset('admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}"
  />
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset('admin/plugins/daterangepicker/daterangepicker.css') }}" />
  <!-- Summernote -->
  <link rel="stylesheet" href="{{ asset('admin/plugins/summernote/summernote-bs4.min.css') }}" />
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Preloader -->
    <div
      class="preloader flex-column justify-content-center align-items-center"
    >
      <img
        class="animation__shake"
        src="{{ asset('admin/dist/img/AdminLTELogo.png') }}"
        alt="AdminLTELogo"
        height="60"
        width="60"
      />
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <!-- Push menu button -->
        <li class="nav-item">
          <a
            class="nav-link"
            data-widget="pushmenu"
            href="#"
            role="button"
          >
            <i class="fas fa-bars"></i>
          </a>
        </li>

        <!-- Home link -->
        <li class="nav-item d-none d-sm-inline-block">
          <a href="{{ url('/auth/home') }}" class="nav-link">Home</a>
        </li>
      </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside
      class="main-sidebar sidebar-dark-primary elevation-4"
    >
      <!-- Brand Logo -->
      <a href="{{ url('/auth/home') }}" class="brand-link d-flex justify-content-center align-items-center" style="height: 56px;">
          <span class="brand-text font-weight-bold">EZExchange</span>
          <i class="bi bi-arrow-repeat mr-2"></i>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">

      <!-- Sidebar user panel -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex justify-content-center">
        <div class="info text-center w-100">
          <a href="#" class="d-block font-weight-bold">
            Admin: {{ Auth::user()->name }}
          </a>
        </div>
      </div>

        <!-- Sidebar Menu -->
        <nav
          class="mt-2"
        >
          <ul
            class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false"
          >
            <!-- Dashboard -->
            <li class="nav-item menu-open">
              <a
                href="{{ url('/auth/home') }}"
                class="nav-link active"
              >
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>

            <!-- User Management -->
            <li class="nav-item">
              <a
                href="{{ route('auth.users.index') }}"
                class="nav-link"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>List of Users</p>
              </a>
            </li>

            <!-- Categories -->
            <li class="nav-item">
              <a
                href="{{ route('auth.categories.index') }}"
                class="nav-link"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>List of Categories</p>
              </a>
            </li>

            <!-- Subcategories -->
            <li class="nav-item">
              <a
                href="{{ route('auth.subcategories.index') }}"
                class="nav-link"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>List of SubCategories</p>
              </a>
            </li>

            <!-- Items -->
            <li class="nav-item">
              <a
                href="{{ route('auth.items.index') }}"
                class="nav-link"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>List of Items</p>
              </a>
            </li>

            <!-- Exchange History -->
            <li class="nav-item">
              <a
                href="{{ route('auth.exchangerequests.index') }}"
                class="nav-link"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>List of Exchange History</p>
              </a>
            </li>

            <!-- Logout -->
            <li class="nav-item">
              <form
                id="logout-form"
                action="{{ route('logout') }}"
                method="POST"
                style="display: inline;"
              >
                @csrf
                <button
                  type="submit"
                  class="nav-link btn btn-link text-left"
                  style="
                    color: #c2c7d0;
                    padding-left: 1rem;
                    padding-top: 0.5rem;
                    padding-bottom: 0.5rem;
                    width: 100%;
                    border: none;
                    background: transparent;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    font-weight: 500;
                    transition: color 0.3s ease;
                  "
                  onmouseover="this.style.color='#fff'"
                  onmouseout="this.style.color='#c2c7d0'"
                >
                  <i class="fas fa-sign-out-alt"></i>
                  <p style="margin: 0;">Logout</p>
                </button>
              </form>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">@yield('page-subtitle', '')</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <section class="content">
        <div class="container">
          @yield('content')
        </div>
      </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
      <strong>
        &copy; {{ date('Y') }} EZExchange Application.
      </strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0.0
      </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
  </div>

  <!-- Scripts -->
  <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
  <script>$.widget.bridge('uibutton', $.ui.button)</script>
  <script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script>
  <script src="{{ asset('admin/plugins/sparklines/sparkline.js') }}"></script>
  <script src="{{ asset('admin/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
  <script src="{{ asset('admin/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
  <script src="{{ asset('admin/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
  <script src="{{ asset('admin/plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
  <script src="{{ asset('admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
  <script src="{{ asset('admin/plugins/summernote/summernote-bs4.min.js') }}"></script>
  <script src="{{ asset('admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
  <script src="{{ asset('admin/dist/js/adminlte.js') }}"></script>
  <script src="{{ asset('admin/dist/js/demo.js') }}"></script>
  <script src="{{ asset('admin/dist/js/pages/dashboard.js') }}"></script>

@stack('scripts')
</body>

</html>
