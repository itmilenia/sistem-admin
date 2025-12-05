<?php

namespace App\Http\Controllers\Feat;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendingSalesOrderController extends Controller
{
    public function landing()
    {
        return view('pages.feat.sales.pending-sales-order.landing');
    }

    public function indexMilenia()
    {
        return view('pages.feat.sales.pending-sales-order.index-milenia');
    }

    public function getDataMilenia(Request $request)
    {
        $cursor = $request->input('cursor');
        $perPage = 10;
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()->toDateString()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth()->toDateString()))->endOfDay();

        $query = DB::connection('sqlsrv_wh')->table('SOSOH as s')
            ->leftJoin('SODOH as d', 's.SOSOH_OrderID', '=', 'd.SODOH_OrderID')
            ->leftJoin('MFCUS as c', 's.SOSOH_CustomerID', '=', 'c.MFCUS_CustomerID')
            ->select([
                's.SOSOH_OrderID',
                's.SOSOH_OrderDate',
                's.SOSOH_CustomerID',
                'c.MFCUS_Description',
            ])
            ->whereNull('d.SODOH_OrderID')
            ->whereBetween('s.SOSOH_OrderDate', [$startDate, $endDate])
            ->orderBy('s.SOSOH_OrderID', 'desc')
            ->orderBy('s.SOSOH_OrderDate', 'desc');

        $pendingOrders = $query->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

        $formattedData = $pendingOrders->getCollection()->map(function ($row) {
            return [
                'SOSOH_OrderID' => $row->SOSOH_OrderID,
                'SOSOH_OrderDate' => $row->SOSOH_OrderDate ? Carbon::parse($row->SOSOH_OrderDate)->format('d-m-Y') : '-',
                'CustomerName' => $row->MFCUS_Description ?? $row->SOSOH_CustomerID,
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'next_cursor' => $pendingOrders->nextCursor()?->encode(),
            'prev_cursor' => $pendingOrders->previousCursor()?->encode(),
        ]);
    }

    public function showMilenia($id)
    {
        $soHeader = DB::connection('sqlsrv_wh')->table('SOSOH as s')
            ->leftJoin('MFCUS as c', 's.SOSOH_CustomerID', '=', 'c.MFCUS_CustomerID')
            ->leftJoin('MFSSM as m', 's.SOSOH_SalesmanID', '=', 'm.MFSSM_SalesmanID')
            ->select('s.*', 'c.MFCUS_Description as customer_name', 'm.MFSSM_Description as salesman_name')
            ->where('s.SOSOH_OrderID', $id)
            ->first();

        if (! $soHeader) {
            abort(404);
        }

        $soDetails = DB::connection('sqlsrv_wh')->table('SOSOD as s')
            ->join('MFIMA as m', 'SOSOD_ItemID', '=', 'm.MFIMA_ItemID')
            ->select('s.*', 'm.MFIMA_Description as item_name')
            ->where('SOSOD_OrderID', $id)
            ->get();

        return view('pages.feat.sales.pending-sales-order.show-milenia', compact('soHeader', 'soDetails'));
    }

    public function indexMileniaBranch()
    {
        return view('pages.feat.sales.pending-sales-order.index-milenia-branch');
    }

    public function getDataMileniaBranch(Request $request)
    {
        $cursor = $request->input('cursor');
        $perPage = 10;
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()->toDateString()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth()->toDateString()))->endOfDay();

        $query = DB::connection('sqlsrv_rd')->table('SOSOH as s')
            ->leftJoin('SODOH as d', 's.SOSOH_OrderID', '=', 'd.SODOH_OrderID')
            ->leftJoin('MFCUS as c', 's.SOSOH_CustomerID', '=', 'c.MFCUS_CustomerID')
            ->select([
                's.SOSOH_OrderID',
                's.SOSOH_OrderDate',
                's.SOSOH_CustomerID',
                'c.MFCUS_Description',
            ])
            ->whereNull('d.SODOH_OrderID')
            ->whereBetween('s.SOSOH_OrderDate', [$startDate, $endDate])
            ->orderBy('s.SOSOH_OrderID', 'desc')
            ->orderBy('s.SOSOH_OrderDate', 'desc');

        $pendingOrders = $query->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

        $formattedData = $pendingOrders->getCollection()->map(function ($row) {
            return [
                'SOSOH_OrderID' => $row->SOSOH_OrderID,
                'SOSOH_OrderDate' => $row->SOSOH_OrderDate ? Carbon::parse($row->SOSOH_OrderDate)->format('d-m-Y') : '-',
                'CustomerName' => $row->MFCUS_Description ?? $row->SOSOH_CustomerID,
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'next_cursor' => $pendingOrders->nextCursor()?->encode(),
            'prev_cursor' => $pendingOrders->previousCursor()?->encode(),
        ]);
    }

    public function showMileniaBranch($id)
    {
        $soHeader = DB::connection('sqlsrv_rd')->table('SOSOH as s')
            ->leftJoin('MFCUS as c', 's.SOSOH_CustomerID', '=', 'c.MFCUS_CustomerID')
            ->leftJoin('MFSSM as m', 's.SOSOH_SalesmanID', '=', 'm.MFSSM_SalesmanID')
            ->select('s.*', 'c.MFCUS_Description as customer_name', 'm.MFSSM_Description as salesman_name')
            ->where('s.SOSOH_OrderID', $id)
            ->first();

        if (!$soHeader) {
            abort(404);
        }

        $soDetails = DB::connection('sqlsrv_rd')->table('SOSOD as s')
            ->join('MFIMA as m', 's.SOSOD_ItemID', '=', 'm.MFIMA_ItemID')
            ->select('s.*', 'm.MFIMA_Description as item_name')
            ->where('s.SOSOD_OrderID', $id)
            ->get();

        return view('pages.feat.sales.pending-sales-order.show-milenia-branch', compact('soHeader', 'soDetails'));
    }

    public function indexMap()
    {
        return view('pages.feat.sales.pending-sales-order.index-map');
    }

    public function getDataMap(Request $request)
    {
        $cursor = $request->input('cursor');
        $perPage = 10;
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()->toDateString()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth()->toDateString()))->endOfDay();

        $query = DB::connection('sqlsrv_snx')->table('SOSOH as s')
            ->leftJoin('SODOH as d', 's.SOSOH_OrderID', '=', 'd.SODOH_OrderID')
            ->leftJoin('MFCUS as c', 's.SOSOH_CustomerID', '=', 'c.MFCUS_CustomerID')
            ->select([
                's.SOSOH_OrderID',
                's.SOSOH_OrderDate',
                's.SOSOH_CustomerID',
                'c.MFCUS_Description',
            ])
            ->whereNull('d.SODOH_OrderID')
            ->whereBetween('s.SOSOH_OrderDate', [$startDate, $endDate])
            ->orderBy('s.SOSOH_OrderID', 'desc')
            ->orderBy('s.SOSOH_OrderDate', 'desc');

        $pendingOrders = $query->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

        $formattedData = $pendingOrders->getCollection()->map(function ($row) {
            return [
                'SOSOH_OrderID' => $row->SOSOH_OrderID,
                'SOSOH_OrderDate' => $row->SOSOH_OrderDate ? Carbon::parse($row->SOSOH_OrderDate)->format('d-m-Y') : '-',
                'CustomerName' => $row->MFCUS_Description ?? $row->SOSOH_CustomerID,
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'next_cursor' => $pendingOrders->nextCursor()?->encode(),
            'prev_cursor' => $pendingOrders->previousCursor()?->encode(),
        ]);
    }

    public function showMap($id)
    {
        $soHeader = DB::connection('sqlsrv_snx')->table('SOSOH as s')
            ->leftJoin('MFCUS as c', 's.SOSOH_CustomerID', '=', 'c.MFCUS_CustomerID')
            ->leftJoin('MFSSM as m', 's.SOSOH_SalesmanID', '=', 'm.MFSSM_SalesmanID')
            ->select('s.*', 'c.MFCUS_Description as customer_name', 'm.MFSSM_Description as salesman_name')
            ->where('s.SOSOH_OrderID', $id)
            ->first();

        if (!$soHeader) {
            abort(404);
        }

        $soDetails = DB::connection('sqlsrv_snx')->table('SOSOD as s')
            ->join('MFIMA as m', 's.SOSOD_ItemID', '=', 'm.MFIMA_ItemID')
            ->select('s.*', 'm.MFIMA_Description as item_name')
            ->where('s.SOSOD_OrderID', $id)
            ->get();

        return view('pages.feat.sales.pending-sales-order.show-map', compact('soHeader', 'soDetails'));
    }
}
