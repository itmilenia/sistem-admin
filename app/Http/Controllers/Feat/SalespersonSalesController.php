<?php

namespace App\Http\Controllers\Feat;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SalespersonSalesController extends Controller
{
    public function landing()
    {
        return view('pages.feat.sales.salesperson-sales.landing');
    }

    public function indexMilenia()
    {
        return view('pages.feat.sales.salesperson-sales.index-milenia');
    }

    public function indexMileniaBranch()
    {
        return view('pages.feat.sales.salesperson-sales.index-milenia-branch');
    }

    public function indexMap()
    {
        return view('pages.feat.sales.salesperson-sales.index-map');
    }

    public function indexMapBranch()
    {
        return view('pages.feat.sales.salesperson-sales.index-map-branch');
    }

    public function getDataMilenia(Request $request)
    {
        try {
            $cursor = $request->input('cursor');
            $perPage = 5;

            // Step 1: ambil semua invoice unik + salesman_id + invoice_amount
            $invoiceSub = DB::connection('sqlsrv_wh')->table('SOIVD')
                ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
                ->select(
                    'SOIVD.SOIVD_SalesmanID',
                    'SOIVH.SOIVH_InvoiceID',
                    DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) AS invoice_total')
                )
                ->groupBy('SOIVD.SOIVD_SalesmanID', 'SOIVH.SOIVH_InvoiceID');


            // Step 2: gabung dengan master salesman dan total per salesman
            $query = DB::connection('sqlsrv_wh')->table(DB::raw("({$invoiceSub->toSql()}) as invoices"))
                ->mergeBindings($invoiceSub)
                ->join('MFSSM', 'invoices.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
                ->where('MFSSM.MFSSM_Active', 1)
                ->select(
                    'MFSSM.MFSSM_SalesmanID',
                    'MFSSM.MFSSM_Description as salesman_name',
                    DB::raw('SUM(invoices.invoice_total) as total_sales'),
                    DB::raw('COUNT(invoices.SOIVH_InvoiceID) as total_invoices')
                )
                ->groupBy('MFSSM.MFSSM_SalesmanID', 'MFSSM.MFSSM_Description');

            if ($request->filled('search_salesperson')) {
                $query->where('MFSSM.MFSSM_Description', 'like', '%' . $request->search_salesperson . '%');
            }

            $salesPerformance = $query
                ->orderBy('MFSSM_SalesmanID', 'asc')
                ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

            $formattedData = $salesPerformance->getCollection()->map(function ($item) {
                return [
                    'salesman_name' => $item->salesman_name,
                    'total_invoices' => number_format($item->total_invoices, 0, ',', '.') . ' Invoices',
                    'total_sales' => 'Rp ' . number_format($item->total_sales, 0, ',', '.'),
                    'action' => '<a href="' . route('salesperson-sales.transactions.milenia.show', $item->MFSSM_SalesmanID) . '"
                                class="btn btn-sm btn-info"><i class="feather-eye me-1"></i> Lihat Detail</a>'
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'next_cursor' => $salesPerformance->nextCursor()?->encode(),
                'prev_cursor' => $salesPerformance->previousCursor()?->encode(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data penjualan sales: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Terjadi kesalahan pada server saat mengambil data.'], 500);
        }
    }

    public function getDataMileniaBranch(Request $request)
    {
        try {
            $cursor = $request->input('cursor');
            $perPage = 5;

            // Step 1: ambil semua invoice unik + salesman_id + invoice_amount
            $invoiceSub = DB::connection('sqlsrv_wh')->table('SOIVD_CABANG')
                ->join('SOIVH_CABANG', 'SOIVD_CABANG.SOIVD_InvoiceID', '=', 'SOIVH_CABANG.SOIVH_InvoiceID')
                ->select(
                    'SOIVD_CABANG.SOIVD_SalesmanID',
                    'SOIVH_CABANG.SOIVH_InvoiceID',
                    DB::raw('SUM(SOIVD_CABANG.SOIVD_LineInvoiceAmount) AS invoice_total')
                )
                ->groupBy('SOIVD_CABANG.SOIVD_SalesmanID', 'SOIVH_CABANG.SOIVH_InvoiceID');


            // Step 2: gabung dengan master salesman dan total per salesman
            $query = DB::connection('sqlsrv_wh')->table(DB::raw("({$invoiceSub->toSql()}) as invoices"))
                ->mergeBindings($invoiceSub)
                ->join('MFSSM', 'invoices.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
                ->where('MFSSM.MFSSM_Active', 1)
                ->select(
                    'MFSSM.MFSSM_SalesmanID',
                    'MFSSM.MFSSM_Description as salesman_name',
                    DB::raw('SUM(invoices.invoice_total) as total_sales'),
                    DB::raw('COUNT(invoices.SOIVH_InvoiceID) as total_invoices')
                )
                ->groupBy('MFSSM.MFSSM_SalesmanID', 'MFSSM.MFSSM_Description');

            if ($request->filled('search_salesperson')) {
                $query->where('MFSSM.MFSSM_Description', 'like', '%' . $request->search_salesperson . '%');
            }

            $salesPerformance = $query
                ->orderBy('MFSSM_SalesmanID', 'asc')
                ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

            $formattedData = $salesPerformance->getCollection()->map(function ($item) {
                return [
                    'salesman_name' => $item->salesman_name,
                    'total_invoices' => number_format($item->total_invoices, 0, ',', '.') . ' Invoices',
                    'total_sales' => 'Rp ' . number_format($item->total_sales, 0, ',', '.'),
                    'action' => '<a href="' . route('salesperson-sales.transactions.milenia.branch.show', $item->MFSSM_SalesmanID) . '"
                                class="btn btn-sm btn-info"><i class="feather-eye me-1"></i> Lihat Detail</a>'
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'next_cursor' => $salesPerformance->nextCursor()?->encode(),
                'prev_cursor' => $salesPerformance->previousCursor()?->encode(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data penjualan sales: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Terjadi kesalahan pada server saat mengambil data.'], 500);
        }
    }

    public function getDataMap(Request $request)
    {
        try {
            $cursor = $request->input('cursor');
            $perPage = 5;

            // Step 1: ambil semua invoice unik + salesman_id + invoice_amount
            $invoiceSub = DB::connection('sqlsrv_snx')->table('SOIVD')
                ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
                ->select(
                    'SOIVD.SOIVD_SalesmanID',
                    'SOIVH.SOIVH_InvoiceID',
                    DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) AS invoice_total')
                )
                ->groupBy('SOIVD.SOIVD_SalesmanID', 'SOIVH.SOIVH_InvoiceID');


            // Step 2: gabung dengan master salesman dan total per salesman
            $query = DB::connection('sqlsrv_snx')->table(DB::raw("({$invoiceSub->toSql()}) as invoices"))
                ->mergeBindings($invoiceSub)
                ->join('MFSSM', 'invoices.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
                ->where('MFSSM.MFSSM_Active', 1)
                ->select(
                    'MFSSM.MFSSM_SalesmanID',
                    'MFSSM.MFSSM_Description as salesman_name',
                    DB::raw('SUM(invoices.invoice_total) as total_sales'),
                    DB::raw('COUNT(invoices.SOIVH_InvoiceID) as total_invoices')
                )
                ->groupBy('MFSSM.MFSSM_SalesmanID', 'MFSSM.MFSSM_Description');

            if ($request->filled('search_salesperson')) {
                $query->where('MFSSM.MFSSM_Description', 'like', '%' . $request->search_salesperson . '%');
            }

            $salesPerformance = $query
                ->orderBy('MFSSM_SalesmanID', 'asc')
                ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

            $formattedData = $salesPerformance->getCollection()->map(function ($item) {
                return [
                    'salesman_name' => $item->salesman_name,
                    'total_invoices' => number_format($item->total_invoices, 0, ',', '.') . ' Invoices',
                    'total_sales' => 'Rp ' . number_format($item->total_sales, 0, ',', '.'),
                    'action' => '<a href="' . route('salesperson-sales.transactions.map.show', $item->MFSSM_SalesmanID) . '"
                                class="btn btn-sm btn-info"><i class="feather-eye me-1"></i> Lihat Detail</a>'
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'next_cursor' => $salesPerformance->nextCursor()?->encode(),
                'prev_cursor' => $salesPerformance->previousCursor()?->encode(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data penjualan sales: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Terjadi kesalahan pada server saat mengambil data.'], 500);
        }
    }

    public function getDataMapBranch(Request $request)
    {
        try {
            $cursor = $request->input('cursor');
            $perPage = 5;

            // Step 1: ambil semua invoice unik + salesman_id + invoice_amount
            $invoiceSub = DB::connection('sqlsrv_snx')->table('SOIVD_CABANG')
                ->join('SOIVH_CABANG', 'SOIVD_CABANG.SOIVD_InvoiceID', '=', 'SOIVH_CABANG.SOIVH_InvoiceID')
                ->select(
                    'SOIVD_CABANG.SOIVD_SalesmanID',
                    'SOIVH_CABANG.SOIVH_InvoiceID',
                    DB::raw('SUM(SOIVD_CABANG.SOIVD_LineInvoiceAmount) AS invoice_total')
                )
                ->groupBy('SOIVD_CABANG.SOIVD_SalesmanID', 'SOIVH_CABANG.SOIVH_InvoiceID');


            // Step 2: gabung dengan master salesman dan total per salesman
            $query = DB::connection('sqlsrv_snx')->table(DB::raw("({$invoiceSub->toSql()}) as invoices"))
                ->mergeBindings($invoiceSub)
                ->join('MFSSM', 'invoices.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
                ->where('MFSSM.MFSSM_Active', 1)
                ->select(
                    'MFSSM.MFSSM_SalesmanID',
                    'MFSSM.MFSSM_Description as salesman_name',
                    DB::raw('SUM(invoices.invoice_total) as total_sales'),
                    DB::raw('COUNT(invoices.SOIVH_InvoiceID) as total_invoices')
                )
                ->groupBy('MFSSM.MFSSM_SalesmanID', 'MFSSM.MFSSM_Description');

            if ($request->filled('search_salesperson')) {
                $query->where('MFSSM.MFSSM_Description', 'like', '%' . $request->search_salesperson . '%');
            }

            $salesPerformance = $query
                ->orderBy('MFSSM_SalesmanID', 'asc')
                ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

            $formattedData = $salesPerformance->getCollection()->map(function ($item) {
                return [
                    'salesman_name' => $item->salesman_name,
                    'total_invoices' => number_format($item->total_invoices, 0, ',', '.') . ' Invoices',
                    'total_sales' => 'Rp ' . number_format($item->total_sales, 0, ',', '.'),
                    'action' => '<a href="' . route('salesperson-sales.transactions.map.branch.show', $item->MFSSM_SalesmanID) . '"
                                class="btn btn-sm btn-info"><i class="feather-eye me-1"></i> Lihat Detail</a>'
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'next_cursor' => $salesPerformance->nextCursor()?->encode(),
                'prev_cursor' => $salesPerformance->previousCursor()?->encode(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data penjualan sales: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Terjadi kesalahan pada server saat mengambil data.'], 500);
        }
    }

    public function showMilenia($id)
    {
        try {
            $salesperson = DB::connection('sqlsrv_wh')->table('MFSSM')
                ->where('MFSSM_SalesmanID', $id)
                ->firstOrFail();

            return view('pages.feat.sales.salesperson-sales.show-milenia', compact('salesperson'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan detail sales: ' . $e->getMessage(), ['salesman_id' => $id, 'exception' => $e]);

            abort(500, 'Tidak dapat memproses permintaan Anda saat ini.');
        }
    }

    public function showMileniaBranch($id)
    {
        try {
            $salesperson = DB::connection('sqlsrv_wh')->table('MFSSM')
                ->where('MFSSM_SalesmanID', $id)
                ->firstOrFail();

            return view('pages.feat.sales.salesperson-sales.show-milenia-branch', compact('salesperson'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan detail sales: ' . $e->getMessage(), ['salesman_id' => $id, 'exception' => $e]);

            abort(500, 'Tidak dapat memproses permintaan Anda saat ini.');
        }
    }

    public function showMap($id)
    {
        try {
            $salesperson = DB::connection('sqlsrv_snx')->table('MFSSM')
                ->where('MFSSM_SalesmanID', $id)
                ->firstOrFail();

            return view('pages.feat.sales.salesperson-sales.show-map', compact('salesperson'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan detail sales: ' . $e->getMessage(), ['salesman_id' => $id, 'exception' => $e]);

            abort(500, 'Tidak dapat memproses permintaan Anda saat ini.');
        }
    }
    public function showMapBranch($id)
    {
        try {
            $salesperson = DB::connection('sqlsrv_snx')->table('MFSSM')
                ->where('MFSSM_SalesmanID', $id)
                ->firstOrFail();

            return view('pages.feat.sales.salesperson-sales.show-map-branch', compact('salesperson'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan detail sales: ' . $e->getMessage(), ['salesman_id' => $id, 'exception' => $e]);

            abort(500, 'Tidak dapat memproses permintaan Anda saat ini.');
        }
    }

    public function getSalespersonTransactionsDataMilenia($id, Request $request)
    {
        try {
            $cursor = $request->input('cursor');
            $perPage = 15;

            $query = DB::connection('sqlsrv_wh')->table('SOIVD')
                ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
                ->join('MFCUS', 'SOIVH.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
                ->join('MFIMA', 'SOIVD.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
                ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
                ->join('MFID', 'MFIMA.MFIMA_Division', '=', 'MFID.MFID_DivisionID')
                ->where('SOIVD.SOIVD_SalesmanID', $id)
                ->select(
                    'SOIVH.SOIVH_InvoiceID',
                    'SOIVH.SOIVH_InvoiceDate',
                    'MFCUS.MFCUS_Description as customer_name',
                    'MFIMA.MFIMA_ItemID as item_id',
                    'MFIMA.MFIMA_Description as item_name',
                    'MFID.MFID_Description as item_division',
                    'MFIB.MFIB_Description as brand_name',
                    'SOIVD.SOIVD_OrgPieceInvoiceAmount as unit_price',
                    'SOIVD.SOIVD_LineInvoiceAmount as total_invoice_amount',
                    'SOIVD.SOIVD_OrderQty as order_qty'
                )
                ->groupBy(
                    'SOIVH.SOIVH_InvoiceID',
                    'SOIVH.SOIVH_InvoiceDate',
                    'MFCUS.MFCUS_Description',
                    'MFIMA.MFIMA_ItemID',
                    'MFIMA.MFIMA_Description',
                    'MFID.MFID_Description',
                    'MFIB.MFIB_Description',
                    'SOIVD.SOIVD_OrgPieceInvoiceAmount',
                    'SOIVD.SOIVD_LineInvoiceAmount',
                    'SOIVD.SOIVD_OrderQty'
                );

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('SOIVH.SOIVH_InvoiceDate', [$request->start_date, $request->end_date]);
            }

            $invoiceSub = DB::connection('sqlsrv_wh')->table('SOIVD')
                ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
                ->select(
                    'SOIVD.SOIVD_SalesmanID',
                    'SOIVH.SOIVH_InvoiceID',
                    DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) AS invoice_total')
                )
                ->where('SOIVD.SOIVD_SalesmanID', $id)
                ->groupBy('SOIVD.SOIVD_SalesmanID', 'SOIVH.SOIVH_InvoiceID');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $invoiceSub->whereBetween('SOIVH.SOIVH_InvoiceDate', [$request->start_date, $request->end_date]);
            }

            $queryForTotal = DB::connection('sqlsrv_wh')
                ->table(DB::raw("({$invoiceSub->toSql()}) as inv"))
                ->mergeBindings($invoiceSub);

            $totalFilteredSales = $queryForTotal->sum('invoice_total');

            $transactions = $query
                ->orderBy('SOIVH_InvoiceID', 'desc')
                ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

            $formattedData = $transactions->getCollection()->map(function ($item) {
                return [
                    'SOIVH_InvoiceID' => $item->SOIVH_InvoiceID,
                    'SOIVH_InvoiceDate' => Carbon::parse($item->SOIVH_InvoiceDate)->translatedFormat('d F Y'),
                    'customer_name' => $item->customer_name,
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'brand_name' => $item->brand_name,
                    'item_division' => $item->item_division,
                    'order_qty' => $item->order_qty,
                    'unit_price' => 'Rp ' . number_format($item->unit_price, 0, ',', '.'),
                    'SOIVD_LineInvoiceAmount' => 'Rp ' . number_format($item->total_invoice_amount, 0, ',', '.'),
                    // 'action' => '<a href="' . route('customer-transaction-milenia.show', $item->SOIVH_InvoiceID) . '"
                    //             class="btn btn-sm btn-info" target="_blank">
                    //             <i class="feather-eye me-1"></i> Lihat Detail
                    //         </a>'
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'next_cursor' => $transactions->nextCursor()?->encode(),
                'prev_cursor' => $transactions->previousCursor()?->encode(),
                'total_filtered_sales' => 'Rp ' . number_format($totalFilteredSales, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data transaksi sales: ' . $e->getMessage(), [
                'salesman_id' => $id,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Terjadi kesalahan pada server saat mengambil data transaksi.'], 500);
        }
    }

    public function getSalespersonTransactionsDataMileniaBranch($id, Request $request)
    {
        try {
            $cursor = $request->input('cursor');
            $perPage = 15;

            $query = DB::connection('sqlsrv_wh')->table('SOIVD_CABANG')
                ->join('SOIVH_CABANG', 'SOIVD_CABANG.SOIVD_InvoiceID', '=', 'SOIVH_CABANG.SOIVH_InvoiceID')
                ->join('MFCUS', 'SOIVH_CABANG.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
                ->join('MFIMA', 'SOIVD_CABANG.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
                ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
                ->join('MFID', 'MFIMA.MFIMA_Division', '=', 'MFID.MFID_DivisionID')
                ->where('SOIVD_CABANG.SOIVD_SalesmanID', $id)
                ->select(
                    'SOIVH_CABANG.SOIVH_InvoiceID',
                    'SOIVH_CABANG.SOIVH_InvoiceDate',
                    'MFCUS.MFCUS_Description as customer_name',
                    'MFIMA.MFIMA_ItemID as item_id',
                    'MFIMA.MFIMA_Description as item_name',
                    'MFID.MFID_Description as item_division',
                    'MFIB.MFIB_Description as brand_name',
                    'SOIVD_CABANG.SOIVD_OrgPieceInvoiceAmount as unit_price',
                    'SOIVD_CABANG.SOIVD_LineInvoiceAmount as total_invoice_amount',
                    'SOIVD_CABANG.SOIVD_OrderQty as order_qty'
                )
                ->groupBy(
                    'SOIVH_CABANG.SOIVH_InvoiceID',
                    'SOIVH_CABANG.SOIVH_InvoiceDate',
                    'MFCUS.MFCUS_Description',
                    'MFIMA.MFIMA_ItemID',
                    'MFIMA.MFIMA_Description',
                    'MFID.MFID_Description',
                    'MFIB.MFIB_Description',
                    'SOIVD_CABANG.SOIVD_OrgPieceInvoiceAmount',
                    'SOIVD_CABANG.SOIVD_LineInvoiceAmount',
                    'SOIVD_CABANG.SOIVD_OrderQty'
                );

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('SOIVH_CABANG.SOIVH_InvoiceDate', [$request->start_date, $request->end_date]);
            }

            $invoiceSub = DB::connection('sqlsrv_wh')->table('SOIVD_CABANG')
                ->join('SOIVH_CABANG', 'SOIVD_CABANG.SOIVD_InvoiceID', '=', 'SOIVH_CABANG.SOIVH_InvoiceID')
                ->select(
                    'SOIVD_CABANG.SOIVD_SalesmanID',
                    'SOIVH_CABANG.SOIVH_InvoiceID',
                    DB::raw('SUM(SOIVD_CABANG.SOIVD_LineInvoiceAmount) AS invoice_total')
                )
                ->where('SOIVD_CABANG.SOIVD_SalesmanID', $id)
                ->groupBy('SOIVD_CABANG.SOIVD_SalesmanID', 'SOIVH_CABANG.SOIVH_InvoiceID');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $invoiceSub->whereBetween('SOIVH_CABANG.SOIVH_InvoiceDate', [$request->start_date, $request->end_date]);
            }

            $queryForTotal = DB::connection('sqlsrv_wh')
                ->table(DB::raw("({$invoiceSub->toSql()}) as inv"))
                ->mergeBindings($invoiceSub);

            $totalFilteredSales = $queryForTotal->sum('invoice_total');

            $transactions = $query
                ->orderBy('SOIVH_InvoiceID', 'desc')
                ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

            $formattedData = $transactions->getCollection()->map(function ($item) {
                return [
                    'SOIVH_InvoiceID' => $item->SOIVH_InvoiceID,
                    'SOIVH_InvoiceDate' => Carbon::parse($item->SOIVH_InvoiceDate)->translatedFormat('d F Y'),
                    'customer_name' => $item->customer_name,
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'brand_name' => $item->brand_name,
                    'item_division' => $item->item_division,
                    'order_qty' => $item->order_qty,
                    'unit_price' => 'Rp ' . number_format($item->unit_price, 0, ',', '.'),
                    'SOIVD_LineInvoiceAmount' => 'Rp ' . number_format($item->total_invoice_amount, 0, ',', '.'),
                    // 'action' => '<a href="' . route('customer-transaction-milenia.show', $item->SOIVH_InvoiceID) . '"
                    //             class="btn btn-sm btn-info" target="_blank">
                    //             <i class="feather-eye me-1"></i> Lihat Detail
                    //         </a>'
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'next_cursor' => $transactions->nextCursor()?->encode(),
                'prev_cursor' => $transactions->previousCursor()?->encode(),
                'total_filtered_sales' => 'Rp ' . number_format($totalFilteredSales, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data transaksi sales: ' . $e->getMessage(), [
                'salesman_id' => $id,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Terjadi kesalahan pada server saat mengambil data transaksi.'], 500);
        }
    }

    public function getSalespersonTransactionsDataMap($id, Request $request)
    {
        try {
            $cursor = $request->input('cursor');
            $perPage = 15;

            $query = DB::connection('sqlsrv_snx')->table('SOIVD')
                ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
                ->join('MFCUS', 'SOIVH.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
                ->join('MFIMA', 'SOIVD.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
                ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
                ->join('MFID', 'MFIMA.MFIMA_Division', '=', 'MFID.MFID_DivisionID')
                ->where('SOIVD.SOIVD_SalesmanID', $id)
                ->select(
                    'SOIVH.SOIVH_InvoiceID',
                    'SOIVH.SOIVH_InvoiceDate',
                    'MFCUS.MFCUS_Description as customer_name',
                    'MFIMA.MFIMA_ItemID as item_id',
                    'MFIMA.MFIMA_Description as item_name',
                    'MFID.MFID_Description as item_division',
                    'MFIB.MFIB_Description as brand_name',
                    'SOIVD.SOIVD_OrgPieceInvoiceAmount as unit_price',
                    'SOIVD.SOIVD_LineInvoiceAmount as total_invoice_amount',
                    'SOIVD.SOIVD_OrderQty as order_qty'
                )
                ->groupBy(
                    'SOIVH.SOIVH_InvoiceID',
                    'SOIVH.SOIVH_InvoiceDate',
                    'MFCUS.MFCUS_Description',
                    'MFIMA.MFIMA_ItemID',
                    'MFIMA.MFIMA_Description',
                    'MFID.MFID_Description',
                    'MFIB.MFIB_Description',
                    'SOIVD.SOIVD_OrgPieceInvoiceAmount',
                    'SOIVD.SOIVD_LineInvoiceAmount',
                    'SOIVD.SOIVD_OrderQty'
                );

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('SOIVH.SOIVH_InvoiceDate', [$request->start_date, $request->end_date]);
            }

            $invoiceSub = DB::connection('sqlsrv_snx')->table('SOIVD')
                ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
                ->select(
                    'SOIVD.SOIVD_SalesmanID',
                    'SOIVH.SOIVH_InvoiceID',
                    DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) AS invoice_total')
                )
                ->where('SOIVD.SOIVD_SalesmanID', $id)
                ->groupBy('SOIVD.SOIVD_SalesmanID', 'SOIVH.SOIVH_InvoiceID');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $invoiceSub->whereBetween('SOIVH.SOIVH_InvoiceDate', [$request->start_date, $request->end_date]);
            }

            $queryForTotal = DB::connection('sqlsrv_snx')
                ->table(DB::raw("({$invoiceSub->toSql()}) as inv"))
                ->mergeBindings($invoiceSub);

            $totalFilteredSales = $queryForTotal->sum('invoice_total');

            $transactions = $query
                ->orderBy('SOIVH_InvoiceID', 'desc')
                ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

            $formattedData = $transactions->getCollection()->map(function ($item) {
                return [
                    'SOIVH_InvoiceID' => $item->SOIVH_InvoiceID,
                    'SOIVH_InvoiceDate' => Carbon::parse($item->SOIVH_InvoiceDate)->translatedFormat('d F Y'),
                    'customer_name' => $item->customer_name,
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'brand_name' => $item->brand_name,
                    'item_division' => $item->item_division,
                    'order_qty' => $item->order_qty,
                    'unit_price' => 'Rp ' . number_format($item->unit_price, 0, ',', '.'),
                    'SOIVD_LineInvoiceAmount' => 'Rp ' . number_format($item->total_invoice_amount, 0, ',', '.'),
                    // 'action' => '<a href="' . route('customer-transaction-milenia.show', $item->SOIVH_InvoiceID) . '"
                    //             class="btn btn-sm btn-info" target="_blank">
                    //             <i class="feather-eye me-1"></i> Lihat Detail
                    //         </a>'
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'next_cursor' => $transactions->nextCursor()?->encode(),
                'prev_cursor' => $transactions->previousCursor()?->encode(),
                'total_filtered_sales' => 'Rp ' . number_format($totalFilteredSales, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data transaksi sales: ' . $e->getMessage(), [
                'salesman_id' => $id,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Terjadi kesalahan pada server saat mengambil data transaksi.'], 500);
        }
    }

    public function getSalespersonTransactionsDataMapBranch($id, Request $request)
    {
        try {
            $cursor = $request->input('cursor');
            $perPage = 15;

            $query = DB::connection('sqlsrv_snx')->table('SOIVD_CABANG')
                ->join('SOIVH_CABANG', 'SOIVD_CABANG.SOIVD_InvoiceID', '=', 'SOIVH_CABANG.SOIVH_InvoiceID')
                ->join('MFCUS', 'SOIVH_CABANG.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
                ->join('MFIMA', 'SOIVD_CABANG.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
                ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
                ->join('MFID', 'MFIMA.MFIMA_Division', '=', 'MFID.MFID_DivisionID')
                ->where('SOIVD_CABANG.SOIVD_SalesmanID', $id)
                ->select(
                    'SOIVH_CABANG.SOIVH_InvoiceID',
                    'SOIVH_CABANG.SOIVH_InvoiceDate',
                    'MFCUS.MFCUS_Description as customer_name',
                    'MFIMA.MFIMA_ItemID as item_id',
                    'MFIMA.MFIMA_Description as item_name',
                    'MFID.MFID_Description as item_division',
                    'MFIB.MFIB_Description as brand_name',
                    'SOIVD_CABANG.SOIVD_OrgPieceInvoiceAmount as unit_price',
                    'SOIVD_CABANG.SOIVD_LineInvoiceAmount as total_invoice_amount',
                    'SOIVD_CABANG.SOIVD_OrderQty as order_qty'
                )
                ->groupBy(
                    'SOIVH_CABANG.SOIVH_InvoiceID',
                    'SOIVH_CABANG.SOIVH_InvoiceDate',
                    'MFCUS.MFCUS_Description',
                    'MFIMA.MFIMA_ItemID',
                    'MFIMA.MFIMA_Description',
                    'MFID.MFID_Description',
                    'MFIB.MFIB_Description',
                    'SOIVD_CABANG.SOIVD_OrgPieceInvoiceAmount',
                    'SOIVD_CABANG.SOIVD_LineInvoiceAmount',
                    'SOIVD_CABANG.SOIVD_OrderQty'
                );

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('SOIVH_CABANG.SOIVH_InvoiceDate', [$request->start_date, $request->end_date]);
            }

            $invoiceSub = DB::connection('sqlsrv_snx')->table('SOIVD_CABANG')
                ->join('SOIVH_CABANG', 'SOIVD_CABANG.SOIVD_InvoiceID', '=', 'SOIVH_CABANG.SOIVH_InvoiceID')
                ->select(
                    'SOIVD_CABANG.SOIVD_SalesmanID',
                    'SOIVH_CABANG.SOIVH_InvoiceID',
                    DB::raw('SUM(SOIVD_CABANG.SOIVD_LineInvoiceAmount) AS invoice_total')
                )
                ->where('SOIVD_CABANG.SOIVD_SalesmanID', $id)
                ->groupBy('SOIVD_CABANG.SOIVD_SalesmanID', 'SOIVH_CABANG.SOIVH_InvoiceID');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $invoiceSub->whereBetween('SOIVH_CABANG.SOIVH_InvoiceDate', [$request->start_date, $request->end_date]);
            }

            $queryForTotal = DB::connection('sqlsrv_snx')
                ->table(DB::raw("({$invoiceSub->toSql()}) as inv"))
                ->mergeBindings($invoiceSub);

            $totalFilteredSales = $queryForTotal->sum('invoice_total');

            $transactions = $query
                ->orderBy('SOIVH_InvoiceID', 'desc')
                ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

            $formattedData = $transactions->getCollection()->map(function ($item) {
                return [
                    'SOIVH_InvoiceID' => $item->SOIVH_InvoiceID,
                    'SOIVH_InvoiceDate' => Carbon::parse($item->SOIVH_InvoiceDate)->translatedFormat('d F Y'),
                    'customer_name' => $item->customer_name,
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'brand_name' => $item->brand_name,
                    'item_division' => $item->item_division,
                    'order_qty' => $item->order_qty,
                    'unit_price' => 'Rp ' . number_format($item->unit_price, 0, ',', '.'),
                    'SOIVD_LineInvoiceAmount' => 'Rp ' . number_format($item->total_invoice_amount, 0, ',', '.'),
                    // 'action' => '<a href="' . route('customer-transaction-milenia.show', $item->SOIVH_InvoiceID) . '"
                    //             class="btn btn-sm btn-info" target="_blank">
                    //             <i class="feather-eye me-1"></i> Lihat Detail
                    //         </a>'
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'next_cursor' => $transactions->nextCursor()?->encode(),
                'prev_cursor' => $transactions->previousCursor()?->encode(),
                'total_filtered_sales' => 'Rp ' . number_format($totalFilteredSales, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data transaksi sales: ' . $e->getMessage(), [
                'salesman_id' => $id,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Terjadi kesalahan pada server saat mengambil data transaksi.'], 500);
        }
    }
}
