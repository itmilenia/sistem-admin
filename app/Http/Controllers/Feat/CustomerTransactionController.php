<?php

namespace App\Http\Controllers\Feat;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CustomerTransactionController extends Controller
{
    public function landing()
    {
        return view('pages.feat.customer.customer-transaction.landing');
    }

    public function indexMilenia()
    {
        return view('pages.feat.customer.customer-transaction.index-milenia');
    }

    public function indexMap()
    {
        return view('pages.feat.customer.customer-transaction.index-map');
    }

    public function getDataMilenia(Request $request)
    {
        $cursor = $request->input('cursor');
        $perPage = 10;

        $query = DB::connection('sqlsrv_wh')->table('SOIVH')
            ->join('MFCUS', 'SOIVH.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
            ->select(
                'SOIVH.SOIVH_InvoiceID',
                'SOIVH.SOIVH_InvoiceDate',
                'MFCUS.MFCUS_Description as customer_name',
                'SOIVH.SOIVH_DueDate',
                'SOIVH.SOIVH_InvoiceAmount'
            )
            ->orderBy('SOIVH.SOIVH_InvoiceID', 'desc');

        // filter (No. Invoice dan Nama Customer)
        if ($request->filled('search_invoice')) {
            $query->where('SOIVH.SOIVH_InvoiceID', 'like', '%' . $request->search_invoice . '%');
        }

        if ($request->filled('search_customer')) {
            $query->where('MFCUS.MFCUS_Description', 'like', '%' . $request->search_customer . '%');
        }

        $purchases = $query->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

        $formattedData = $purchases->getCollection()->map(function ($item) {
            return [
                'SOIVH_InvoiceID' => $item->SOIVH_InvoiceID,
                'customer_name' => $item->customer_name,
                'SOIVH_InvoiceDate' => Carbon::parse($item->SOIVH_InvoiceDate)->translatedFormat('d F Y'),
                'SOIVH_DueDate' => Carbon::parse($item->SOIVH_DueDate)->translatedFormat('d F Y'),
                'SOIVH_InvoiceAmount' => 'Rp ' . number_format($item->SOIVH_InvoiceAmount, 0, ',', '.'),
                'action' => '<a href="' . route('customer-transaction-milenia.show', $item->SOIVH_InvoiceID) . '" class="btn btn-sm btn-info"><i class="feather-eye me-1"></i> Lihat</a>'
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'next_cursor' => $purchases->nextCursor()?->encode(),
            'prev_cursor' => $purchases->previousCursor()?->encode(),
        ]);
    }

    public function getDataMap(Request $request)
    {
        $cursor = $request->input('cursor');
        $perPage = 10;

        $query = DB::connection('sqlsrv_snx')->table('SOIVH')
            ->join('MFCUS', 'SOIVH.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
            ->select(
                'SOIVH.SOIVH_InvoiceID',
                'SOIVH.SOIVH_InvoiceDate',
                'MFCUS.MFCUS_Description as customer_name',
                'SOIVH.SOIVH_DueDate',
                'SOIVH.SOIVH_InvoiceAmount'
            )
            ->orderBy('SOIVH.SOIVH_InvoiceID', 'desc');

        // filter (No. Invoice dan Nama Customer)
        if ($request->filled('search_invoice')) {
            $query->where('SOIVH.SOIVH_InvoiceID', 'like', '%' . $request->search_invoice . '%');
        }

        if ($request->filled('search_customer')) {
            $query->where('MFCUS.MFCUS_Description', 'like', '%' . $request->search_customer . '%');
        }

        $purchases = $query->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

        $formattedData = $purchases->getCollection()->map(function ($item) {
            return [
                'SOIVH_InvoiceID' => $item->SOIVH_InvoiceID,
                'customer_name' => $item->customer_name,
                'SOIVH_InvoiceDate' => Carbon::parse($item->SOIVH_InvoiceDate)->translatedFormat('d F Y'),
                'SOIVH_DueDate' => Carbon::parse($item->SOIVH_DueDate)->translatedFormat('d F Y'),
                'SOIVH_InvoiceAmount' => 'Rp ' . number_format($item->SOIVH_InvoiceAmount, 0, ',', '.'),
                'action' => '<a href="' . route('customer-transaction-map.show', $item->SOIVH_InvoiceID) . '" class="btn btn-sm btn-info"><i class="feather-eye me-1"></i> Lihat</a>'
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'next_cursor' => $purchases->nextCursor()?->encode(),
            'prev_cursor' => $purchases->previousCursor()?->encode(),
        ]);
    }

    public function showMilenia($id)
    {
        $invoiceHeader = DB::connection('sqlsrv_wh')->table('SOIVH')
            ->leftJoin('MFCUS', 'SOIVH.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
            ->leftJoin('MFTRM', 'SOIVH.SOIVH_TermsID', '=', 'MFTRM.MFTRM_TermsID')
            ->select(
                'SOIVH_InvoiceID',
                'SOIVH_InvoiceDate',
                'MFCUS.MFCUS_Description as customer_name',
                'SOIVH_CurrencyID',
                'SOIVH_Note',
                'SOIVH_TaxID',
                'MFTRM.MFTRM_Description as terms_description',
                'SOIVH_DueDate',
                'SOIVH_InvoiceAmount',
                'SOIVH_TaxAmount',
                'SOIVH_InvoiceAmountGross',
                'SOIVH_DiscAmount',
                'SOIVH_UserID'
            )
            ->where('SOIVH_InvoiceID', $id)
            ->first();

        if (!$invoiceHeader) {
            abort(404, 'Data Invoice tidak ditemukan.');
        }

        $invoiceDetails = DB::connection('sqlsrv_wh')->table('SOIVD')
            ->leftJoin('MFSSM', 'SOIVD.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
            ->leftJoin('MFIMA', 'SOIVD.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->select(
                'SOIVD_LineNbr',
                'SOIVD_DeliveryID',
                'SOIVD_OrderID',
                'SOIVD_OrderDate',
                'MFSSM.MFSSM_Description as salesman_name',
                'MFIMA.MFIMA_Description as item_name',
                'SOIVD_UM',
                'SOIVD_OrderQty',
                'SOIVD_OrgPieceInvoiceAmount',
                'SOIVD_OrgInvoiceAmount',
                'SOIVD_DiscMarkPersen',
                'SOIVD_DiscMarkAmount',
                'SOIVD_TaxPersen',
                'SOIVD_TaxAmount',
                'SOIVD_LineInvoiceAmount'
            )
            ->where('SOIVD_InvoiceID', $id)
            ->orderBy('SOIVD_LineNbr', 'asc')
            ->get();

        return view('pages.feat.customer.customer-transaction.show-milenia', compact('invoiceHeader', 'invoiceDetails'));
    }

    public function showMap($id)
    {
        $invoiceHeader = DB::connection('sqlsrv_snx')->table('SOIVH')
            ->leftJoin('MFCUS', 'SOIVH.SOIVH_CustomerID', '=', 'MFCUS.MFCUS_CustomerID')
            ->leftJoin('MFTRM', 'SOIVH.SOIVH_TermsID', '=', 'MFTRM.MFTRM_TermsID')
            ->select(
                'SOIVH_InvoiceID',
                'SOIVH_InvoiceDate',
                'MFCUS.MFCUS_Description as customer_name',
                'SOIVH_CurrencyID',
                'SOIVH_Note',
                'SOIVH_TaxID',
                'MFTRM.MFTRM_Description as terms_description',
                'SOIVH_DueDate',
                'SOIVH_InvoiceAmount',
                'SOIVH_TaxAmount',
                'SOIVH_InvoiceAmountGross',
                'SOIVH_DiscAmount',
                'SOIVH_UserID'
            )
            ->where('SOIVH_InvoiceID', $id)
            ->first();

        if (!$invoiceHeader) {
            abort(404, 'Data Invoice tidak ditemukan.');
        }

        $invoiceDetails = DB::connection('sqlsrv_snx')->table('SOIVD')
            ->leftJoin('MFSSM', 'SOIVD.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
            ->leftJoin('MFIMA', 'SOIVD.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->select(
                'SOIVD_LineNbr',
                'SOIVD_DeliveryID',
                'SOIVD_OrderID',
                'SOIVD_OrderDate',
                'MFSSM.MFSSM_Description as salesman_name',
                'MFIMA.MFIMA_Description as item_name',
                'SOIVD_UM',
                'SOIVD_OrderQty',
                'SOIVD_OrgPieceInvoiceAmount',
                'SOIVD_OrgInvoiceAmount',
                'SOIVD_DiscMarkPersen',
                'SOIVD_DiscMarkAmount',
                'SOIVD_TaxPersen',
                'SOIVD_TaxAmount',
                'SOIVD_LineInvoiceAmount'
            )
            ->where('SOIVD_InvoiceID', $id)
            ->orderBy('SOIVD_LineNbr', 'asc')
            ->get();

        return view('pages.feat.customer.customer-transaction.show-map', compact('invoiceHeader', 'invoiceDetails'));
    }
}
