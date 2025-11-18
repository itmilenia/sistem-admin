<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PricelistMap;
use Illuminate\Http\Request;
use App\Models\CustomerNetwork;
use App\Models\PricelistMilenia;
use Illuminate\Support\Facades\DB;
use App\Models\SalesOrderDetailMap;
use App\Models\SalesOrderDetailMilenia;
use App\Models\SalesOrderDetailMapBranch;
use App\Models\SalesOrderDetailMileniaBranch;

class DashboardController extends Controller
{
    public function index()
    {
        $salesManSalesMilenia = SalesOrderDetailMilenia::with('salesManMilenia')
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->whereMonth('SOIVH.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH.SOIVH_InvoiceDate', now()->year)
            ->selectRaw('
            SOIVD.SOIVD_SalesmanID,
            COUNT(*) as total_transaksi,
            SUM(SOIVD.SOIVD_OrderQty) as total_qty,
            SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_amount
        ')
            ->groupBy('SOIVD.SOIVD_SalesmanID')
            ->orderByDesc('total_amount')
            ->get();

        $salesManSalesMileniaBranch = SalesOrderDetailMileniaBranch::with('salesManMileniaBranch')
            ->join('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->whereMonth('SOIVH_Cabang.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH_Cabang.SOIVH_InvoiceDate', now()->year)
            ->selectRaw('
            SOIVD_Cabang.SOIVD_SalesmanID,
            COUNT(*) as total_transaksi,
            SUM(SOIVD_Cabang.SOIVD_OrderQty) as total_qty,
            SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) as total_amount
        ')
            ->groupBy('SOIVD_Cabang.SOIVD_SalesmanID')
            ->orderByDesc('total_amount')
            ->get();

        $salesManSalesMap = SalesOrderDetailMap::with('salesManMap')
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->whereMonth('SOIVH.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH.SOIVH_InvoiceDate', now()->year)
            ->selectRaw('
            SOIVD.SOIVD_SalesmanID,
            COUNT(*) as total_transaksi,
            SUM(SOIVD.SOIVD_OrderQty) as total_qty,
            SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_amount
        ')
            ->groupBy('SOIVD.SOIVD_SalesmanID')
            ->orderByDesc('total_amount')
            ->get();

        $salesManSalesMapBranch = SalesOrderDetailMapBranch::with('salesManMapBranch')
            ->join('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->whereMonth('SOIVH_Cabang.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH_Cabang.SOIVH_InvoiceDate', now()->year)
            ->selectRaw('
            SOIVD_Cabang.SOIVD_SalesmanID,
            COUNT(*) as total_transaksi,
            SUM(SOIVD_Cabang.SOIVD_OrderQty) as total_qty,
            SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) as total_amount
        ')
            ->groupBy('SOIVD_Cabang.SOIVD_SalesmanID')
            ->orderByDesc('total_amount')
            ->get();

        $totalSummary = [
            'total_qty' =>
            $salesManSalesMilenia->sum('total_qty') +
                $salesManSalesMileniaBranch->sum('total_qty') +
                $salesManSalesMap->sum('total_qty') +
                $salesManSalesMapBranch->sum('total_qty'),

            'total_amount' =>
            $salesManSalesMilenia->sum('total_amount') +
                $salesManSalesMileniaBranch->sum('total_amount') +
                $salesManSalesMap->sum('total_amount') +
                $salesManSalesMapBranch->sum('total_amount'),
        ];

        $totalCustomerNetwork = CustomerNetwork::where('is_active', 1)
            ->count();

        $totalActiveUser = User::where('Aktif', 1)
            ->where('ID', '!=', 1)
            ->count();

        $updatedPricelistsMilenia = PricelistMilenia::with('ItemMilenia')
            ->whereMonth('SOMPD_UPDATE', now()->month)
            ->whereYear('SOMPD_UPDATE', now()->year)
            ->orderByRaw('SOMPD_UPDATE DESC')
            ->get();

        $updatedPricelistsMap = PricelistMap::with('ItemMap')
            ->whereMonth('SOMPD_UPDATE', now()->month)
            ->whereYear('SOMPD_UPDATE', now()->year)
            ->orderByRaw('SOMPD_UPDATE DESC')
            ->get();

        $brandTransactionMilenia = SalesOrderDetailMilenia::query()
            ->select(
                'MFIB.MFIB_BrandID',
                'MFIB.MFIB_Description as brand_name',
                DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->join('MFIMA', 'SOIVD.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
            ->whereMonth('SOIVH.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH.SOIVH_InvoiceDate', now()->year)
            ->groupBy('MFIB.MFIB_BrandID', 'MFIB.MFIB_Description')
            ->orderBy('total_sales', 'desc')
            ->get();

        $brandTransactionMap = SalesOrderDetailMap::query()
            ->select(
                'MFIB.MFIB_BrandID',
                'MFIB.MFIB_Description as brand_name',
                DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->join('MFIMA', 'SOIVD.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
            ->whereMonth('SOIVH.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH.SOIVH_InvoiceDate', now()->year)
            ->groupBy('MFIB.MFIB_BrandID', 'MFIB.MFIB_Description')
            ->orderBy('total_sales', 'desc')
            ->get();

        $brandTransactionMileniaBranch = SalesOrderDetailMileniaBranch::query()
            ->select(
                'MFIB.MFIB_BrandID',
                'MFIB.MFIB_Description as brand_name',
                DB::raw('SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->join('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->join('MFIMA', 'SOIVD_Cabang.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
            ->whereMonth('SOIVH_Cabang.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH_Cabang.SOIVH_InvoiceDate', now()->year)
            ->groupBy('MFIB.MFIB_BrandID', 'MFIB.MFIB_Description')
            ->orderBy('total_sales', 'desc')
            ->get();

        $brandTransactionMapBranch = SalesOrderDetailMapBranch::query()
            ->select(
                'MFIB.MFIB_BrandID',
                'MFIB.MFIB_Description as brand_name',
                DB::raw('SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->join('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->join('MFIMA', 'SOIVD_Cabang.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
            ->whereMonth('SOIVH_Cabang.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH_Cabang.SOIVH_InvoiceDate', now()->year)
            ->groupBy('MFIB.MFIB_BrandID', 'MFIB.MFIB_Description')
            ->orderBy('total_sales', 'desc')
            ->get();

        // Transaksi per Customer Network
        // --- Kueri 1: Penjualan dari SOIVD (Tabel Utama) ---
        $salesMainMileniaperCustomer = DB::connection('sqlsrv_wh')->table('SOIVD')
            ->select(
                'MFCUS.MFCUS_CustomerID',
                'MFCUS.MFCUS_Description',
                'SOIVD.SOIVD_LineInvoiceAmount'
            )
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->join('MFCUS', 'SOIVH.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
            ->whereMonth('SOIVH.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH.SOIVH_InvoiceDate', now()->year);

        // --- Kueri 2: Penjualan dari SOIVD_Cabang (Tabel Cabang) ---
        $salesBranchMileniaperCustomer = DB::connection('sqlsrv_wh')->table('SOIVD_Cabang')
            ->select(
                'MFCUS.MFCUS_CustomerID',
                'MFCUS.MFCUS_Description',
                'SOIVD_Cabang.SOIVD_LineInvoiceAmount'
            )
            ->join('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->join('MFCUS', 'SOIVH_Cabang.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
            ->whereMonth('SOIVH_Cabang.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH_Cabang.SOIVH_InvoiceDate', now()->year);


        // --- Gabungkan kedua kueri ---
        $salesMainMileniaperCustomer->unionAll($salesBranchMileniaperCustomer);

        $finalReportMilenia = DB::connection('sqlsrv_wh')
            ->query()
            ->fromSub($salesMainMileniaperCustomer, 'all_sales')
            ->select(
                'MFCUS_CustomerID',
                'MFCUS_Description as customer_name',
                DB::raw('SUM(SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->groupBy('MFCUS_CustomerID', 'MFCUS_Description')
            ->orderBy('total_sales', 'desc')
            ->get();

        // Transaksi per Customer Network
        // --- Kueri 1: Penjualan dari SOIVD (Tabel Utama) ---
        $salesMainMapperCustomer = DB::connection('sqlsrv_snx')->table('SOIVD')
            ->select(
                'MFCUS.MFCUS_CustomerID',
                'MFCUS.MFCUS_Description',
                'SOIVD.SOIVD_LineInvoiceAmount'
            )
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->join('MFCUS', 'SOIVH.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
            ->whereMonth('SOIVH.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH.SOIVH_InvoiceDate', now()->year);

        // --- Kueri 2: Penjualan dari SOIVD_Cabang (Tabel Cabang) ---
        $salesBranchMapperCustomer = DB::connection('sqlsrv_snx')->table('SOIVD_Cabang')
            ->select(
                'MFCUS.MFCUS_CustomerID',
                'MFCUS.MFCUS_Description',
                'SOIVD_Cabang.SOIVD_LineInvoiceAmount'
            )
            ->join('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->join('MFCUS', 'SOIVH_Cabang.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
            ->whereMonth('SOIVH_Cabang.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH_Cabang.SOIVH_InvoiceDate', now()->year);


        // --- Gabungkan kedua kueri ---
        $salesMainMapperCustomer->unionAll($salesBranchMapperCustomer);

        $finalReportMap = DB::connection('sqlsrv_snx')
            ->query()
            ->fromSub($salesMainMapperCustomer, 'all_sales')
            ->select(
                'MFCUS_CustomerID',
                'MFCUS_Description as customer_name',
                DB::raw('SUM(SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->groupBy('MFCUS_CustomerID', 'MFCUS_Description')
            ->orderBy('total_sales', 'desc')
            ->get();

        return view('pages.dashboard.index', compact(
            'salesManSalesMilenia',
            'salesManSalesMileniaBranch',
            'salesManSalesMap',
            'salesManSalesMapBranch',
            'totalSummary',
            'totalCustomerNetwork',
            'totalActiveUser',
            'updatedPricelistsMilenia',
            'updatedPricelistsMap',
            'brandTransactionMilenia',
            'brandTransactionMileniaBranch',
            'brandTransactionMap',
            'brandTransactionMapBranch',
            'finalReportMilenia',
            'finalReportMap'
        ));
    }
}
