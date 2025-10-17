<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Feat\CustomerController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Feat\SalespersonSalesController;
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

    Route::prefix('manajemen-user')->group(function () {
        // Route Master Karyawan
        Route::middleware('permission:manage_master')->group(function () {
            Route::get('user', [UserController::class, 'index'])->name('master-user.index');
            Route::get('user/tambah', [UserController::class, 'create'])->name('master-user.create');
            Route::post('user', [UserController::class, 'store'])->name('master-user.store');
            Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('master-user.edit');
            Route::put('user/{id}/update', [UserController::class, 'update'])->name('master-user.update');
            Route::put('user/{id}/delete', [UserController::class, 'delete'])->name('master-user.destroy');
            Route::get('/sync', [UserController::class, 'syncEmployeeData'])->name('master-user.sync');
        });

        // Route Master Role
        Route::middleware('permission:manage_roles')->group(function () {
            Route::get('peran', [RoleController::class, 'index'])->name('master-role.index');
            Route::get('peran/tambah', [RoleController::class, 'create'])->name('master-role.create');
            Route::post('peran', [RoleController::class, 'store'])->name('master-role.store');
            Route::get('peran/{id}/edit', [RoleController::class, 'edit'])->name('master-role.edit');
            Route::put('peran/{id}/update', [RoleController::class, 'update'])->name('master-role.update');
            Route::delete('peran/{id}/delete', [RoleController::class, 'destroy'])->name('master-role.destroy');
        });

        // Route Izin Akses
        Route::middleware('permission:manage_permissions')->group(function () {
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
        Route::prefix('data-customer-milenia')->middleware('permission:view_customer_milenia')->group(function () {
            Route::get('/', [CustomerController::class, 'indexMilenia'])->name('customer-data-milenia.index');
            Route::get('/{id}/detail', [CustomerController::class, 'showMilenia'])->name('customer-data-milenia.show');
        });

        Route::prefix('data-customer-mega')->middleware('permission:view_customer_map')->group(function () {
            Route::get('/', [CustomerController::class, 'indexMega'])->name('customer-data-map.index');
            Route::get('/{id}/detail', [CustomerController::class, 'showMega'])->name('customer-data-map.show');
        });

        // Route Transaksi Customer
        Route::prefix(('transaksi-pembelian-customer'))->group(function () {
            Route::get('/landing', [CustomerTransactionController::class, 'landing'])->name('customer-transaction.landing');

            // Route Menampilkan Data Transaksi Customer Milenia
            Route::prefix('milenia')->middleware('permission:view_customer_transaction_milenia_pusat')->group(function () {
                Route::get('/', [CustomerTransactionController::class, 'indexMilenia'])->name('customer-transaction-milenia.index');
                Route::get('/data', [CustomerTransactionController::class, 'getDataMilenia'])->name('customer-transaction-milenia.data');
                Route::get('/{id}/detail', [CustomerTransactionController::class, 'showMilenia'])->name('customer-transaction-milenia.show');
            });

            Route::prefix('milenia-cabang')->middleware('permission:view_customer_transaction_milenia_cabang')->group(function () {
                Route::get('/', [CustomerTransactionController::class, 'indexMileniaBranch'])->name('customer-transaction-milenia-branch.index');
                Route::get('/data', [CustomerTransactionController::class, 'getDataMileniaBranch'])->name('customer-transaction-milenia-branch.data');
                Route::get('/{id}/detail', [CustomerTransactionController::class, 'showMileniaBranch'])->name('customer-transaction-milenia-branch.show');
            });

            // Route Menampilkan Data Transaksi Customer Map
            Route::prefix('map')->middleware('permission:view_customer_transaction_map_pusat')->group(function () {
                Route::get('/', [CustomerTransactionController::class, 'indexMap'])->name('customer-transaction-map.index');
                Route::get('/data', [CustomerTransactionController::class, 'getDataMap'])->name('customer-transaction-map.data');
                Route::get('/{id}/detail', [CustomerTransactionController::class, 'showMap'])->name('customer-transaction-map.show');
            });

            Route::prefix('map-cabang')->middleware('permission:view_customer_transaction_map_cabang')->group(function () {
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
            Route::prefix('milenia')->middleware('permission:view_salesperson_sales_milenia_pusat')->group(function () {
                Route::get('/', [SalespersonSalesController::class, 'indexMilenia'])->name('transactions.milenia.index');
                Route::get('/data', [SalespersonSalesController::class, 'getDataMilenia'])->name('transactions.milenia.data');
                Route::get('/{id}/detail', [SalespersonSalesController::class, 'showMilenia'])->name('transactions.milenia.show');
                Route::get('/{id}/transactions', [SalespersonSalesController::class, 'getSalespersonTransactionsDataMilenia'])->name('transactions.milenia.data.details');
                Route::get('/{id}/export-sales-by-brand', [SalespersonSalesController::class, 'exportSalesMileniaByBrand'])->name('transactions.milenia.export-sales-by-brand');
            });

            Route::prefix('milenia-cabang')->middleware('permission:view_salesperson_sales_milenia_cabang')->group(function () {
                Route::get('/', [SalespersonSalesController::class, 'indexMileniaBranch'])->name('transactions.milenia.branch.index');
                Route::get('/data', [SalespersonSalesController::class, 'getDataMileniaBranch'])->name('transactions.milenia.branch.data');
                Route::get('/{id}/detail', [SalespersonSalesController::class, 'showMileniaBranch'])->name('transactions.milenia.branch.show');
                Route::get('/{id}/transactions', [SalespersonSalesController::class, 'getSalespersonTransactionsDataMileniaBranch'])->name('transactions.milenia.data.branch.details');
                Route::get('/{id}/export-sales-by-brand', [SalespersonSalesController::class, 'exportSalesMileniaBranchByBrand'])->name('transactions.milenia-branch.export-sales-by-brand');
            });

            // Route Menampilkan Data Penjualan Sales
            Route::prefix('map')->middleware('permission:view_salesperson_sales_map_pusat')->group(function () {
                Route::get('/', [SalespersonSalesController::class, 'indexMap'])->name('transactions.map.index');
                Route::get('/data', [SalespersonSalesController::class, 'getDataMap'])->name('transactions.map.data');
                Route::get('/{id}/detail', [SalespersonSalesController::class, 'showMap'])->name('transactions.map.show');
                Route::get('/{id}/transactions', [SalespersonSalesController::class, 'getSalespersonTransactionsDataMap'])->name('transactions.map.data.details');
                Route::get('/{id}/export-sales-by-brand', [SalespersonSalesController::class, 'exportSalesMapByBrand'])->name('transactions.map.export-sales-by-brand');
            });

            Route::prefix('map-cabang')->middleware('permission:view_salesperson_sales_map_cabang')->group(function () {
                Route::get('/', [SalespersonSalesController::class, 'indexMapBranch'])->name('transactions.map.branch.index');
                Route::get('/data', [SalespersonSalesController::class, 'getDataMapBranch'])->name('transactions.map.branch.data');
                Route::get('/{id}/detail', [SalespersonSalesController::class, 'showMapBranch'])->name('transactions.map.branch.show');
                Route::get('/{id}/transactions', [SalespersonSalesController::class, 'getSalespersonTransactionsDataMapBranch'])->name('transactions.map.data.branch.details');
                Route::get('/{id}/export-sales-by-brand', [SalespersonSalesController::class, 'exportSalesMapBranchByBrand'])->name('transactions.map-branch.export-sales-by-brand');
            });
        });
    });

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
