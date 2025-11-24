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

        // 1. CTE_Invoice_Salesman: MAPPING INVOICE -> SALESMAN
        // Mencari siapa salesman dominan untuk setiap invoice (mencegah duplikasi)
        $invoiceSalesmanMilenia = DB::connection('sqlsrv_wh')
            ->table('SOIVD')
            ->select('SOIVD_InvoiceID', DB::raw('MAX(SOIVD_SalesmanID) as SalesmanID'))
            ->groupBy('SOIVD_InvoiceID');

        // 2. CTE_Fix_Unique_Returns: ISOLASI RETURN
        $uniqueReturnsMilenia = DB::connection('sqlsrv_wh')
            ->table('SOORH')
            ->join('SOORD', 'SOORH.SOORH_ReturnID', '=', 'SOORD.SOORD_ReturnID')
            ->select(
            'SOORH.SOORH_ReturnID',
            DB::raw('MAX(SOORD.SOORD_InvoiceID) as Ref_InvoiceID'),
            DB::raw('MAX(SOORH.SOORH_ReturnAmount - ISNULL(SOORH.SOORH_TaxAmount, 0)) AS NetReturnAmount')
            )
            ->groupBy('SOORH.SOORH_ReturnID');

        // 3. CTE_Filtered_Returns: FILTER TANGGAL & TOTAL PER SALESMAN
        // Menggabungkan Data Retur Unik + Tanggal Invoice + Mapping Salesman
        $returnPerSalesmanMilenia = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$uniqueReturnsMilenia->toSql()}) as R"))
            ->mergeBindings($uniqueReturnsMilenia)
            ->join('SOIVH as H', 'R.Ref_InvoiceID', '=', 'H.SOIVH_InvoiceID')
            ->leftJoin(DB::raw("({$invoiceSalesmanMilenia->toSql()}) as Map"), 'R.Ref_InvoiceID', '=', 'Map.SOIVD_InvoiceID')
            ->mergeBindings($invoiceSalesmanMilenia)
            ->select(
            'Map.SalesmanID',
            DB::raw('SUM(R.NetReturnAmount) as TotalReturnAmount')
            )
            ->whereBetween('H.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('Map.SalesmanID');

        // 4. CTE_SalesData: QUERY SALES UTAMA
        $salesDataMilenia = DB::connection('sqlsrv_wh')
            ->table('SOIVD')
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->select(
                'SOIVD.SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD.SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD.SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVH.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD.SOIVD_SalesmanID');

        // 5. FINAL SELECT (GABUNGAN)
        $salesManSalesMilenia = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$salesDataMilenia->toSql()}) as S"))
            ->mergeBindings($salesDataMilenia)
            ->leftJoin(DB::raw("({$returnPerSalesmanMilenia->toSql()}) as R"), 'S.SOIVD_SalesmanID', '=', 'R.SalesmanID')
            ->mergeBindings($returnPerSalesmanMilenia)
            ->leftJoin('MFSSM as M', 'S.SOIVD_SalesmanID', '=', 'M.MFSSM_SalesmanID')
            ->selectRaw("
                S.SOIVD_SalesmanID,
                ISNULL(M.MFSSM_Description, 'Unknown Salesman') AS salesman_name,
                S.total_transaksi,
                S.total_qty,
                S.total_amount,
                ISNULL(R.TotalReturnAmount, 0) AS total_return_amount,
                (ISNULL(S.total_amount, 0) - ISNULL(R.TotalReturnAmount, 0)) AS net_amount
            ")
            ->orderByDesc('S.total_amount')
            ->get();

        // --- MILENIA BRANCH (CABANG) ---
        // 1. CTE_Invoice_Salesman: MAPPING INVOICE -> SALESMAN
        $invoiceSalesmanMileniaBranch = DB::connection('sqlsrv_wh')
            ->table('SOIVD_Cabang')
            ->select('SOIVD_InvoiceID', DB::raw('MAX(SOIVD_SalesmanID) as SalesmanID'))
            ->groupBy('SOIVD_InvoiceID');

        // 2. CTE_Fix_Unique_Returns: ISOLASI RETURN
        // Memastikan nominal retur hanya diambil 1x per Nomor Retur (Header)
        // Walaupun barangnya banyak, nominal tidak dikali jumlah barang
        $uniqueReturnsMileniaBranch = DB::connection('sqlsrv_wh')
            ->table('SOORH_Cabang')
            ->join('SOORD_Cabang', 'SOORH_Cabang.SOORH_ReturnID', '=', 'SOORD_Cabang.SOORD_ReturnID')
            ->select(
            'SOORH_Cabang.SOORH_ReturnID',
            DB::raw('MAX(SOORD_Cabang.SOORD_InvoiceID) as Ref_InvoiceID'),
            DB::raw('MAX(SOORH_Cabang.SOORH_ReturnAmount - ISNULL(SOORH_Cabang.SOORH_TaxAmount, 0)) AS NetReturnAmount')
            )
            ->groupBy('SOORH_Cabang.SOORH_ReturnID');

        // 3. CTE_Filtered_Returns: FILTER TANGGAL & TOTAL PER SALESMAN
        $returnPerSalesmanMileniaBranch = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$uniqueReturnsMileniaBranch->toSql()}) as R"))
            ->mergeBindings($uniqueReturnsMileniaBranch)
            ->join('SOIVH_Cabang as H', 'R.Ref_InvoiceID', '=', 'H.SOIVH_InvoiceID')
            ->leftJoin(DB::raw("({$invoiceSalesmanMileniaBranch->toSql()}) as Map"), 'R.Ref_InvoiceID', '=', 'Map.SOIVD_InvoiceID')
            ->mergeBindings($invoiceSalesmanMileniaBranch)
            ->select(
            'Map.SalesmanID',
            DB::raw('SUM(R.NetReturnAmount) as TotalReturnAmount')
            )
            // [FILTER TANGGAL RETUR MENGIKUTI INVOICE]
            ->whereBetween('H.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('Map.SalesmanID');

        // 4. CTE_SalesData: QUERY SALES UTAMA
        // Menghitung penjualan kotor (Gross Sales)
        $salesDataMileniaBranch = DB::connection('sqlsrv_wh')
            ->table('SOIVD_Cabang')
            ->join('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->select(
                'SOIVD_Cabang.SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD_Cabang.SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD_Cabang.SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVH_Cabang.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD_Cabang.SOIVD_SalesmanID');

        // 5. FINAL SELECT (GABUNGAN)
        $salesManSalesMileniaBranch = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$salesDataMileniaBranch->toSql()}) as S"))
            ->mergeBindings($salesDataMileniaBranch)
            ->leftJoin(DB::raw("({$returnPerSalesmanMileniaBranch->toSql()}) as R"), 'S.SOIVD_SalesmanID', '=', 'R.SalesmanID')
            ->mergeBindings($returnPerSalesmanMileniaBranch)
            ->leftJoin('MFSSM as M', 'S.SOIVD_SalesmanID', '=', 'M.MFSSM_SalesmanID')
            ->selectRaw("
                S.SOIVD_SalesmanID,
                ISNULL(M.MFSSM_Description, 'Unknown Salesman') AS salesman_name,
                S.total_transaksi,
                S.total_qty,
                S.total_amount,
                ISNULL(R.TotalReturnAmount, 0) AS total_return_amount,
                (ISNULL(S.total_amount, 0) - ISNULL(R.TotalReturnAmount, 0)) AS net_amount
            ")
            ->orderByDesc('S.total_amount')
            ->get();

        // --- MAP ---
        // 1. CTE_Invoice_Salesman: MAPPING INVOICE -> SALESMAN
        // Mencari siapa salesman dominan untuk setiap invoice (mencegah duplikasi)
        $invoiceSalesmanMap = DB::connection('sqlsrv_snx')
            ->table('SOIVD')
            ->select('SOIVD_InvoiceID', DB::raw('MAX(SOIVD_SalesmanID) as SalesmanID'))
            ->groupBy('SOIVD_InvoiceID');

        // 2. CTE_Fix_Unique_Returns: ISOLASI RETURN
        $uniqueReturnsMap = DB::connection('sqlsrv_snx')
            ->table('SOORH')
            ->join('SOORD', 'SOORH.SOORH_ReturnID', '=', 'SOORD.SOORD_ReturnID')
            ->select(
            'SOORH.SOORH_ReturnID',
            DB::raw('MAX(SOORD.SOORD_InvoiceID) as Ref_InvoiceID'),
            DB::raw('MAX(SOORH.SOORH_ReturnAmount - ISNULL(SOORH.SOORH_TaxAmount, 0)) AS NetReturnAmount')
            )
            ->groupBy('SOORH.SOORH_ReturnID');

        // 3. CTE_Filtered_Returns: FILTER TANGGAL & TOTAL PER SALESMAN
        // Menggabungkan Data Retur Unik + Tanggal Invoice + Mapping Salesman
        $returnPerSalesmanMap = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$uniqueReturnsMap->toSql()}) as R"))
            ->mergeBindings($uniqueReturnsMap)
            ->join('SOIVH as H', 'R.Ref_InvoiceID', '=', 'H.SOIVH_InvoiceID')
            ->leftJoin(DB::raw("({$invoiceSalesmanMap->toSql()}) as Map"), 'R.Ref_InvoiceID', '=', 'Map.SOIVD_InvoiceID')
            ->mergeBindings($invoiceSalesmanMap)
            ->select(
            'Map.SalesmanID',
            DB::raw('SUM(R.NetReturnAmount) as TotalReturnAmount')
            )
            ->whereBetween('H.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('Map.SalesmanID');

        // 4. CTE_SalesData: QUERY SALES UTAMA
        $salesDataMap = DB::connection('sqlsrv_snx')
            ->table('SOIVD')
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->select(
                'SOIVD.SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD.SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD.SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVH.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD.SOIVD_SalesmanID');

        // 5. FINAL SELECT (GABUNGAN)
        $salesManSalesMap = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$salesDataMap->toSql()}) as S"))
            ->mergeBindings($salesDataMap)
            ->leftJoin(DB::raw("({$returnPerSalesmanMap->toSql()}) as R"), 'S.SOIVD_SalesmanID', '=', 'R.SalesmanID')
            ->mergeBindings($returnPerSalesmanMap)
            ->leftJoin('MFSSM as M', 'S.SOIVD_SalesmanID', '=', 'M.MFSSM_SalesmanID')
            ->selectRaw("
                S.SOIVD_SalesmanID,
                ISNULL(M.MFSSM_Description, 'Unknown Salesman') AS salesman_name,
                S.total_transaksi,
                S.total_qty,
                S.total_amount,
                ISNULL(R.TotalReturnAmount, 0) AS total_return_amount,
                (ISNULL(S.total_amount, 0) - ISNULL(R.TotalReturnAmount, 0)) AS net_amount
            ")
            ->orderByDesc('S.total_amount')
            ->get();

        // --- MAP BRANCH ---
        // 1. CTE_Invoice_Salesman: MAPPING INVOICE -> SALESMAN
        $invoiceSalesmanMapBranch = DB::connection('sqlsrv_snx')
            ->table('SOIVD_Cabang')
            ->select('SOIVD_InvoiceID', DB::raw('MAX(SOIVD_SalesmanID) as SalesmanID'))
            ->groupBy('SOIVD_InvoiceID');

        // 2. CTE_Fix_Unique_Returns: ISOLASI RETURN
        // Memastikan nominal retur hanya diambil 1x per Nomor Retur (Header)
        // Walaupun barangnya banyak, nominal tidak dikali jumlah barang
        $uniqueReturnsMapBranch = DB::connection('sqlsrv_snx')
            ->table('SOORH_Cabang')
            ->join('SOORD_Cabang', 'SOORH_Cabang.SOORH_ReturnID', '=', 'SOORD_Cabang.SOORD_ReturnID')
            ->select(
            'SOORH_Cabang.SOORH_ReturnID',
            DB::raw('MAX(SOORD_Cabang.SOORD_InvoiceID) as Ref_InvoiceID'),
            DB::raw('MAX(SOORH_Cabang.SOORH_ReturnAmount - ISNULL(SOORH_Cabang.SOORH_TaxAmount, 0)) AS NetReturnAmount')
            )
            ->groupBy('SOORH_Cabang.SOORH_ReturnID');

        // 3. CTE_Filtered_Returns: FILTER TANGGAL & TOTAL PER SALESMAN
        $returnPerSalesmanMapBranch = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$uniqueReturnsMapBranch->toSql()}) as R"))
            ->mergeBindings($uniqueReturnsMapBranch)
            ->join('SOIVH_Cabang as H', 'R.Ref_InvoiceID', '=', 'H.SOIVH_InvoiceID')
            ->leftJoin(DB::raw("({$invoiceSalesmanMapBranch->toSql()}) as Map"), 'R.Ref_InvoiceID', '=', 'Map.SOIVD_InvoiceID')
            ->mergeBindings($invoiceSalesmanMapBranch)
            ->select(
            'Map.SalesmanID',
            DB::raw('SUM(R.NetReturnAmount) as TotalReturnAmount')
            )
            // [FILTER TANGGAL RETUR MENGIKUTI INVOICE]
            ->whereBetween('H.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('Map.SalesmanID');

        // 4. CTE_SalesData: QUERY SALES UTAMA
        // Menghitung penjualan kotor (Gross Sales)
        $salesDataMapBranch = DB::connection('sqlsrv_snx')
            ->table('SOIVD_Cabang')
            ->join('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->select(
                'SOIVD_Cabang.SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD_Cabang.SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD_Cabang.SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVH_Cabang.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD_Cabang.SOIVD_SalesmanID');

        // 5. FINAL SELECT (GABUNGAN)
        $salesManSalesMapBranch = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$salesDataMapBranch->toSql()}) as S"))
            ->mergeBindings($salesDataMapBranch)
            ->leftJoin(DB::raw("({$returnPerSalesmanMapBranch->toSql()}) as R"), 'S.SOIVD_SalesmanID', '=', 'R.SalesmanID')
            ->mergeBindings($returnPerSalesmanMapBranch)
            ->leftJoin('MFSSM as M', 'S.SOIVD_SalesmanID', '=', 'M.MFSSM_SalesmanID')
            ->selectRaw("
                S.SOIVD_SalesmanID,
                ISNULL(M.MFSSM_Description, 'Unknown Salesman') AS salesman_name,
                S.total_transaksi,
                S.total_qty,
                S.total_amount,
                ISNULL(R.TotalReturnAmount, 0) AS total_return_amount,
                (ISNULL(S.total_amount, 0) - ISNULL(R.TotalReturnAmount, 0)) AS net_amount
            ")
            ->orderByDesc('S.total_amount')
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

    // Export Sales
    public function exportSalesmanSalesMilenia(Request $request)
    {
        // 1. Tentukan Tanggal Filter
        $startDateTime = $request->input('start_date', date('Y-m-01 00:00:00'));
        $endDateTime   = $request->input('end_date', date('Y-m-d 23:59:59'));
        $companyType = 'PT Milenia Mega Mandiri (PUSAT)';

        // 2. Jalankan Query (Sesuai kode Anda)
        // 1. CTE_Invoice_Salesman: MAPPING INVOICE -> SALESMAN
        // Mencari siapa salesman dominan untuk setiap invoice (mencegah duplikasi)
        $invoiceSalesmanMilenia = DB::connection('sqlsrv_wh')
            ->table('SOIVD')
            ->select('SOIVD_InvoiceID', DB::raw('MAX(SOIVD_SalesmanID) as SalesmanID'))
            ->groupBy('SOIVD_InvoiceID');

        // 2. CTE_Fix_Unique_Returns: ISOLASI RETURN
        $uniqueReturnsMilenia = DB::connection('sqlsrv_wh')
            ->table('SOORH')
            ->join('SOORD', 'SOORH.SOORH_ReturnID', '=', 'SOORD.SOORD_ReturnID')
            ->select(
                'SOORH.SOORH_ReturnID',
                DB::raw('MAX(SOORD.SOORD_InvoiceID) as Ref_InvoiceID'),
                DB::raw('MAX(SOORH.SOORH_ReturnAmount - ISNULL(SOORH.SOORH_TaxAmount, 0)) AS NetReturnAmount')
            )
            ->groupBy('SOORH.SOORH_ReturnID');

        // 3. CTE_Filtered_Returns: FILTER TANGGAL & TOTAL PER SALESMAN
        // Menggabungkan Data Retur Unik + Tanggal Invoice + Mapping Salesman
        $returnPerSalesmanMilenia = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$uniqueReturnsMilenia->toSql()}) as R"))
            ->mergeBindings($uniqueReturnsMilenia)
            ->join('SOIVH as H', 'R.Ref_InvoiceID', '=', 'H.SOIVH_InvoiceID')
            ->leftJoin(DB::raw("({$invoiceSalesmanMilenia->toSql()}) as Map"), 'R.Ref_InvoiceID', '=', 'Map.SOIVD_InvoiceID')
            ->mergeBindings($invoiceSalesmanMilenia)
            ->select(
                'Map.SalesmanID',
                DB::raw('SUM(R.NetReturnAmount) as TotalReturnAmount')
            )
            ->whereBetween('H.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('Map.SalesmanID');

        // 4. CTE_SalesData: QUERY SALES UTAMA
        // 1. CTE_Invoice_Salesman: MAPPING INVOICE -> SALESMAN
        // Mencari siapa salesman dominan untuk setiap invoice (mencegah duplikasi)
        $invoiceSalesmanMilenia = DB::connection('sqlsrv_wh')
            ->table('SOIVD')
            ->select('SOIVD_InvoiceID', DB::raw('MAX(SOIVD_SalesmanID) as SalesmanID'))
            ->groupBy('SOIVD_InvoiceID');

        // 2. CTE_Fix_Unique_Returns: ISOLASI RETURN
        $uniqueReturnsMilenia = DB::connection('sqlsrv_wh')
            ->table('SOORH')
            ->join('SOORD', 'SOORH.SOORH_ReturnID', '=', 'SOORD.SOORD_ReturnID')
            ->select(
                'SOORH.SOORH_ReturnID',
                DB::raw('MAX(SOORD.SOORD_InvoiceID) as Ref_InvoiceID'),
                DB::raw('MAX(SOORH.SOORH_ReturnAmount - ISNULL(SOORH.SOORH_TaxAmount, 0)) AS NetReturnAmount')
            )
            ->groupBy('SOORH.SOORH_ReturnID');

        // 3. CTE_Filtered_Returns: FILTER TANGGAL & TOTAL PER SALESMAN
        // Menggabungkan Data Retur Unik + Tanggal Invoice + Mapping Salesman
        $returnPerSalesmanMilenia = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$uniqueReturnsMilenia->toSql()}) as R"))
            ->mergeBindings($uniqueReturnsMilenia)
            ->join('SOIVH as H', 'R.Ref_InvoiceID', '=', 'H.SOIVH_InvoiceID')
            ->leftJoin(DB::raw("({$invoiceSalesmanMilenia->toSql()}) as Map"), 'R.Ref_InvoiceID', '=', 'Map.SOIVD_InvoiceID')
            ->mergeBindings($invoiceSalesmanMilenia)
            ->select(
                'Map.SalesmanID',
                DB::raw('SUM(R.NetReturnAmount) as TotalReturnAmount')
            )
            ->whereBetween('H.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('Map.SalesmanID');

        // 4. CTE_SalesData: QUERY SALES UTAMA
        $salesDataMilenia = DB::connection('sqlsrv_wh')
            ->table('SOIVD')
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->select(
                'SOIVD.SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD.SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD.SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVH.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD.SOIVD_SalesmanID');

        // 5. FINAL SELECT (GABUNGAN)
        $data = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$salesDataMilenia->toSql()}) as S"))
            ->mergeBindings($salesDataMilenia)
            ->leftJoin(DB::raw("({$returnPerSalesmanMilenia->toSql()}) as R"), 'S.SOIVD_SalesmanID', '=', 'R.SalesmanID')
            ->mergeBindings($returnPerSalesmanMilenia)
            ->leftJoin('MFSSM as M', 'S.SOIVD_SalesmanID', '=', 'M.MFSSM_SalesmanID')
            ->selectRaw("
                S.SOIVD_SalesmanID,
                ISNULL(M.MFSSM_Description, 'Unknown Salesman') AS salesman_name,
                S.total_transaksi,
                S.total_qty,
                S.total_amount,
                ISNULL(R.TotalReturnAmount, 0) AS total_return_amount,
                (ISNULL(S.total_amount, 0) - ISNULL(R.TotalReturnAmount, 0)) AS net_amount
            ")
            ->orderByDesc('S.total_amount')
            ->get();

        // ubah format tanggal menjadi teks translated format
        $startDateTimeText = Carbon::parse($startDateTime)->translatedFormat('d F Y');
        $endDateTimeText   = Carbon::parse($endDateTime)->translatedFormat('d F Y');

        // 4. Nama File saat didownload
        $filename = 'Laporan_Penjualan_Sales_Milenia_Pusat_' . $startDateTimeText . '_to_' . $endDateTimeText . '.xls';

        // 5. Return View dengan Header Excel
        return response()->view('exports.sales_man_sales', [
            'salesData' => $data,
            'startDate' => $startDateTime,
            'endDate'   => $endDateTime,
            'companyType' => $companyType
        ])
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function exportSalesmanSalesMileniaBranch(Request $request)
    {
        // 1. Tentukan Tanggal Filter
        $startDateTime = $request->input('start_date', date('Y-m-01 00:00:00'));
        $endDateTime   = $request->input('end_date', date('Y-m-d 23:59:59'));
        $companyType = 'PT Milenia Mega Mandiri (CABANG)';

        // 2. Jalankan Query (Milenia Branch / Cabang)
        // 1. CTE_Invoice_Salesman: MAPPING INVOICE -> SALESMAN
        $invoiceSalesmanMileniaBranch = DB::connection('sqlsrv_wh')
            ->table('SOIVD_Cabang')
            ->select('SOIVD_InvoiceID', DB::raw('MAX(SOIVD_SalesmanID) as SalesmanID'))
            ->groupBy('SOIVD_InvoiceID');

        // 2. CTE_Fix_Unique_Returns: ISOLASI RETURN
        // Memastikan nominal retur hanya diambil 1x per Nomor Retur (Header)
        // Walaupun barangnya banyak, nominal tidak dikali jumlah barang
        $uniqueReturnsMileniaBranch = DB::connection('sqlsrv_wh')
            ->table('SOORH_Cabang')
            ->join('SOORD_Cabang', 'SOORH_Cabang.SOORH_ReturnID', '=', 'SOORD_Cabang.SOORD_ReturnID')
            ->select(
                'SOORH_Cabang.SOORH_ReturnID',
                DB::raw('MAX(SOORD_Cabang.SOORD_InvoiceID) as Ref_InvoiceID'),
                DB::raw('MAX(SOORH_Cabang.SOORH_ReturnAmount - ISNULL(SOORH_Cabang.SOORH_TaxAmount, 0)) AS NetReturnAmount')
            )
            ->groupBy('SOORH_Cabang.SOORH_ReturnID');

        // 3. CTE_Filtered_Returns: FILTER TANGGAL & TOTAL PER SALESMAN
        $returnPerSalesmanMileniaBranch = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$uniqueReturnsMileniaBranch->toSql()}) as R"))
            ->mergeBindings($uniqueReturnsMileniaBranch)
            ->join('SOIVH_Cabang as H', 'R.Ref_InvoiceID', '=', 'H.SOIVH_InvoiceID')
            ->leftJoin(DB::raw("({$invoiceSalesmanMileniaBranch->toSql()}) as Map"), 'R.Ref_InvoiceID', '=', 'Map.SOIVD_InvoiceID')
            ->mergeBindings($invoiceSalesmanMileniaBranch)
            ->select(
                'Map.SalesmanID',
                DB::raw('SUM(R.NetReturnAmount) as TotalReturnAmount')
            )
            // [FILTER TANGGAL RETUR MENGIKUTI INVOICE]
            ->whereBetween('H.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('Map.SalesmanID');

        // 4. CTE_SalesData: QUERY SALES UTAMA
        // Menghitung penjualan kotor (Gross Sales)
        $salesDataMileniaBranch = DB::connection('sqlsrv_wh')
            ->table('SOIVD_Cabang')
            ->join('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->select(
                'SOIVD_Cabang.SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD_Cabang.SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD_Cabang.SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVH_Cabang.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD_Cabang.SOIVD_SalesmanID');

        // 5. FINAL SELECT (GABUNGAN)
        $data = DB::connection('sqlsrv_wh')
            ->table(DB::raw("({$salesDataMileniaBranch->toSql()}) as S"))
            ->mergeBindings($salesDataMileniaBranch)
            ->leftJoin(DB::raw("({$returnPerSalesmanMileniaBranch->toSql()}) as R"), 'S.SOIVD_SalesmanID', '=', 'R.SalesmanID')
            ->mergeBindings($returnPerSalesmanMileniaBranch)
            ->leftJoin('MFSSM as M', 'S.SOIVD_SalesmanID', '=', 'M.MFSSM_SalesmanID')
            ->selectRaw("
                S.SOIVD_SalesmanID,
                ISNULL(M.MFSSM_Description, 'Unknown Salesman') AS salesman_name,
                S.total_transaksi,
                S.total_qty,
                S.total_amount,
                ISNULL(R.TotalReturnAmount, 0) AS total_return_amount,
                (ISNULL(S.total_amount, 0) - ISNULL(R.TotalReturnAmount, 0)) AS net_amount
            ")
            ->orderByDesc('S.total_amount')
            ->get();

        // 3. Ubah format tanggal untuk nama file
        $startDateTimeText = \Carbon\Carbon::parse($startDateTime)->translatedFormat('d F Y');
        $endDateTimeText   = \Carbon\Carbon::parse($endDateTime)->translatedFormat('d F Y');

        // 4. Nama File saat didownload (Ganti 'Pusat' jadi 'Cabang')
        $filename = 'Laporan_Penjualan_Sales_Milenia_Cabang_' . $startDateTimeText . '_to_' . $endDateTimeText . '.xls';

        // 5. Return View (Gunakan view yang sama karena struktur datanya sama)
        return response()->view('exports.sales_man_sales', [
            'salesData' => $data,
            'startDate' => $startDateTime,
            'endDate'   => $endDateTime,
            'companyType' => $companyType
        ])
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function exportSalesmanSalesMap(Request $request)
    {
        // 1. Tentukan Tanggal Filter
        $startDateTime = $request->input('start_date', date('Y-m-01 00:00:00'));
        $endDateTime   = $request->input('end_date', date('Y-m-d 23:59:59'));
        $companyType = 'PT Mega Auto Prima (PUSAT)';

        // 2. Jalankan Query (Sesuai kode Anda)
        // 1. CTE_Invoice_Salesman: MAPPING INVOICE -> SALESMAN
        // Mencari siapa salesman dominan untuk setiap invoice (mencegah duplikasi)
        $invoiceSalesmanMap = DB::connection('sqlsrv_snx')
            ->table('SOIVD')
            ->select('SOIVD_InvoiceID', DB::raw('MAX(SOIVD_SalesmanID) as SalesmanID'))
            ->groupBy('SOIVD_InvoiceID');

        // 2. CTE_Fix_Unique_Returns: ISOLASI RETURN
        $uniqueReturnsMap = DB::connection('sqlsrv_snx')
            ->table('SOORH')
            ->join('SOORD', 'SOORH.SOORH_ReturnID', '=', 'SOORD.SOORD_ReturnID')
            ->select(
                'SOORH.SOORH_ReturnID',
                DB::raw('MAX(SOORD.SOORD_InvoiceID) as Ref_InvoiceID'),
                DB::raw('MAX(SOORH.SOORH_ReturnAmount - ISNULL(SOORH.SOORH_TaxAmount, 0)) AS NetReturnAmount')
            )
            ->groupBy('SOORH.SOORH_ReturnID');

        // 3. CTE_Filtered_Returns: FILTER TANGGAL & TOTAL PER SALESMAN
        // Menggabungkan Data Retur Unik + Tanggal Invoice + Mapping Salesman
        $returnPerSalesmanMap = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$uniqueReturnsMap->toSql()}) as R"))
            ->mergeBindings($uniqueReturnsMap)
            ->join('SOIVH as H', 'R.Ref_InvoiceID', '=', 'H.SOIVH_InvoiceID')
            ->leftJoin(DB::raw("({$invoiceSalesmanMap->toSql()}) as Map"), 'R.Ref_InvoiceID', '=', 'Map.SOIVD_InvoiceID')
            ->mergeBindings($invoiceSalesmanMap)
            ->select(
                'Map.SalesmanID',
                DB::raw('SUM(R.NetReturnAmount) as TotalReturnAmount')
            )
            ->whereBetween('H.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('Map.SalesmanID');

        // 4. CTE_SalesData: QUERY SALES UTAMA
        $salesDataMap = DB::connection('sqlsrv_snx')
            ->table('SOIVD')
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->select(
                'SOIVD.SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD.SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD.SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVH.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD.SOIVD_SalesmanID');

        // 5. FINAL SELECT (GABUNGAN)
        $data = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$salesDataMap->toSql()}) as S"))
            ->mergeBindings($salesDataMap)
            ->leftJoin(DB::raw("({$returnPerSalesmanMap->toSql()}) as R"), 'S.SOIVD_SalesmanID', '=', 'R.SalesmanID')
            ->mergeBindings($returnPerSalesmanMap)
            ->leftJoin('MFSSM as M', 'S.SOIVD_SalesmanID', '=', 'M.MFSSM_SalesmanID')
            ->selectRaw("
                S.SOIVD_SalesmanID,
                ISNULL(M.MFSSM_Description, 'Unknown Salesman') AS salesman_name,
                S.total_transaksi,
                S.total_qty,
                S.total_amount,
                ISNULL(R.TotalReturnAmount, 0) AS total_return_amount,
                (ISNULL(S.total_amount, 0) - ISNULL(R.TotalReturnAmount, 0)) AS net_amount
            ")
            ->orderByDesc('S.total_amount')
            ->get();

        // ubah format tanggal menjadi teks translated format
        $startDateTimeText = Carbon::parse($startDateTime)->translatedFormat('d F Y');
        $endDateTimeText   = Carbon::parse($endDateTime)->translatedFormat('d F Y');

        // 4. Nama File saat didownload
        $filename = 'Laporan_Penjualan_Sales_Map_Pusat_' . $startDateTimeText . '_to_' . $endDateTimeText . '.xls';

        // 5. Return View dengan Header Excel
        return response()->view('exports.sales_man_sales', [
            'salesData' => $data,
            'startDate' => $startDateTime,
            'endDate'   => $endDateTime,
            'companyType' => $companyType
        ])
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function exportSalesmanSalesMapBranch(Request $request)
    {
        // 1. Tentukan Tanggal Filter
        $startDateTime = $request->input('start_date', date('Y-m-01 00:00:00'));
        $endDateTime   = $request->input('end_date', date('Y-m-d 23:59:59'));
        $companyType = 'PT Mega Auto Prima (CABANG)';

        // 2. Jalankan Query (Milenia Branch / Cabang)
        // 1. CTE_Invoice_Salesman: MAPPING INVOICE -> SALESMAN
        $invoiceSalesmanMapBranch = DB::connection('sqlsrv_snx')
            ->table('SOIVD_Cabang')
            ->select('SOIVD_InvoiceID', DB::raw('MAX(SOIVD_SalesmanID) as SalesmanID'))
            ->groupBy('SOIVD_InvoiceID');

        // 2. CTE_Fix_Unique_Returns: ISOLASI RETURN
        // Memastikan nominal retur hanya diambil 1x per Nomor Retur (Header)
        // Walaupun barangnya banyak, nominal tidak dikali jumlah barang
        $uniqueReturnsMapBranch = DB::connection('sqlsrv_snx')
            ->table('SOORH_Cabang')
            ->join('SOORD_Cabang', 'SOORH_Cabang.SOORH_ReturnID', '=', 'SOORD_Cabang.SOORD_ReturnID')
            ->select(
                'SOORH_Cabang.SOORH_ReturnID',
                DB::raw('MAX(SOORD_Cabang.SOORD_InvoiceID) as Ref_InvoiceID'),
                DB::raw('MAX(SOORH_Cabang.SOORH_ReturnAmount - ISNULL(SOORH_Cabang.SOORH_TaxAmount, 0)) AS NetReturnAmount')
            )
            ->groupBy('SOORH_Cabang.SOORH_ReturnID');

        // 3. CTE_Filtered_Returns: FILTER TANGGAL & TOTAL PER SALESMAN
        $returnPerSalesmanMapBranch = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$uniqueReturnsMapBranch->toSql()}) as R"))
            ->mergeBindings($uniqueReturnsMapBranch)
            ->join('SOIVH_Cabang as H', 'R.Ref_InvoiceID', '=', 'H.SOIVH_InvoiceID')
            ->leftJoin(DB::raw("({$invoiceSalesmanMapBranch->toSql()}) as Map"), 'R.Ref_InvoiceID', '=', 'Map.SOIVD_InvoiceID')
            ->mergeBindings($invoiceSalesmanMapBranch)
            ->select(
                'Map.SalesmanID',
                DB::raw('SUM(R.NetReturnAmount) as TotalReturnAmount')
            )
            // [FILTER TANGGAL RETUR MENGIKUTI INVOICE]
            ->whereBetween('H.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('Map.SalesmanID');

        // 4. CTE_SalesData: QUERY SALES UTAMA
        // Menghitung penjualan kotor (Gross Sales)
        $salesDataMapBranch = DB::connection('sqlsrv_snx')
            ->table('SOIVD_Cabang')
            ->join('SOIVH_Cabang', 'SOIVD_Cabang.SOIVD_InvoiceID', '=', 'SOIVH_Cabang.SOIVH_InvoiceID')
            ->select(
                'SOIVD_Cabang.SOIVD_SalesmanID',
                DB::raw('COUNT(DISTINCT SOIVD_Cabang.SOIVD_InvoiceID) AS total_transaksi'),
                DB::raw('SUM(SOIVD_Cabang.SOIVD_OrderQty) AS total_qty'),
                DB::raw('SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) AS total_amount')
            )
            ->whereBetween('SOIVH_Cabang.SOIVH_InvoiceDate', [$startDateTime, $endDateTime])
            ->groupBy('SOIVD_Cabang.SOIVD_SalesmanID');

        // 5. FINAL SELECT (GABUNGAN)
        $data = DB::connection('sqlsrv_snx')
            ->table(DB::raw("({$salesDataMapBranch->toSql()}) as S"))
            ->mergeBindings($salesDataMapBranch)
            ->leftJoin(DB::raw("({$returnPerSalesmanMapBranch->toSql()}) as R"), 'S.SOIVD_SalesmanID', '=', 'R.SalesmanID')
            ->mergeBindings($returnPerSalesmanMapBranch)
            ->leftJoin('MFSSM as M', 'S.SOIVD_SalesmanID', '=', 'M.MFSSM_SalesmanID')
            ->selectRaw("
                S.SOIVD_SalesmanID,
                ISNULL(M.MFSSM_Description, 'Unknown Salesman') AS salesman_name,
                S.total_transaksi,
                S.total_qty,
                S.total_amount,
                ISNULL(R.TotalReturnAmount, 0) AS total_return_amount,
                (ISNULL(S.total_amount, 0) - ISNULL(R.TotalReturnAmount, 0)) AS net_amount
            ")
            ->orderByDesc('S.total_amount')
            ->get();

        // 3. Ubah format tanggal untuk nama file
        $startDateTimeText = \Carbon\Carbon::parse($startDateTime)->translatedFormat('d F Y');
        $endDateTimeText   = \Carbon\Carbon::parse($endDateTime)->translatedFormat('d F Y');

        // 4. Nama File saat didownload (Ganti 'Pusat' jadi 'Cabang')
        $filename = 'Laporan_Penjualan_Sales_Map_Cabang_' . $startDateTimeText . '_to_' . $endDateTimeText . '.xls';

        // 5. Return View (Gunakan view yang sama karena struktur datanya sama)
        return response()->view('exports.sales_man_sales', [
            'salesData' => $data,
            'startDate' => $startDateTime,
            'endDate'   => $endDateTime,
            'companyType' => $companyType
        ])
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    // Export Brand
    // 1. Export Milenia PUSAT
    public function exportBrandMilenia(Request $request)
    {
        $startDateTime = $request->input('start_date', date('Y-m-01 00:00:00'));
        $endDateTime   = $request->input('end_date', date('Y-m-d 23:59:59'));
        $companyType   = 'PT Milenia Mega Mandiri (PUSAT)';

        $data = SalesOrderDetailMilenia::getBrandPerformanceDashboard($startDateTime, $endDateTime);

        return $this->generateBrandExcelResponse($data, $startDateTime, $endDateTime, 'Milenia_Pusat', $companyType);
    }

    // 2. Export MAP PUSAT
    public function exportBrandMap(Request $request)
    {
        $startDateTime = $request->input('start_date', date('Y-m-01 00:00:00'));
        $endDateTime   = $request->input('end_date', date('Y-m-d 23:59:59'));
        $companyType   = 'PT Map Mega Mandiri (PUSAT)';

        $data = SalesOrderDetailMap::getBrandPerformanceDashboard($startDateTime, $endDateTime);

        return $this->generateBrandExcelResponse($data, $startDateTime, $endDateTime, 'MAP_Pusat', $companyType);
    }

    // 3. Export Milenia CABANG
    public function exportBrandMileniaBranch(Request $request)
    {
        $startDateTime = $request->input('start_date', date('Y-m-01 00:00:00'));
        $endDateTime   = $request->input('end_date', date('Y-m-d 23:59:59'));
        $companyType   = 'PT Milenia Mega Mandiri (CABANG)';

        $data = SalesOrderDetailMileniaBranch::getBrandPerformanceDashboard($startDateTime, $endDateTime);

        return $this->generateBrandExcelResponse($data, $startDateTime, $endDateTime, 'Milenia_Cabang', $companyType);
    }

    // 4. Export MAP CABANG
    public function exportBrandMapBranch(Request $request)
    {
        $startDateTime = $request->input('start_date', date('Y-m-01 00:00:00'));
        $endDateTime   = $request->input('end_date', date('Y-m-d 23:59:59'));
        $companyType   = 'PT Map Mega Mandiri (CABANG)';

        $data = SalesOrderDetailMapBranch::getBrandPerformanceDashboard($startDateTime, $endDateTime);

        return $this->generateBrandExcelResponse($data, $startDateTime, $endDateTime, 'MAP_Cabang', $companyType);
    }

    // Helper Generate Brand Excel Export
    private function generateBrandExcelResponse($data, $startDateTime, $endDateTime, $fileSuffix, $companyType)
    {
        $startDateTimeText = Carbon::parse($startDateTime)->translatedFormat('d F Y');
        $endDateTimeText   = Carbon::parse($endDateTime)->translatedFormat('d F Y');

        $filename = 'Laporan_Brand_Performance_' . $fileSuffix . '_' . $startDateTimeText . '_to_' . $endDateTimeText . '.xls';

        return response()->view('exports.brand_performance', [
            'brandData'   => $data,
            'startDate'   => $startDateTime,
            'endDate'     => $endDateTime,
            'companyName' => $companyType
        ])
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    // Export Customer
    // 1. Export Customer Milenia
    public function exportCustomerMilenia(Request $request)
    {
        $startDateTime = $request->input('start_date', date('Y-m-01 00:00:00'));
        $endDateTime   = $request->input('end_date', date('Y-m-d 23:59:59'));
        $companyType   = 'PT Milenia Mega Mandiri';

        $data = SalesOrderDetailMilenia::getCustomerPerformanceDashboard($startDateTime, $endDateTime);

        return $this->generateCustomerExcelResponse($data, $startDateTime, $endDateTime, 'Milenia', $companyType);
    }

    // 2. Export Customer MAP
    public function exportCustomerMap(Request $request)
    {
        $startDateTime = $request->input('start_date', date('Y-m-01 00:00:00'));
        $endDateTime   = $request->input('end_date', date('Y-m-d 23:59:59'));
        $companyType   = 'PT Map Mega Mandiri';

        $data = SalesOrderDetailMap::getCustomerPerformanceDashboard($startDateTime, $endDateTime);

        return $this->generateCustomerExcelResponse($data, $startDateTime, $endDateTime, 'MAP', $companyType);
    }

    // --- HELPER PRIVATE FUNCTION (Agar kodingan rapi) ---
    private function generateCustomerExcelResponse($data, $startDateTime, $endDateTime, $fileSuffix, $companyType)
    {
        $startDateTimeText = \Carbon\Carbon::parse($startDateTime)->translatedFormat('d F Y');
        $endDateTimeText   = \Carbon\Carbon::parse($endDateTime)->translatedFormat('d F Y');

        $filename = 'Laporan_Customer_' . $fileSuffix . '_' . $startDateTimeText . '_to_' . $endDateTimeText . '.xls';

        return response()->view('exports.customer_performance', [
            'customerData' => $data,
            'startDate'    => $startDateTime,
            'endDate'      => $endDateTime,
            'companyType'  => $companyType
        ])
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
