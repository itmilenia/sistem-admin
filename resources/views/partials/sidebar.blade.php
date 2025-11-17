<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand" style="display: flex; align-items: center; gap: 8px;">
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
                @can('kelola_data_master')
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
                            @can('kelola_peran')
                                <li class="nxl-item {{ request()->routeIs(['master-role.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('master-role.index') }}">Peran</a>
                                </li>
                            @endcan
                            @can('kelola_hak_akses')
                                <li class="nxl-item {{ request()->routeIs(['master-permission.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('master-permission.index') }}">Hak Akses</a>
                                </li>
                            @endcan
                        </ul>

                    </li>

                    <li
                        class="nxl-item nxl-hasmenu {{ request()->routeIs(['master-customer-network.*', 'master-product-brand.*', 'master-tax.*']) ? 'active' : '' }}">
                        <a href="#" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-settings"></i></span>
                            <span class="nxl-mtext">Manajemen Fitur</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            @can('kelola_data_master')
                                <li class="nxl-item {{ request()->routeIs(['master-customer-network.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('master-customer-network.index') }}">Jaringan
                                        Customer</a>
                                </li>
                                <li class="nxl-item {{ request()->routeIs(['master-product-brand.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('master-product-brand.index') }}">Product Brand</a>
                                </li>
                                <li class="nxl-item {{ request()->routeIs(['master-tax.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('master-tax.index') }}">Pajak</a>
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

                @canany(['lihat_data_customer_map', 'lihat_data_customer_milenia',
                    'lihat_transaksi_customer_map_cabang', 'lihat_transaksi_customer_map_pusat',
                    'lihat_transaksi_customer_milenia_cabang', 'lihat_transaksi_customer_milenia_pusat'])
                    <li
                        class="nxl-item nxl-hasmenu {{ request()->routeIs([
                            'customer-data-milenia.*',
                            'customer-data-map.*',
                            'customer-transaction.*',
                            'customer-transaction-map.*',
                            'customer-transaction-milenia.*',
                            'customer-transaction-map-branch.*',
                            'customer-transaction-milenia-branch.*',
                        ])
                            ? 'active'
                            : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Customer</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            @canany(['lihat_data_customer_map', 'lihat_data_customer_milenia'])
                                <li
                                    class="nxl-item {{ request()->routeIs(['customer-data.*', 'customer-data-milenia.*', 'customer-data-map.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('customer-data.landing') }}">Data
                                        Customer</a>
                                </li>
                            @endcanany

                            @canany(['lihat_transaksi_customer_milenia_pusat', 'lihat_transaksi_customer_milenia_cabang',
                                'lihat_transaksi_customer_map_pusat', 'lihat_transaksi_customer_map_cabang'])
                                <li
                                    class="nxl-item {{ request()->routeIs(['customer-transaction.*', 'customer-transaction-map.*', 'customer-transaction-milenia.*', 'customer-transaction-map-branch.*', 'customer-transaction-milenia-branch.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('customer-transaction.landing') }}">Transaksi Pembelian
                                        Customer</a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                @canany(['lihat_penjualan_sales_map_cabang', 'lihat_penjualan_sales_map_pusat',
                    'lihat_penjualan_sales_milenia_cabang', 'lihat_penjualan_sales_milenia_pusat',
                    'lihat_surat_agreement_map', 'lihat_surat_agreement_milenia', 'lihat_surat_penawaran_map',
                    'lihat_surat_penawaran_milenia'])
                    <li
                        class="nxl-item nxl-hasmenu {{ request()->routeIs(['salesperson-sales.*', 'quotation-letter.*', 'agreement-letter.*']) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Sales</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            @canany(['lihat_penjualan_sales_map_cabang', 'lihat_penjualan_sales_map_pusat',
                                'lihat_penjualan_sales_milenia_cabang', 'lihat_penjualan_sales_milenia_pusat'])
                                <li
                                    class="nxl-item {{ request()->routeIs(['salesperson-sales.transactions.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('salesperson-sales.landing') }}">Data
                                        Penjualan Sales</a>
                                </li>
                            @endcanany

                            @canany(['lihat_surat_penawaran_map', 'lihat_surat_penawaran_milenia'])
                                <li class="nxl-item {{ request()->routeIs(['quotation-letter.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('quotation-letter.landing') }}">Surat Penawaran</a>
                                </li>
                            @endcanany

                            @canany(['lihat_surat_agreement_map', 'lihat_surat_agreement_milenia'])
                                <li class="nxl-item {{ request()->routeIs(['agreement-letter.*']) ? 'active' : '' }}"><a
                                        class="nxl-link" href="{{ route('agreement-letter.landing') }}">Surat Agreement</a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                @canany(['lihat_program_promo_map', 'lihat_program_promo_milenia', 'lihat_data_pricelist_produk_map',
                    'lihat_data_pricelist_produk_milenia', 'lihat_klaim_produk_map', 'lihat_klaim_produk_milenia'])
                    <li
                        class="nxl-item nxl-hasmenu {{ request()->routeIs(['pricelist-produk.*', 'pricelist-produk-milenia.*', 'pricelist-produk-map.*', 'promotion-program.*', 'product-claim-form.*']) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-gift"></i></span>
                            <span class="nxl-mtext">Promo & Produk</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            @canany(['lihat_program_promo_map', 'lihat_program_promo_milenia'])
                                <li class="nxl-item {{ request()->routeIs(['promotion-program.*']) ? 'active' : '' }}"><a
                                        class="nxl-link" href="{{ route('promotion-program.landing') }}">Program Promo</a>
                                </li>
                            @endcanany

                            @canany(['lihat_data_pricelist_produk_map', 'lihat_data_pricelist_produk_milenia'])
                                <li
                                    class="nxl-item {{ request()->routeIs(['pricelist-produk.*', 'pricelist-produk-milenia.*', 'pricelist-produk-map.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('pricelist-produk.landing') }}">Pricelist Produk</a>
                                </li>
                            @endcanany

                            @canany(['lihat_klaim_produk_map', 'lihat_klaim_produk_milenia'])
                                <li class="nxl-item {{ request()->routeIs(['product-claim-form.*']) ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('product-claim-form.landing') }}">Form Klaim
                                        Produk
                                    </a>
                                </li>
                            @endcanany
                    </li>
                @endcanany
            </ul>
        </div>
    </div>
</nav>
