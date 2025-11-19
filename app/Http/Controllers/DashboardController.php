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
        $salesManSalesMilenia = SalesOrderDetailMilenia::query()
            ->join('MFSSM', 'SOIVD.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
            ->whereMonth('SOIVD.SOIVD_OrderDate', now()->month)
            ->whereYear('SOIVD.SOIVD_OrderDate', now()->year)
            ->selectRaw("
                SOIVD.SOIVD_SalesmanID,
                MFSSM.MFSSM_Description as salesman_name,
                COUNT(DISTINCT SOIVD.SOIVD_InvoiceID) as total_transaksi,
                SUM(SOIVD.SOIVD_OrderQty) as total_qty,
                SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_amount
            ")
            ->groupBy('SOIVD.SOIVD_SalesmanID', 'MFSSM.MFSSM_Description')
            ->orderByDesc('total_amount')
            ->get();

        $salesManSalesMileniaBranch = SalesOrderDetailMileniaBranch::query()
            ->join('MFSSM', 'SOIVD_Cabang.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
            ->whereMonth('SOIVD_Cabang.SOIVD_OrderDate', now()->month)
            ->whereYear('SOIVD_Cabang.SOIVD_OrderDate', now()->year)
            ->selectRaw("
                SOIVD_Cabang.SOIVD_SalesmanID,
                MFSSM.MFSSM_Description as salesman_name,
                COUNT(DISTINCT SOIVD_Cabang.SOIVD_InvoiceID) as total_transaksi,
                SUM(SOIVD_Cabang.SOIVD_OrderQty) as total_qty,
                SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) as total_amount
            ")
            ->groupBy('SOIVD_Cabang.SOIVD_SalesmanID', 'MFSSM.MFSSM_Description')
            ->orderByDesc('total_amount')
            ->get();


        $salesManSalesMap = SalesOrderDetailMap::query()
            ->join('MFSSM', 'SOIVD.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
            ->whereMonth('SOIVD.SOIVD_OrderDate', now()->month)
            ->whereYear('SOIVD.SOIVD_OrderDate', now()->year)
            ->selectRaw("
                SOIVD.SOIVD_SalesmanID,
                MFSSM.MFSSM_Description as salesman_name,
                COUNT(DISTINCT SOIVD.SOIVD_InvoiceID) as total_transaksi,
                SUM(SOIVD.SOIVD_OrderQty) as total_qty,
                SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_amount
            ")
            ->groupBy('SOIVD.SOIVD_SalesmanID', 'MFSSM.MFSSM_Description')
            ->orderByDesc('total_amount')
            ->get();

        $salesManSalesMapBranch = SalesOrderDetailMapBranch::query()
            ->join('MFSSM', 'SOIVD_Cabang.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
            ->whereMonth('SOIVD_Cabang.SOIVD_OrderDate', now()->month)
            ->whereYear('SOIVD_Cabang.SOIVD_OrderDate', now()->year)
            ->selectRaw("
                SOIVD_Cabang.SOIVD_SalesmanID,
                MFSSM.MFSSM_Description as salesman_name,
                COUNT(DISTINCT SOIVD_Cabang.SOIVD_InvoiceID) as total_transaksi,
                SUM(SOIVD_Cabang.SOIVD_OrderQty) as total_qty,
                SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) as total_amount
            ")
            ->groupBy('SOIVD_Cabang.SOIVD_SalesmanID', 'MFSSM.MFSSM_Description')
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
            ->join('MFIMA', 'SOIVD.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
            ->whereMonth('SOIVD.SOIVD_OrderDate', now()->month)
            ->whereYear('SOIVD.SOIVD_OrderDate', now()->year)
            ->groupBy('MFIB.MFIB_BrandID', 'MFIB.MFIB_Description')
            ->orderBy('total_sales', 'desc')
            ->get();

        $brandTransactionMap = SalesOrderDetailMap::query()
            ->select(
                'MFIB.MFIB_BrandID',
                'MFIB.MFIB_Description as brand_name',
                DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_sales')
        )
            ->join('MFIMA', 'SOIVD.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
            ->whereMonth('SOIVD.SOIVD_OrderDate', now()->month)
            ->whereYear('SOIVD.SOIVD_OrderDate', now()->year)
            ->groupBy('MFIB.MFIB_BrandID', 'MFIB.MFIB_Description')
            ->orderBy('total_sales', 'desc')
            ->get();

        $brandTransactionMileniaBranch = SalesOrderDetailMileniaBranch::query()
            ->select(
                'MFIB.MFIB_BrandID',
                'MFIB.MFIB_Description as brand_name',
                DB::raw('SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) as total_sales')
        )
            ->join('MFIMA', 'SOIVD_Cabang.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
            ->whereMonth('SOIVD_Cabang.SOIVD_OrderDate', now()->month)
            ->whereYear('SOIVD_Cabang.SOIVD_OrderDate', now()->year)
            ->groupBy('MFIB.MFIB_BrandID', 'MFIB.MFIB_Description')
            ->orderBy('total_sales', 'desc')
            ->get();

        $brandTransactionMapBranch = SalesOrderDetailMapBranch::query()
            ->select(
                'MFIB.MFIB_BrandID',
                'MFIB.MFIB_Description as brand_name',
                DB::raw('SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) as total_sales')
        )
            ->join('MFIMA', 'SOIVD_Cabang.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
            ->whereMonth('SOIVD_Cabang.SOIVD_OrderDate', now()->month)
            ->whereYear('SOIVD_Cabang.SOIVD_OrderDate', now()->year)
            ->groupBy('MFIB.MFIB_BrandID', 'MFIB.MFIB_Description')
            ->orderBy('total_sales', 'desc')
            ->get();


        // sales per customer
        // Query Sales Main Milenia
        $salesMainMileniaperCustomer = DB::connection('sqlsrv_wh')->table('SOIVD')
            ->select(
            'SOIVH.SOIVH_CustomerID',
            DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->leftJoin('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->whereMonth('SOIVH.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH.SOIVH_InvoiceDate', now()->year)
            ->groupBy('SOIVH.SOIVH_CustomerID');

        // Query Sales Branch Milenia
        $salesBranchMileniaperCustomer = DB::connection('sqlsrv_wh')->table('SOIVD_Cabang')
            ->select(
            'SOIVH_Cabang.SOIVH_CustomerID',
            DB::raw('SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->leftJoin('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->whereMonth('SOIVH_Cabang.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH_Cabang.SOIVH_InvoiceDate', now()->year)
            ->groupBy('SOIVH_Cabang.SOIVH_CustomerID');


        // Merge Milenia Query
        $salesMainMileniaperCustomer = $salesMainMileniaperCustomer
            ->unionAll($salesBranchMileniaperCustomer);

        // Final Report Milenia
        $finalReportMilenia = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$salesMainMileniaperCustomer->toSql()}) as all_sales"))
            ->mergeBindings($salesMainMileniaperCustomer)
            ->join('MFCUS', 'all_sales.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
            ->select(
            'all_sales.SOIVH_CustomerID as MFCUS_CustomerID',
            'MFCUS.MFCUS_Description as customer_name',
            DB::raw('SUM(all_sales.total_sales) as total_sales')
            )
            ->groupBy('all_sales.SOIVH_CustomerID', 'MFCUS.MFCUS_Description')
            ->orderByDesc('total_sales')
            ->get();

        // Query Sales Main Map
        $salesMainMapperCustomer = DB::connection('sqlsrv_snx')->table('SOIVD')
            ->select(
            'SOIVH.SOIVH_CustomerID',
            DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->leftJoin('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->whereMonth('SOIVH.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH.SOIVH_InvoiceDate', now()->year)
            ->groupBy('SOIVH.SOIVH_CustomerID');

        // Query Sales Branch Map
        $salesBranchMapperCustomer = DB::connection('sqlsrv_snx')->table('SOIVD_Cabang')
            ->select(
            'SOIVH_Cabang.SOIVH_CustomerID',
            DB::raw('SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->leftJoin('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->whereMonth('SOIVH_Cabang.SOIVH_InvoiceDate', now()->month)
            ->whereYear('SOIVH_Cabang.SOIVH_InvoiceDate', now()->year)
            ->groupBy('SOIVH_Cabang.SOIVH_CustomerID');


        // Merge Map Query
        $salesMainMapperCustomer = $salesMainMapperCustomer
            ->unionAll($salesBranchMapperCustomer);

        // Final Report Map
        $finalReportMap = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$salesMainMapperCustomer->toSql()}) as all_sales"))
            ->mergeBindings($salesMainMapperCustomer)
            ->join('MFCUS', 'all_sales.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
            ->select(
            'all_sales.SOIVH_CustomerID as MFCUS_CustomerID',
            'MFCUS.MFCUS_Description as customer_name',
            DB::raw('SUM(all_sales.total_sales) as total_sales')
            )
            ->groupBy('all_sales.SOIVH_CustomerID', 'MFCUS.MFCUS_Description')
            ->orderByDesc('total_sales')
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
