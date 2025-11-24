<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SalesOrderDetailMapBranch extends Model
{
    protected $table = 'SOIVD_Cabang';
    protected $connection = 'sqlsrv_snx';
    protected $primaryKey = 'SOIVD_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function salesManMapBranch()
    {
        return $this->belongsTo(SalesMap::class, 'SOIVD_SalesmanID', 'MFSSM_SalesmanID');
    }

    public static function getBrandPerformanceDashboard($startDateTime, $endDateTime)
    {
        $sql = "
            SELECT
                S.MFIB_BrandID,
                S.brand_name,
                S.sale_amount,
                ISNULL(R.retur_amount, 0) AS retur_amount,
                (S.sale_amount - ISNULL(R.retur_amount, 0)) AS net_amount
            FROM
                (
                    -- 1. BASE: SALES DATA
                    -- Ambil semua penjualan yang invoicenya terjadi di range tanggal ini
                    SELECT
                        MFIB.MFIB_BrandID,
                        MAX(MFIB.MFIB_Description) as brand_name, -- Pakai MAX biar group by aman
                        SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) AS sale_amount
                    FROM
                        SOIVD_Cabang
                    INNER JOIN SOIVH_Cabang ON SOIVD_Cabang.SOIVD_InvoiceID = SOIVH_Cabang.SOIVH_InvoiceID
                    INNER JOIN MFIMA ON SOIVD_Cabang.SOIVD_ItemID = MFIMA.MFIMA_ItemID
                    INNER JOIN MFIB ON MFIMA.MFIMA_Brand = MFIB.MFIB_BrandID
                    WHERE
                        SOIVH_Cabang.SOIVH_InvoiceDate BETWEEN ? AND ?
                    GROUP BY
                        MFIB.MFIB_BrandID
                ) AS S
            LEFT JOIN
                (
                    -- 2. LINKED: RETURN DATA (Clawback Logic)
                    -- Cari retur yang memiliki InvoiceID dari range tanggal yang sama
                    SELECT
                        MFIB.MFIB_BrandID,
                        -- Rumus: IncTax - Tax = Net
                        SUM(SOORD_Cabang.SOORD_LineReturnAmount - ISNULL(SOORD_Cabang.SOORD_TaxAmount, 0)) AS retur_amount
                    FROM
                        SOORD_Cabang
                    -- PENTING: Join ke SOIVH_Cabang menggunakan SOORD_InvoiceID
                    INNER JOIN SOIVH_Cabang ON SOORD_Cabang.SOORD_InvoiceID = SOIVH_Cabang.SOIVH_InvoiceID
                    INNER JOIN MFIMA ON SOORD_Cabang.SOORD_ItemID = MFIMA.MFIMA_ItemID
                    INNER JOIN MFIB ON MFIMA.MFIMA_Brand = MFIB.MFIB_BrandID
                    WHERE
                        -- Filter Tanggal INVOICE (bukan tanggal retur)
                        SOIVH_Cabang.SOIVH_InvoiceDate BETWEEN ? AND ?
                    GROUP BY
                        MFIB.MFIB_BrandID
                ) AS R ON S.MFIB_BrandID = R.MFIB_BrandID
            ORDER BY
                (S.sale_amount - ISNULL(R.retur_amount, 0)) DESC
        ";

        return DB::connection('sqlsrv_snx')->select($sql, [
            $startDateTime,
            $endDateTime,
            $startDateTime,
            $endDateTime
        ]);
    }
}
