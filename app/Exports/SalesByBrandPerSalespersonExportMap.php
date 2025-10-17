<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesByBrandPerSalespersonExportMap implements FromView, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $description;

    public function __construct($startDate, $endDate, $description)
    {
        $this->startDate   = $startDate;
        $this->endDate     = $endDate;
        $this->description = $description;
    }

    public function view(): View
    {
        $queryStartDate = Carbon::parse($this->startDate)->startOfDay()->format('Y-m-d H:i:s');
        $queryEndDate   = Carbon::parse($this->endDate)->endOfDay()->format('Y-m-d H:i:s');

        // 1. Query baru untuk mengambil data gabungan semua sales
        $salesData = DB::connection('sqlsrv_snx')
            ->table('SOIVD')
            ->join('SOIVH', 'SOIVD.SOIVD_InvoiceID', '=', 'SOIVH.SOIVH_InvoiceID')
            ->join('MFIMA', 'SOIVD.SOIVD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->join('MFIB', 'MFIMA.MFIMA_Brand', '=', 'MFIB.MFIB_BrandID')
            ->join('MFSSM', 'SOIVD.SOIVD_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID') // Join ke tabel sales
            ->where('MFSSM.MFSSM_Active', 1) // Hanya sales yang aktif
            ->whereBetween('SOIVH.SOIVH_InvoiceDate', [$queryStartDate, $queryEndDate])
            ->select(
                'MFSSM.MFSSM_Description as salesman_name', // Ambil nama sales
                'MFIB.MFIB_Description as brand_name',
                DB::raw('YEAR(SOIVH.SOIVH_InvoiceDate) as year'),
                DB::raw('SUM(SOIVD.SOIVD_LineInvoiceAmount) as total_sales')
            )
            ->groupBy(
                'MFSSM.MFSSM_Description',
                'MFIB.MFIB_Description',
                DB::raw('YEAR(SOIVH.SOIVH_InvoiceDate)')
            )
            ->orderBy('salesman_name')->orderBy('year')
            ->get();

        // 2. Proses data mentah menjadi format PIVOT
        $pivotData = [];
        $allBrands = [];

        foreach ($salesData as $data) {
            // Kumpulkan semua brand unik untuk dijadikan kolom header
            if (!in_array($data->brand_name, $allBrands)) {
                $allBrands[] = $data->brand_name;
            }

            // Susun data dalam array bersarang: Sales -> Tahun -> Brand -> Total
            $pivotData[$data->salesman_name][$data->year][$data->brand_name] = $data->total_sales;
        }

        // Urutkan nama brand secara alfabetis
        sort($allBrands);

        // 3. Kirim data yang sudah jadi ke template view baru
        return view('exports.all_sales_report', [
            'pivotData'   => $pivotData,
            'allBrands'   => $allBrands,
            'description' => $this->description,
            'startDate'   => $this->startDate,
            'endDate'     => $this->endDate,
        ]);
    }
}
