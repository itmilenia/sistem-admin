<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="index.html" class="b-brand" style="display: flex; align-items: center; gap: 8px;">
                <div class="logo logo-lg">
                    <img src="{{ asset('assets/images/logo/logo.jpeg') }}" alt="Logo" class="logo logo-lg"
                        style="height: 80px; width: auto;">
                    <span><b>Sistem Admin</b></span>
                </div>
                <img src="{{ asset('assets/images/logo/logo.jpeg') }}" alt="" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                @can('manage_master')
                    <li class="nxl-item nxl-caption">
                        <label>Master Data</label>
                    </li>
                    <li
                        class="nxl-item nxl-hasmenu {{ request()->routeIs(['master-user.*', 'master-role.*', 'master-permission.*']) ? 'active' : '' }}">
                        <a href="#" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-lock"></i></span>
                            <span class="nxl-mtext">Manajemen User</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs(['master-user.*']) ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('master-user.index') }}">User</a>
                            </li>
                            @can('manage_roles')
                                <li class="nxl-item {{ request()->routeIs(['master-role.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('master-role.index') }}">Peran</a>
                                </li>
                            @endcan
                            @can('manage_permissions')
                                <li class="nxl-item {{ request()->routeIs(['master-permission.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('master-permission.index') }}">Hak Akses</a>
                                </li>
                            @endcan
                        </ul>

                    </li>
                @endcan
                <li class="nxl-item nxl-caption">
                    <label>Navigation</label>
                </li>
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs(['dashboard']) ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>
                <li
                    class="nxl-item nxl-hasmenu {{ request()->routeIs(['customer-data-milenia.*', 'customer-data-map.*', 'customer-transaction.*', 'customer-transaction-map.*', 'customer-transaction-milenia.*']) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-users"></i></span>
                        <span class="nxl-mtext">Customer</span><span class="nxl-arrow"><i
                                class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li
                            class="nxl-item {{ request()->routeIs(['customer-data.*', 'customer-data-milenia.*', 'customer-data-map.*']) ? 'active' : '' }}">
                            <a class="nxl-link" href="{{ route('customer-data.landing') }}">Data
                                Customer</a>
                        </li>
                        <li
                            class="nxl-item {{ request()->routeIs(['customer-transaction.*', 'customer-transaction-map.*', 'customer-transaction-milenia.*']) ? 'active' : '' }}">
                            <a class="nxl-link" href="{{ route('customer-transaction.landing') }}">Transaksi Pembelian
                                Customer</a>
                        </li>
                    </ul>
                </li>
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs(['salesperson-sales.*']) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                        <span class="nxl-mtext">Sales</span><span class="nxl-arrow"><i
                                class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item {{ request()->routeIs(['salesperson-sales.*']) ? 'active' : '' }}">
                            <a class="nxl-link" href="{{ route('salesperson-sales.index') }}">Data
                                Penjualan Sales</a>
                        </li>
                        <li class="nxl-item"><a class="nxl-link" href="#">Surat Penawaran</a></li>
                        <li class="nxl-item"><a class="nxl-link" href="#">Surat Agreement</a></li>
                    </ul>
                </li>
                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-gift"></i></span>
                        <span class="nxl-mtext">Promo & Produk</span><span class="nxl-arrow"><i
                                class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="#">Program Promo</a></li>
                        <li class="nxl-item"><a class="nxl-link" href="#">Pricelist Produk</a></li>
                        <li class="nxl-item"><a class="nxl-link" href="#">Form Klaim Produk</a></li>
                </li>
            </ul>
        </div>
    </div>
</nav>
