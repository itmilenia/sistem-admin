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
        Route::prefix(('transaksi-pembelian-customer'))->middleware('permission:view_customer_transaction')->group(function () {
            Route::get('/', [CustomerTransactionController::class, 'index'])->name('customer-transaction.index');
            Route::get('/data', [CustomerTransactionController::class, 'getData'])->name('customer-transaction.data');
            Route::get('/{id}/detail', [CustomerTransactionController::class, 'show'])->name('customer-transaction.show');
        });
    });

    Route::prefix('sales')->group(function () {
        Route::prefix('data-penjualan-sales')->name('salesperson-sales.')->middleware('permission:view_salesperson_sales')->group(function () {
            Route::get('/', [SalespersonSalesController::class, 'index'])->name('index');
            Route::get('/data', [SalespersonSalesController::class, 'getData'])->name('data');
            Route::get('/{id}/detail', [SalespersonSalesController::class, 'show'])->name('show');
            Route::get('/{id}/transactions', [SalespersonSalesController::class, 'getSalespersonTransactionsData'])->name('transactions.data');
        });
    });

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
