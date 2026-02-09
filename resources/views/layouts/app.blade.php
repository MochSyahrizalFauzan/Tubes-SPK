<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'SPK Trust Lafalo')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @yield('styles')

  <style>
    :root{
      --bd:#e5e7eb;
      --muted:#64748b;
      --text:#0f172a;
      --bg:#f6f7fb;
      --card:#ffffff;
      --primary:#0d6efd;
    }

    body { background: var(--bg); color: var(--text); }

    /* Layout */
    .app-shell{ min-height:100vh; display:flex; }

    /* Sidebar desktop */
    .sidebar{
      width: 270px;
      background:#fff;
      border-right:1px solid var(--bd);
      position:sticky;
      top:0;
      height:100vh;
      overflow:hidden;
    }
    .sidebar-inner{
      height:100%;
      display:flex;
      flex-direction:column;
      padding:16px;
      gap:12px;
    }

    .brand{
      padding:10px 10px;
      border:1px solid var(--bd);
      border-radius:14px;
      background:#fff;
    }
    .brand-title{ font-weight:800; color:var(--text); line-height:1.2; }
    .brand-sub{ font-size:.85rem; color:var(--muted); }

    .brand-logo{
  width: 44px;
  height: 44px;
  object-fit: contain;
  border-radius: 10px;
  border: 1px solid var(--bd);
  background: #fff;
}


    .sidebar-scroll{
      overflow:auto;
      padding-right:4px;
      flex:1;
    }

    .nav-section{
      font-size:.72rem;
      letter-spacing:.08em;
      color:var(--muted);
      text-transform:uppercase;
      margin:14px 8px 6px;
    }

    .nav-link{
      color:var(--text);
      border-radius:12px;
      padding:10px 12px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      transition: all .12s ease;
    }
    .nav-link:hover{
      background:#f1f5f9;
      color:var(--text);
    }

    .nav-link.active{
      background: rgba(13,110,253,.12);
      color: var(--primary);
      font-weight:800;
      border:1px solid rgba(13,110,253,.18);
    }

    /* little active indicator dot */
    .nav-dot{
      width:8px;height:8px;border-radius:999px;
      background:transparent;
      flex:0 0 auto;
    }
    .nav-link.active .nav-dot{ background: var(--primary); }

    .content{
      flex:1;
      min-width:0;
    }

    /* Topbar (mobile) */
    .topbar{
      position:sticky;
      top:0;
      z-index:1020;
      background:rgba(246,247,251,.85);
      backdrop-filter: blur(8px);
      border-bottom:1px solid var(--bd);
    }

    /* main padding */
    .page-pad{ padding:22px; }

    /* Make cards consistent */
    .card { border:1px solid var(--bd); border-radius: 14px; }

    /* Offcanvas width = sidebar */
    .offcanvas.sidebar-offcanvas{ width: 270px; }
  </style>
</head>

