<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesByBrandExportMap implements FromView, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $salesmanId;
    protected $salespersonName;
    protected $description;
    protected $brandId;

    public function __construct($salesmanId, $startDate, $endDate, $salespersonName, $description, $brandId = null)
    {
        $this->salesmanId = $salesmanId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->salespersonName = $salespersonName;
        $this->description = $description;
        $this->brandId = $brandId;
    }

    public function view(): View
    {
        $this->startDate = Carbon::parse($this->startDate)->startOfDay()->format('Y-m-d H:i:s');
        $this->endDate   = Carbon::parse($this->endDate)->endOfDay()->format('Y-m-d H:i:s');

        // 1. Query untuk mengambil data yang sudah diagregasi
        $salesData = DB::connection('sqlsrv_snx')
            ->table('SOIVD')
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->join('MFIMA', 'SOIVD.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
            ->where('SOIVD.SOIVD_SalesmanID', $this->salesmanId)
            ->whereBetween('SOIVH.SOIVH_InvoiceDate', [$this->startDate, $this->endDate]);

        if ($this->brandId) {
            $salesData->where('MFIMA.MFIMA_Brand', $this->brandId);
        }

        $salesData = $salesData->select(
            'MFIB.MFIB_Description as brand_name',
            DB::raw('YEAR(SOIVH.SOIVH_InvoiceDate) as year'),
            DB::raw('MONTH(SOIVH.SOIVH_InvoiceDate) as month'),
            DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_sales')
        )
            ->groupBy(
                'MFIB.MFIB_Description',
                DB::raw('YEAR(SOIVH.SOIVH_InvoiceDate)'),
                DB::raw('MONTH(SOIVH.SOIVH_InvoiceDate)')
            )
            ->orderBy('brand_name')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // 2. Proses data menjadi format pivot
        $pivotData = [];
        $months = [];

        foreach ($salesData as $data) {
            // Inisialisasi brand jika belum ada
            if (!isset($pivotData[$data->brand_name])) {
                $pivotData[$data->brand_name] = [];
            }
            // Inisialisasi tahun jika belum ada
            if (!isset($pivotData[$data->brand_name][$data->year])) {
                $pivotData[$data->brand_name][$data->year] = [];
            }
            // Simpan total sales per bulan
            $pivotData[$data->brand_name][$data->year][$data->month] = $data->total_sales;

            // Kumpulkan semua bulan yang ada datanya
            if (!in_array($data->month, $months)) {
                $months[] = $data->month;
            }
        }

        // Urutkan bulan
        sort($months);

        // 3. Kirim data yang sudah diproses ke Blade View
        return view('exports.sales_report', [
            'pivotData' => $pivotData,
            'months' => $months,
            'description' => $this->description,
            'salespersonName' => $this->salespersonName,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }
}
