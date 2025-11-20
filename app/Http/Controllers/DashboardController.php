<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
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
    public function index(Request $request)
    {
        // 1. Ambil input tanggal, set default jika kosong
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // 2. Konversi ke format DateTime SQL agar akurat (00:00:00 s/d 23:59:59)
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // --- MILENIA ---
        $subSalesMilenia = DB::connection('sqlsrv_wh')
            ->table('SOIVD')
            ->select(
                'SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD_LineInvoiceAmount) AS total_amount')
            )
            // Ubah filter tanggal
            ->whereBetween('SOIVD_OrderDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD_SalesmanID');

        $subReturnMilenia = DB::connection('sqlsrv_wh')
            ->table('SOORH')
            ->select(
                'SOORH_SalesmanID',
                DB::raw('SUM(SOORH_ReturnAmount - SOORH_TaxAmount) AS total_return_amount')
            )
            // Ubah filter tanggal
            ->whereBetween('SOORH_ReturnDate', [$startDateTime, $endDateTime])
            ->groupBy('SOORH_SalesmanID');

        $salesManSalesMilenia = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$subSalesMilenia->toSql()}) as s"))
            ->mergeBindings($subSalesMilenia)
            ->leftJoin(DB::raw("({$subReturnMilenia->toSql()}) as r"), 'r.SOORH_SalesmanID', '=', 's.SOIVD_SalesmanID')
            ->mergeBindings($subReturnMilenia)
            ->join('MFSSM as m', 'm.MFSSM_SalesmanID', '=', 's.SOIVD_SalesmanID')
            ->selectRaw("
            s.SOIVD_SalesmanID,
            m.MFSSM_Description AS salesman_name,
            s.total_transaksi,
            s.total_qty,
            s.total_amount,
            COALESCE(r.total_return_amount, 0) AS total_return_amount,
            (ISNULL(s.total_amount, 0) - ISNULL(r.total_return_amount, 0)) AS net_amount
        ")
            ->orderByDesc('s.total_amount')
            ->get();

        // --- MILENIA BRANCH ---
        $subSalesMileniaBranch = DB::connection('sqlsrv_wh')
            ->table('SOIVD_Cabang')
            ->select(
                'SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVD_OrderDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD_SalesmanID');

        $subReturnMileniaBranch = DB::connection('sqlsrv_wh')
            ->table('SOORH_Cabang')
            ->select(
                'SOORH_SalesmanID',
                DB::raw('SUM(SOORH_ReturnAmount - SOORH_TaxAmount) AS total_return_amount')
            )
            ->whereBetween('SOORH_ReturnDate', [$startDateTime, $endDateTime])
            ->groupBy('SOORH_SalesmanID');

        $salesManSalesMileniaBranch = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$subSalesMileniaBranch->toSql()}) as s"))
            ->mergeBindings($subSalesMileniaBranch)
            ->leftJoin(DB::raw("({$subReturnMileniaBranch->toSql()}) as r"), 'r.SOORH_SalesmanID', '=', 's.SOIVD_SalesmanID')
            ->mergeBindings($subReturnMileniaBranch)
            ->join('MFSSM as m', 'm.MFSSM_SalesmanID', '=', 's.SOIVD_SalesmanID')
            ->selectRaw("
            s.SOIVD_SalesmanID,
            m.MFSSM_Description AS salesman_name,
            s.total_transaksi,
            s.total_qty,
            s.total_amount,
            COALESCE(r.total_return_amount, 0) AS total_return_amount,
            (ISNULL(s.total_amount, 0) - ISNULL(r.total_return_amount, 0)) AS net_amount
        ")
            ->orderByDesc('s.total_amount')
            ->get();

        // --- MAP ---
        $subSalesMap = DB::connection('sqlsrv_snx')
            ->table('SOIVD')
            ->select(
                'SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVD_OrderDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD_SalesmanID');

        $subReturnMap = DB::connection('sqlsrv_snx')
            ->table('SOORH')
            ->select(
                'SOORH_SalesmanID',
                DB::raw('SUM(SOORH_ReturnAmount - SOORH_TaxAmount) AS total_return_amount')
            )
            ->whereBetween('SOORH_ReturnDate', [$startDateTime, $endDateTime])
            ->groupBy('SOORH_SalesmanID');

        $salesManSalesMap = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$subSalesMap->toSql()}) as s"))
            ->mergeBindings($subSalesMap)
            ->leftJoin(DB::raw("({$subReturnMap->toSql()}) as r"), 'r.SOORH_SalesmanID', '=', 's.SOIVD_SalesmanID')
            ->mergeBindings($subReturnMap)
            ->join('MFSSM as m', 'm.MFSSM_SalesmanID', '=', 's.SOIVD_SalesmanID')
            ->selectRaw("
            s.SOIVD_SalesmanID,
            m.MFSSM_Description AS salesman_name,
            s.total_transaksi,
            s.total_qty,
            s.total_amount,
            COALESCE(r.total_return_amount, 0) AS total_return_amount,
            (ISNULL(s.total_amount, 0) - ISNULL(r.total_return_amount, 0)) AS net_amount
        ")
            ->orderByDesc('s.total_amount')
            ->get();

        // --- MAP BRANCH ---
        $subSalesMapBranch = DB::connection('sqlsrv_snx')
            ->table('SOIVD_Cabang')
            ->select(
                'SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVD_OrderDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD_SalesmanID');

        $subReturnMapBranch = DB::connection('sqlsrv_snx')
            ->table('SOORH_Cabang')
            ->select(
                'SOORH_SalesmanID',
                DB::raw('SUM(SOORH_ReturnAmount - SOORH_TaxAmount) AS total_return_amount')
            )
            ->whereBetween('SOORH_ReturnDate', [$startDateTime, $endDateTime])
            ->groupBy('SOORH_SalesmanID');

        $salesManSalesMapBranch = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$subSalesMapBranch->toSql()}) as s"))
            ->mergeBindings($subSalesMapBranch)
            ->leftJoin(DB::raw("({$subReturnMapBranch->toSql()}) as r"), 'r.SOORH_SalesmanID', '=', 's.SOIVD_SalesmanID')
            ->mergeBindings($subReturnMapBranch)
            ->join('MFSSM as m', 'm.MFSSM_SalesmanID', '=', 's.SOIVD_SalesmanID')
            ->selectRaw("
            s.SOIVD_SalesmanID,
            m.MFSSM_Description AS salesman_name,
            s.total_transaksi,
            s.total_qty,
            s.total_amount,
            COALESCE(r.total_return_amount, 0) AS total_return_amount,
            (ISNULL(s.total_amount, 0) - ISNULL(r.total_return_amount, 0)) AS net_amount
        ")
            ->orderByDesc('s.total_amount')
            ->get();

        $totalSummary = [
            'total_qty' =>
            $salesManSalesMilenia->sum('total_qty') +
                $salesManSalesMileniaBranch->sum('total_qty') +
                $salesManSalesMap->sum('total_qty') +
                $salesManSalesMapBranch->sum('total_qty'),

            'total_amount' =>
            $salesManSalesMilenia->sum('net_amount') +
                $salesManSalesMileniaBranch->sum('net_amount') +
                $salesManSalesMap->sum('net_amount') +
                $salesManSalesMapBranch->sum('net_amount'),
        ];

        $totalCustomerNetwork = CustomerNetwork::where('is_active', 1)
            ->count();

        $totalActiveUser = User::where('Aktif', 1)
            ->where('ID', '!=', 1)
            ->count();

        $taxActive = Tax::where('is_active', 1)->first();

        // Filter Pricelist juga diupdate ke Date Range
        $updatedPricelistsMilenia = PricelistMilenia::with('ItemMilenia')
            ->whereBetween('SOMPD_UPDATE', [$startDateTime, $endDateTime])
            ->orderByRaw('SOMPD_UPDATE DESC')
            ->get();

        $updatedPricelistsMap = PricelistMap::with('ItemMap')
            ->whereBetween('SOMPD_UPDATE', [$startDateTime, $endDateTime])
            ->orderByRaw('SOMPD_UPDATE DESC')
            ->get();

        // brand performance milenia and map
        $brandTransactionMilenia = SalesOrderDetailMilenia::getBrandPerformanceDashboard($startDateTime, $endDateTime);
        $brandTransactionMap = SalesOrderDetailMap::getBrandPerformanceDashboard($startDateTime, $endDateTime);

        // brand performance milenia and map branch
        $brandTransactionMileniaBranch = SalesOrderDetailMileniaBranch::getBrandPerformanceDashboard($startDateTime, $endDateTime);
        $brandTransactionMapBranch = SalesOrderDetailMapBranch::getBrandPerformanceDashboard($startDateTime, $endDateTime);

        // sales per customer
        $finalReportMilenia = SalesOrderDetailMilenia::getCustomerPerformanceDashboard($startDateTime, $endDateTime);
        $finalReportMap = SalesOrderDetailMap::getCustomerPerformanceDashboard($startDateTime, $endDateTime);

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
            'finalReportMap',
            'startDate',
            'endDate',
            'taxActive'
        ));
    }
}