<body>
  {{-- Topbar hanya muncul di mobile --}}
  <div class="topbar d-lg-none">
    <div class="container-fluid py-2 px-3 d-flex align-items-center justify-content-between">
      <div class="fw-bold">SPK Trust Lafalo</div>
      <button class="btn btn-outline-secondary btn-sm"
              type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#sidebarOffcanvas"
              aria-controls="sidebarOffcanvas">
        Menu
      </button>
    </div>
  </div>

  <div class="app-shell">

    {{-- Sidebar Desktop (lg+) --}}
    <aside class="sidebar d-none d-lg-block">
      <div class="sidebar-inner">
        <div class="brand d-flex align-items-center gap-3">
          <img src="{{ asset('img/logo.jpeg') }}"
               alt="Logo Trust Lafalo"
               class="brand-logo">
          <div>
            <div class="brand-title">SPK Trust Lafalo</div>
            <div class="brand-sub">Admin Panel</div>
          </div>
        </div>

        <div class="sidebar-scroll">
          <div class="nav-section">Menu</div>
          <nav class="nav flex-column gap-1">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
              <span>Dashboard</span>
              <span class="nav-dot"></span>
            </a>

            <div class="nav-section">Master Data</div>
            <a class="nav-link {{ request()->is('customers*') ? 'active' : '' }}"
               href="{{ route('customers.index') }}">
              <span>Customers</span><span class="nav-dot"></span>
            </a>
            <a class="nav-link {{ request()->is('machine-types*') ? 'active' : '' }}"
               href="{{ route('machine-types.index') }}">
              <span>Machine Types</span><span class="nav-dot"></span>
            </a>
            <a class="nav-link {{ request()->is('machine-sizes*') ? 'active' : '' }}"
               href="{{ route('machine-sizes.index') }}">
              <span>Machine Sizes</span><span class="nav-dot"></span>
            </a>
            <a class="nav-link {{ request()->is('processing-rules*') ? 'active' : '' }}"
               href="{{ route('processing-rules.index') }}">
              <span>Processing Rules</span><span class="nav-dot"></span>
            </a>
            <a class="nav-link {{ request()->is('slots*') ? 'active' : '' }}"
               href="{{ route('slots.index') }}">
              <span>Slots</span><span class="nav-dot"></span>
            </a>

            <div class="nav-section">Transaksi</div>
            <a class="nav-link {{ request()->is('orders*') ? 'active' : '' }}"
               href="{{ route('orders.index') }}">
              <span>Orders</span><span class="nav-dot"></span>
            </a>

            <div class="nav-section">Scheduling</div>
            <a class="nav-link {{ request()->is('scheduling/run') ? 'active' : '' }}"
               href="{{ url('/scheduling/run') }}">
              <span>Run ILP</span><span class="nav-dot"></span>
            </a>
            <a class="nav-link {{ request()->is('scheduling/runs*') ? 'active' : '' }}"
               href="{{ url('/scheduling/runs') }}">
              <span>Riwayat Run</span><span class="nav-dot"></span>
            </a>
          </nav>
        </div>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn btn-outline-danger w-100" type="submit">Logout</button>
        </form>
      </div>
    </aside>

    {{-- Offcanvas Sidebar (mobile) --}}
    <div class="offcanvas offcanvas-start sidebar-offcanvas d-lg-none" tabindex="-1" id="sidebarOffcanvas">
      <div class="offcanvas-header border-bottom">
        <div>
          <div class="fw-bold">SPK Trust Lafalo</div>
          <div class="small text-muted">Admin Panel</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body p-2">
        <nav class="nav flex-column gap-1 px-2">

          <div class="nav-section">Menu</div>
          <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
             href="{{ route('dashboard') }}">
            <span>Dashboard</span><span class="nav-dot"></span>
          </a>

          <div class="nav-section">Master Data</div>
          <a class="nav-link {{ request()->is('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
            <span>Customers</span><span class="nav-dot"></span>
          </a>
          <a class="nav-link {{ request()->is('machine-types*') ? 'active' : '' }}" href="{{ route('machine-types.index') }}">
            <span>Machine Types</span><span class="nav-dot"></span>
          </a>
          <a class="nav-link {{ request()->is('machine-sizes*') ? 'active' : '' }}" href="{{ route('machine-sizes.index') }}">
            <span>Machine Sizes</span><span class="nav-dot"></span>
          </a>
          <a class="nav-link {{ request()->is('processing-rules*') ? 'active' : '' }}" href="{{ route('processing-rules.index') }}">
            <span>Processing Rules</span><span class="nav-dot"></span>
          </a>
          <a class="nav-link {{ request()->is('slots*') ? 'active' : '' }}" href="{{ route('slots.index') }}">
            <span>Slots</span><span class="nav-dot"></span>
          </a>

          <div class="nav-section">Transaksi</div>
          <a class="nav-link {{ request()->is('orders*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
            <span>Orders</span><span class="nav-dot"></span>
          </a>

          <div class="nav-section">Scheduling</div>
          <a class="nav-link {{ request()->is('scheduling/run') ? 'active' : '' }}" href="{{ url('/scheduling/run') }}">
            <span>Run ILP</span><span class="nav-dot"></span>
          </a>
          <a class="nav-link {{ request()->is('scheduling/runs*') ? 'active' : '' }}" href="{{ url('/scheduling/runs') }}">
            <span>Riwayat Run</span><span class="nav-dot"></span>
          </a>

          <hr class="my-3">

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-danger w-100" type="submit">Logout</button>
          </form>
        </nav>
      </div>
    </div>

    {{-- Main --}}
    <main class="content">
      <div class="page-pad">
        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger">
            <div class="fw-bold mb-1">Terjadi kesalahan:</div>
            <ul class="mb-0">
              @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
          </div>
        @endif

        @yield('content')
      </div>
    </main>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
