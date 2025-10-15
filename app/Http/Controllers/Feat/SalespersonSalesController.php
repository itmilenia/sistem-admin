<?php

namespace App\Http\Controllers\Feat;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SalespersonSalesController extends Controller
{
    public function index()
    {
        return view('pages.feat.sales.salesperson-sales.index');
    }

    public function getData(Request $request)
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
                    'SOIVH.SOIVH_InvoiceAmount'
                )
                ->groupBy('SOIVD.SOIVD_SalesmanID', 'SOIVH.SOIVH_InvoiceID', 'SOIVH.SOIVH_InvoiceAmount');

            // Step 2: gabung dengan master salesman dan total per salesman
            $query = DB::connection('sqlsrv_wh')->table(DB::raw("({$invoiceSub->toSql()}) as invoices"))
                ->mergeBindings($invoiceSub)
                ->join('MFSSM', 'invoices.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
                ->where('MFSSM.MFSSM_Active', 1)
                ->select(
                    'MFSSM.MFSSM_SalesmanID',
                    'MFSSM.MFSSM_Description as salesman_name',
                    DB::raw('SUM(invoices.SOIVH_InvoiceAmount) as total_sales'),
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
                    'action' => '<a href="' . route('salesperson-sales.show', $item->MFSSM_SalesmanID) . '"
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

    public function show($id)
    {
        try {
            $salesperson = DB::connection('sqlsrv_wh')->table('MFSSM')
                ->where('MFSSM_SalesmanID', $id)
                ->firstOrFail();

            return view('pages.feat.sales.salesperson-sales.show', compact('salesperson'));
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan detail sales: ' . $e->getMessage(), ['salesman_id' => $id, 'exception' => $e]);

            abort(500, 'Tidak dapat memproses permintaan Anda saat ini.');
        }
    }

    public function getSalespersonTransactionsData($id, Request $request)
    {
        try {
            $cursor = $request->input('cursor');
            $perPage = 15;

            // Query diubah untuk mengelompokkan berdasarkan Invoice
            $query = DB::connection('sqlsrv_wh')->table('SOIVD')
                ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
                ->join('MFCUS', 'SOIVH.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
                ->where('SOIVD.SOIVD_SalesmanID', $id)
                ->select(
                    'SOIVH.SOIVH_InvoiceID',
                    'SOIVH.SOIVH_InvoiceDate',
                    'MFCUS.MFCUS_Description as customer_name',
                    'SOIVH.SOIVH_InvoiceAmount as total_invoice_amount'
                )
                ->groupBy(
                    'SOIVH.SOIVH_InvoiceID',
                    'SOIVH.SOIVH_InvoiceDate',
                    'MFCUS.MFCUS_Description',
                    'SOIVH.SOIVH_InvoiceAmount'
                );

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->havingBetween('SOIVH.SOIVH_InvoiceDate', [$request->start_date, $request->end_date]);
            }

            $queryForTotal = clone $query;
            $totalFilteredSales = $queryForTotal->get()->sum('total_invoice_amount');

            $transactions = $query
                ->orderBy('SOIVH_InvoiceID', 'desc')
                ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

            $formattedData = $transactions->getCollection()->map(function ($item) {
                return [
                    'SOIVH_InvoiceID' => $item->SOIVH_InvoiceID,
                    'SOIVH_InvoiceDate' => Carbon::parse($item->SOIVH_InvoiceDate)->translatedFormat('d F Y'),
                    'customer_name' => $item->customer_name,
                    'SOIVD_LineInvoiceAmount' => 'Rp ' . number_format($item->total_invoice_amount, 0, ',', '.'),
                    'action' => '<a href="' . route('customer-transaction.show', $item->SOIVH_InvoiceID) . '"
                                    class="btn btn-sm btn-info" target="_blank">
                                    <i class="feather-eye me-1"></i> Lihat Detail
                                </a>'
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'next_cursor' => $transactions->nextCursor()?->encode(),
                'prev_cursor' => $transactions->previousCursor()?->encode(),
                'total_filtered_sales' => 'Rp ' . number_format($totalFilteredSales, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data transaksi sales: ' . $e->getMessage(), ['salesman_id' => $id, 'exception' => $e]);
            return response()->json(['error' => 'Terjadi kesalahan pada server saat mengambil data transaksi.'], 500);
        }
    }
}
