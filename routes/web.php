<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Feat\CustomerController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\ProductBrandController;
use App\Http\Controllers\Feat\AgreementLetterController;
use App\Http\Controllers\Feat\QuotationLetterController;
use App\Http\Controllers\Feat\ProductPricelistController;
use App\Http\Controllers\Feat\PromotionProgramController;
use App\Http\Controllers\Feat\SalespersonSalesController;
use App\Http\Controllers\Master\CustomerNetworkController;
use App\Http\Controllers\Feat\CustomerTransactionController;

Route::get('/', function () {
    return auth()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('welcome');
    })->name('dashboard');

    Route::prefix('manajemen-fitur')->group(function () {
        // Route Jaringan Customer
        Route::middleware('permission:kelola_data_master')->group(function () {
            Route::prefix('jaringan-customer')->name('master-customer-network.')->group(function () {
                Route::get('/', [CustomerNetworkController::class, 'index'])->name('index');
                Route::get('/{id}/detail', [CustomerNetworkController::class, 'detail'])->name('detail');
                Route::get('/tambah', [CustomerNetworkController::class, 'create'])->name('create');
                Route::post('/', [CustomerNetworkController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [CustomerNetworkController::class, 'edit'])->name('edit');
                Route::put('/{id}/update', [CustomerNetworkController::class, 'update'])->name('update');
            });

            // Route Product Brand
            Route::prefix('product-brand')->name('master-product-brand.')->group(function () {
                Route::get('/', [ProductBrandController::class, 'index'])->name('index');
                Route::get('/{id}/detail', [ProductBrandController::class, 'detail'])->name('detail');
                Route::get('/tambah', [ProductBrandController::class, 'create'])->name('create');
                Route::post('/', [ProductBrandController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [ProductBrandController::class, 'edit'])->name('edit');
                Route::put('/{id}/update', [ProductBrandController::class, 'update'])->name('update');
            });
        });
    });

    Route::prefix('manajemen-user')->group(function () {
        // Route Master Karyawan
        Route::middleware('permission:kelola_data_master')->group(function () {
            Route::get('user', [UserController::class, 'index'])->name('master-user.index');
            Route::get('user/tambah', [UserController::class, 'create'])->name('master-user.create');
            Route::post('user', [UserController::class, 'store'])->name('master-user.store');
            Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('master-user.edit');
            Route::put('user/{id}/update', [UserController::class, 'update'])->name('master-user.update');
            Route::put('user/{id}/delete', [UserController::class, 'delete'])->name('master-user.destroy');
            Route::get('/sync', [UserController::class, 'syncEmployeeData'])->name('master-user.sync');
        });

        // Route Master Role
        Route::middleware('permission:kelola_peran')->group(function () {
            Route::get('peran', [RoleController::class, 'index'])->name('master-role.index');
            Route::get('peran/tambah', [RoleController::class, 'create'])->name('master-role.create');
            Route::post('peran', [RoleController::class, 'store'])->name('master-role.store');
            Route::get('peran/{id}/edit', [RoleController::class, 'edit'])->name('master-role.edit');
            Route::put('peran/{id}/update', [RoleController::class, 'update'])->name('master-role.update');
            Route::delete('peran/{id}/delete', [RoleController::class, 'destroy'])->name('master-role.destroy');
        });

        // Route Izin Akses
        Route::middleware('permission:kelola_hak_akses')->group(function () {
            Route::get('hak-akses', [PermissionController::class, 'index'])->name('master-permission.index');
            Route::get('hak-akses/tambah', [PermissionController::class, 'create'])->name('master-permission.create');
            Route::post('hak-akses', [PermissionController::class, 'store'])->name('master-permission.store');
            Route::get('hak-akses/{id}/edit', [PermissionController::class, 'edit'])->name('master-permission.edit');
            Route::put('hak-akses/{id}/update', [PermissionController::class, 'update'])->name('master-permission.update');
            Route::delete('hak-akses/{id}/delete', [PermissionController::class, 'destroy'])->name('master-permission.destroy');
        });
    });

    route::prefix('customer')->group(function () {
        Route::get('/landing', [CustomerController::class, 'landing'])->name('customer-data.landing');

        // Route Data Customer
        Route::prefix('data-customer-milenia')->middleware('permission:lihat_data_customer_milenia')->group(function () {
            Route::get('/', [CustomerController::class, 'indexMilenia'])->name('customer-data-milenia.index');
            Route::get('/{id}/detail', [CustomerController::class, 'showMilenia'])->name('customer-data-milenia.show');
        });

        Route::prefix('data-customer-mega')->middleware('permission:lihat_data_customer_map')->group(function () {
            Route::get('/', [CustomerController::class, 'indexMega'])->name('customer-data-map.index');
            Route::get('/{id}/detail', [CustomerController::class, 'showMega'])->name('customer-data-map.show');
        });

        // Route Transaksi Customer
        Route::prefix(('transaksi-pembelian-customer'))->group(function () {
            Route::get('/landing', [CustomerTransactionController::class, 'landing'])->name('customer-transaction.landing');

            // Route Menampilkan Data Transaksi Customer Milenia
            Route::prefix('milenia')->middleware('permission:lihat_transaksi_customer_milenia_pusat')->group(function () {
                Route::get('/', [CustomerTransactionController::class, 'indexMilenia'])->name('customer-transaction-milenia.index');
                Route::get('/data', [CustomerTransactionController::class, 'getDataMilenia'])->name('customer-transaction-milenia.data');
                Route::get('/{id}/detail', [CustomerTransactionController::class, 'showMilenia'])->name('customer-transaction-milenia.show');
            });

            Route::prefix('milenia-cabang')->middleware('permission:lihat_transaksi_customer_milenia_cabang')->group(function () {
                Route::get('/', [CustomerTransactionController::class, 'indexMileniaBranch'])->name('customer-transaction-milenia-branch.index');
                Route::get('/data', [CustomerTransactionController::class, 'getDataMileniaBranch'])->name('customer-transaction-milenia-branch.data');
                Route::get('/{id}/detail', [CustomerTransactionController::class, 'showMileniaBranch'])->name('customer-transaction-milenia-branch.show');
            });

            // Route Menampilkan Data Transaksi Customer Map
            Route::prefix('map')->middleware('permission:lihat_transaksi_customer_map_pusat')->group(function () {
                Route::get('/', [CustomerTransactionController::class, 'indexMap'])->name('customer-transaction-map.index');
                Route::get('/data', [CustomerTransactionController::class, 'getDataMap'])->name('customer-transaction-map.data');
                Route::get('/{id}/detail', [CustomerTransactionController::class, 'showMap'])->name('customer-transaction-map.show');
            });

            Route::prefix('map-cabang')->middleware('permission:lihat_transaksi_customer_map_cabang')->group(function () {
                Route::get('/', [CustomerTransactionController::class, 'indexMapBranch'])->name('customer-transaction-map-branch.index');
                Route::get('/data', [CustomerTransactionController::class, 'getDataMapBranch'])->name('customer-transaction-map-branch.data');
                Route::get('/{id}/detail', [CustomerTransactionController::class, 'showMapBranch'])->name('customer-transaction-map-branch.show');
            });
        });
    });

    Route::prefix('sales')->group(function () {
        Route::prefix('data-penjualan-sales')->name('salesperson-sales.')->group(function () {
            Route::get('/landing', [SalespersonSalesController::class, 'landing'])->name('landing');

            // Route Menampilkan Data Penjualan Sales
            Route::prefix('milenia')->middleware('permission:lihat_penjualan_sales_milenia_pusat')->group(function () {
                Route::get('/', [SalespersonSalesController::class, 'indexMilenia'])->name('transactions.milenia.index');
                Route::get('/data', [SalespersonSalesController::class, 'getDataMilenia'])->name('transactions.milenia.data');
                Route::get('/{id}/detail', [SalespersonSalesController::class, 'showMilenia'])->name('transactions.milenia.show');
                Route::get('/{id}/transactions', [SalespersonSalesController::class, 'getSalespersonTransactionsDataMilenia'])->name('transactions.milenia.data.details');
                Route::get('/export-all-per-sales', [SalespersonSalesController::class, 'exportAllSalesMileniaByBrand'])->name('transactions.milenia.export-all-per-sales');
                Route::get('/{id}/export-sales-by-brand', [SalespersonSalesController::class, 'exportSalesMileniaByBrand'])->name('transactions.milenia.export-sales-by-brand');
            });

            Route::prefix('milenia-cabang')->middleware('permission:lihat_penjualan_sales_milenia_cabang')->group(function () {
                Route::get('/', [SalespersonSalesController::class, 'indexMileniaBranch'])->name('transactions.milenia.branch.index');
                Route::get('/data', [SalespersonSalesController::class, 'getDataMileniaBranch'])->name('transactions.milenia.branch.data');
                Route::get('/{id}/detail', [SalespersonSalesController::class, 'showMileniaBranch'])->name('transactions.milenia.branch.show');
                Route::get('/{id}/transactions', [SalespersonSalesController::class, 'getSalespersonTransactionsDataMileniaBranch'])->name('transactions.milenia.data.branch.details');
                Route::get('/export-all-per-sales', [SalespersonSalesController::class, 'exportAllSalesMileniaBranchByBrand'])->name('transactions.milenia-branch.export-all-per-sales');
                Route::get('/{id}/export-sales-by-brand', [SalespersonSalesController::class, 'exportSalesMileniaBranchByBrand'])->name('transactions.milenia-branch.export-sales-by-brand');
            });

            // Route Menampilkan Data Penjualan Sales
            Route::prefix('map')->middleware('permission:lihat_penjualan_sales_map_pusat')->group(function () {
                Route::get('/', [SalespersonSalesController::class, 'indexMap'])->name('transactions.map.index');
                Route::get('/data', [SalespersonSalesController::class, 'getDataMap'])->name('transactions.map.data');
                Route::get('/{id}/detail', [SalespersonSalesController::class, 'showMap'])->name('transactions.map.show');
                Route::get('/{id}/transactions', [SalespersonSalesController::class, 'getSalespersonTransactionsDataMap'])->name('transactions.map.data.details');
                Route::get('/export-all-per-sales', [SalespersonSalesController::class, 'exportAllSalesMapByBrand'])->name('transactions.map.export-all-per-sales');
                Route::get('/{id}/export-sales-by-brand', [SalespersonSalesController::class, 'exportSalesMapByBrand'])->name('transactions.map.export-sales-by-brand');
            });

            Route::prefix('map-cabang')->middleware('permission:lihat_penjualan_sales_map_cabang')->group(function () {
                Route::get('/', [SalespersonSalesController::class, 'indexMapBranch'])->name('transactions.map.branch.index');
                Route::get('/data', [SalespersonSalesController::class, 'getDataMapBranch'])->name('transactions.map.branch.data');
                Route::get('/{id}/detail', [SalespersonSalesController::class, 'showMapBranch'])->name('transactions.map.branch.show');
                Route::get('/{id}/transactions', [SalespersonSalesController::class, 'getSalespersonTransactionsDataMapBranch'])->name('transactions.map.data.branch.details');
                Route::get('/export-all-per-sales', [SalespersonSalesController::class, 'exportAllSalesMapBranchByBrand'])->name('transactions.map-branch.export-all-per-sales');
                Route::get('/{id}/export-sales-by-brand', [SalespersonSalesController::class, 'exportSalesMapBranchByBrand'])->name('transactions.map-branch.export-sales-by-brand');
            });
        });

        // Route Menampilkan Data Surat Penawaran (Quotation Letter)
        Route::prefix('surat-penawaran')->name('quotation-letter.')->group(function () {

            Route::get('/landing', [QuotationLetterController::class, 'landing'])->name('landing');
            Route::post('/store', [QuotationLetterController::class, 'store'])->name('store');
            Route::put('/{id}/update', [QuotationLetterController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [QuotationLetterController::class, 'destroy'])->name('destroy');

            // Milenia
            Route::prefix('milenia')->group(function () {
                Route::get('/', [QuotationLetterController::class, 'indexMilenia'])->middleware('permission:lihat_surat_penawaran_milenia')->name('milenia.index');
                Route::get('/{id}/detail', [QuotationLetterController::class, 'showMilenia'])->middleware('permission:lihat_surat_penawaran_milenia')->name('milenia.show');
                Route::get('/tambah', [QuotationLetterController::class, 'createMilenia'])->middleware('permission:buat_surat_penawaran_milenia')->name('milenia.create');
                Route::get('/{id}/edit', [QuotationLetterController::class, 'editMilenia'])->middleware('permission:ubah_surat_penawaran_milenia')->name('milenia.edit');
            });

            // Map
            Route::prefix('map')->group(function () {
                Route::get('/', [QuotationLetterController::class, 'indexMap'])->middleware('permission:lihat_surat_penawaran_map')->name('map.index');
                Route::get('/{id}/detail', [QuotationLetterController::class, 'showMap'])->middleware('permission:lihat_surat_penawaran_map')->name('map.show');
                Route::get('/tambah', [QuotationLetterController::class, 'createMap'])->middleware('permission:buat_surat_penawaran_map')->name('map.create');
                Route::get('/{id}/edit', [QuotationLetterController::class, 'editMap'])->middleware('permission:ubah_surat_penawaran_map')->name('map.edit');
            });
        });

        // Route Menampilkan Data Surat Agreement (Agreement Letter)
        Route::prefix('surat-agreement')->name('agreement-letter.')->group(function () {
            Route::get('/landing', [AgreementLetterController::class, 'landing'])->name('landing');
            Route::post('/store', [AgreementLetterController::class, 'store'])->name('store');
            Route::put('/{id}/update', [AgreementLetterController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [AgreementLetterController::class, 'destroy'])->name('destroy');

            // Milenia
            Route::prefix('milenia')->group(function () {
                Route::get('/', [AgreementLetterController::class, 'indexMilenia'])->middleware('permission:lihat_surat_agreement_milenia')->name('milenia.index');
                Route::get('/{id}/detail', [AgreementLetterController::class, 'showMilenia'])->middleware('permission:lihat_surat_agreement_milenia')->name('milenia.show');
                Route::get('/tambah', [AgreementLetterController::class, 'createMilenia'])->middleware('permission:buat_surat_agreement_milenia')->name('milenia.create');
                Route::get('/{id}/edit', [AgreementLetterController::class, 'editMilenia'])->middleware('permission:ubah_surat_agreement_milenia')->name('milenia.edit');
            });

            // Map
            Route::prefix('map')->group(function () {
                Route::get('/', [AgreementLetterController::class, 'indexMap'])->middleware('permission:lihat_surat_agreement_map')->name('map.index');
                Route::get('/{id}/detail', [AgreementLetterController::class, 'showMap'])->middleware('permission:lihat_surat_agreement_map')->name('map.show');
                Route::get('/tambah', [AgreementLetterController::class, 'createMap'])->middleware('permission:buat_surat_agreement_map')->name('map.create');
                Route::get('/{id}/edit', [AgreementLetterController::class, 'editMap'])->middleware('permission:ubah_surat_agreement_map')->name('map.edit');
            });
        });
    });

    Route::prefix('promo-produk')->group(function () {
        // Route Menampilkan Data Pricelist Produk
        Route::prefix('pricelist-produk')->group(function () {
            Route::get('/landing', [ProductPricelistController::class, 'landing'])->name('pricelist-produk.landing');

            Route::prefix('milenia')->middleware('permission:lihat_data_pricelist_produk_milenia')->group(function () {
                Route::get('/', [ProductPricelistController::class, 'indexMilenia'])->name('pricelist-produk-milenia.index');
            });

            Route::prefix('map')->middleware('permission:lihat_data_pricelist_produk_map')->group(function () {
                Route::get('/', [ProductPricelistController::class, 'indexMap'])->name('pricelist-produk-map.index');
            });
        });

        // Route Menampilkan Data Promo Produk
        Route::prefix('program-promo')->name('promotion-program.')->group(function () {
            Route::get('/landing', [PromotionProgramController::class, 'landing'])->name('landing');
            Route::post('/store', [PromotionProgramController::class, 'store'])->name('store');
            Route::put('/{id}/update', [PromotionProgramController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [PromotionProgramController::class, 'destroy'])->name('destroy');

            Route::prefix('milenia')->group(function () {
                Route::get('/', [PromotionProgramController::class, 'indexMilenia'])->middleware('permission:lihat_program_promo_milenia')->name('milenia.index');
                Route::get('/{id}/detail', [PromotionProgramController::class, 'showMilenia'])->middleware('permission:lihat_program_promo_milenia')->name('milenia.show');
                Route::get('/search-items', [PromotionProgramController::class, 'searchItemsMilenia'])->name('milenia.searchItems');
                Route::get('/tambah', [PromotionProgramController::class, 'createMilenia'])->middleware('permission:buat_program_promo_milenia')->name('milenia.create');
                Route::get('/{id}/edit', [PromotionProgramController::class, 'editMilenia'])->middleware('permission:ubah_program_promo_milenia')->name('milenia.edit');
            });

            Route::prefix('map')->group(function () {
                Route::get('/', [PromotionProgramController::class, 'indexMap'])->middleware('permission:lihat_program_promo_map')->name('map.index');
                Route::get('/{id}/detail', [PromotionProgramController::class, 'showMap'])->middleware('permission:lihat_program_promo_map')->name('map.show');
                Route::get('/search-items', [PromotionProgramController::class, 'searchItemsMap'])->name('map.searchItems');
                Route::get('/tambah', [PromotionProgramController::class, 'createMap'])->middleware('permission:buat_program_promo_map')->name('map.create');
                Route::get('/{id}/edit', [PromotionProgramController::class, 'editMap'])->middleware('permission:ubah_program_promo_map')->name('map.edit');
            });
        });
    });

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
