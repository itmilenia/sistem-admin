<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PricelistMap;
use Illuminate\Http\Request;
use App\Models\CustomerNetwork;
use App\Models\PricelistMilenia;
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

        return view('pages.dashboard.index', compact(
            'salesManSalesMilenia',
            'salesManSalesMileniaBranch',
            'salesManSalesMap',
            'salesManSalesMapBranch',
            'totalSummary',
            'totalCustomerNetwork',
            'totalActiveUser',
            'updatedPricelistsMilenia',
            'updatedPricelistsMap'
        ));
    }
}
