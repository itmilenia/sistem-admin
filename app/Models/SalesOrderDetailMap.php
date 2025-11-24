<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SalesOrderDetailMap extends Model
{
    protected $table = 'SOIVD';
    protected $connection = 'sqlsrv_snx';
    protected $primaryKey = 'SOIVD_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function salesManMap()
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
                        SUM(SOIVD.SOIVD_LineInvoiceAmount) AS sale_amount
                    FROM
                        SOIVD
                    INNER JOIN SOIVH ON SOIVD.SOIVD_InvoiceID = SOIVH.SOIVH_InvoiceID
                    INNER JOIN MFIMA ON SOIVD.SOIVD_ItemID = MFIMA.MFIMA_ItemID
                    INNER JOIN MFIB ON MFIMA.MFIMA_Brand = MFIB.MFIB_BrandID
                    WHERE
                        SOIVH.SOIVH_InvoiceDate BETWEEN ? AND ?
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
                        SUM(SOORD.SOORD_LineReturnAmount - ISNULL(SOORD.SOORD_TaxAmount, 0)) AS retur_amount
                    FROM
                        SOORD
                    -- PENTING: Join ke SOIVH menggunakan SOORD_InvoiceID
                    INNER JOIN SOIVH ON SOORD.SOORD_InvoiceID = SOIVH.SOIVH_InvoiceID
                    INNER JOIN MFIMA ON SOORD.SOORD_ItemID = MFIMA.MFIMA_ItemID
                    INNER JOIN MFIB ON MFIMA.MFIMA_Brand = MFIB.MFIB_BrandID
                    WHERE
                        -- Filter Tanggal INVOICE (bukan tanggal retur)
                        SOIVH.SOIVH_InvoiceDate BETWEEN ? AND ?
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

    public static function getCustomerPerformanceDashboard($startDateTime, $endDateTime)
    {
        $sql = "
            WITH
            -- 1. CTE SALES (GABUNGAN PUSAT & CABANG)
            AllSalesRaw AS (
                -- A. SALES PUSAT
                SELECT
                    SOIVH.SOIVH_CustomerID,
                    SUM(SOIVD.SOIVD_LineInvoiceAmount) AS SalesAmount
                FROM SOIVD
                INNER JOIN SOIVH ON SOIVD.SOIVD_InvoiceID = SOIVH.SOIVH_InvoiceID
                WHERE SOIVH.SOIVH_InvoiceDate BETWEEN ? AND ?
                GROUP BY SOIVH.SOIVH_CustomerID

                UNION ALL

                -- B. SALES CABANG
                SELECT
                    SOIVH_Cabang.SOIVH_CustomerID,
                    SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) AS SalesAmount
                FROM SOIVD_Cabang
                INNER JOIN SOIVH_Cabang ON SOIVD_Cabang.SOIVD_InvoiceID = SOIVH_Cabang.SOIVH_InvoiceID
                WHERE SOIVH_Cabang.SOIVH_InvoiceDate BETWEEN ? AND ?
                GROUP BY SOIVH_Cabang.SOIVH_CustomerID
            ),

            -- 2. CTE RETURNS (GABUNGAN PUSAT & CABANG)
            -- Mengambil Nominal dari SOORH (Header), tapi Filter via SOORD -> SOIVH (Clawback)
            AllReturnsRaw AS (
                -- A. RETUR PUSAT
                SELECT
                    R.SOORH_CustomerID,
                    SUM(R.NetReturnAmount) AS ReturnAmount
                FROM (
                    -- Subquery: Ambil DISTINCT ReturnID yang valid sesuai Invoice Date
                    -- Agar nominal Header tidak terhitung berkali-kali jika barangnya banyak
                    SELECT DISTINCT
                        SOORH.SOORH_ReturnID,
                        SOORH.SOORH_CustomerID,
                        (SOORH.SOORH_ReturnAmount - ISNULL(SOORH.SOORH_TaxAmount, 0)) AS NetReturnAmount
                    FROM SOORH
                    INNER JOIN SOORD ON SOORH.SOORH_ReturnID = SOORD.SOORD_ReturnID
                    INNER JOIN SOIVH ON SOORD.SOORD_InvoiceID = SOIVH.SOIVH_InvoiceID
                    WHERE
                        -- LOGIKA CLAWBACK: Filter Tanggal Invoice
                        SOIVH.SOIVH_InvoiceDate BETWEEN ? AND ?
                ) AS R
                GROUP BY R.SOORH_CustomerID

                UNION ALL

                -- B. RETUR CABANG
                SELECT
                    R_Cab.SOORH_CustomerID,
                    SUM(R_Cab.NetReturnAmount) AS ReturnAmount
                FROM (
                    SELECT DISTINCT
                        SOORH_Cabang.SOORH_ReturnID,
                        SOORH_Cabang.SOORH_CustomerID,
                        (SOORH_Cabang.SOORH_ReturnAmount - ISNULL(SOORH_Cabang.SOORH_TaxAmount, 0)) AS NetReturnAmount
                    FROM SOORH_Cabang
                    INNER JOIN SOORD_Cabang ON SOORH_Cabang.SOORH_ReturnID = SOORD_Cabang.SOORD_ReturnID
                    INNER JOIN SOIVH_Cabang ON SOORD_Cabang.SOORD_InvoiceID = SOIVH_Cabang.SOIVH_InvoiceID
                    WHERE
                        SOIVH_Cabang.SOIVH_InvoiceDate BETWEEN ? AND ?
                ) AS R_Cab
                GROUP BY R_Cab.SOORH_CustomerID
            ),

            -- 3. GROUPING FINAL
            FinalSales AS (
                SELECT SOIVH_CustomerID, SUM(SalesAmount) as TotalSales
                FROM AllSalesRaw GROUP BY SOIVH_CustomerID
            ),
            FinalReturns AS (
                SELECT SOORH_CustomerID, SUM(ReturnAmount) as TotalReturn
                FROM AllReturnsRaw GROUP BY SOORH_CustomerID
            )

            -- 4. FINAL SELECT
            SELECT
                S.SOIVH_CustomerID AS MFCUS_CustomerID,
                ISNULL(M.MFCUS_Description, 'Unknown Customer') AS customer_name,
                ISNULL(S.TotalSales, 0) AS sale_amount,
                ISNULL(R.TotalReturn, 0) AS retur_amount,
                (ISNULL(S.TotalSales, 0) - ISNULL(R.TotalReturn, 0)) AS net_amount
            FROM FinalSales S
            -- LEFT JOIN: Basis data adalah Penjualan Bulan Tersebut
            LEFT JOIN FinalReturns R ON S.SOIVH_CustomerID = R.SOORH_CustomerID
            LEFT JOIN MFCUS M ON S.SOIVH_CustomerID = M.MFCUS_CustomerID
            ORDER BY net_amount DESC
        ";

        return DB::connection('sqlsrv_snx')->select($sql, [
            $startDateTime,
            $endDateTime,
            $startDateTime,
            $endDateTime,
            $startDateTime,
            $endDateTime,
            $startDateTime,
            $endDateTime,
        ]);
    }
}
