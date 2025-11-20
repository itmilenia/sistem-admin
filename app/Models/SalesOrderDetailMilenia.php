<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SalesOrderDetailMilenia extends Model
{
    protected $table = 'SOIVD';
    protected $connection = 'sqlsrv_wh';
    protected $primaryKey = 'SOIVD_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function salesManMilenia()
    {
        return $this->belongsTo(SalesMilenia::class, 'SOIVD_SalesmanID', 'MFSSM_SalesmanID');
    }

    public static function getBrandPerformanceDashboard($startDateTime, $endDateTime)
    {
        $sql = "
            SELECT
                COALESCE(SalesTable.MFIB_BrandID, ReturnTable.MFIB_BrandID) AS MFIB_BrandID,
                COALESCE(SalesTable.MFIB_Description, (SELECT TOP 1 MFIB_Description FROM MFIB WHERE MFIB_BrandID = ReturnTable.MFIB_BrandID)) AS brand_name,
                ISNULL(SalesTable.GrossSales, 0) AS sale_amount,
                ISNULL(ReturnTable.TotalReturn, 0) AS retur_amount,
                ISNULL(SalesTable.GrossSales, 0) - ISNULL(ReturnTable.TotalReturn, 0) AS net_amount
            FROM
                (
                    SELECT
                        MFIB.MFIB_BrandID,
                        MFIB.MFIB_Description,
                        SUM(SOIVD.SOIVD_LineInvoiceAmount) AS GrossSales
                    FROM
                        SOIVD
                    INNER JOIN MFIMA ON SOIVD.SOIVD_ItemID = MFIMA.MFIMA_ItemID
                    INNER JOIN MFIB ON MFIMA.MFIMA_Brand = MFIB.MFIB_BrandID
                    -- Optional: Join ke SOIVH jika ingin memfilter berdasarkan Tanggal Invoice Header
                    -- INNER JOIN SOIVH ON SOIVD.SOIVD_InvoiceID = SOIVH.SOIVH_InvoiceID
                    WHERE
                        SOIVD.SOIVD_OrderDate BETWEEN ? AND ?
                    GROUP BY
                        MFIB.MFIB_BrandID, MFIB.MFIB_Description
                ) AS SalesTable
            FULL OUTER JOIN
                (
                    SELECT
                        MFIB.MFIB_BrandID,
                        SUM(SOORD.SOORD_LineReturnAmount - SOORD.SOORD_TaxAmount) AS TotalReturn
                    FROM
                        SOORD
                    INNER JOIN SOORH ON SOORD.SOORD_ReturnID = SOORH.SOORH_ReturnID
                    INNER JOIN MFIMA ON SOORD.SOORD_ItemID = MFIMA.MFIMA_ItemID
                    INNER JOIN MFIB ON MFIMA.MFIMA_Brand = MFIB.MFIB_BrandID
                    WHERE
                        SOORH.SOORH_ReturnDate BETWEEN ? AND ?
                    GROUP BY
                        MFIB.MFIB_BrandID
                ) AS ReturnTable ON SalesTable.MFIB_BrandID = ReturnTable.MFIB_BrandID
            ORDER BY
                net_amount DESC
        ";

        return DB::connection('sqlsrv_wh')->select($sql, [
            $startDateTime,
            $endDateTime,
            $startDateTime,
            $endDateTime,
        ]);
    }

    public static function getCustomerPerformanceDashboard($startDateTime, $endDateTime)
    {
        $sql = "
                WITH
                    AllSales AS (
                        -- 1. SALES PUSAT (MAIN)
                        SELECT
                            SOIVH.SOIVH_CustomerID,
                            SUM(SOIVD.SOIVD_LineInvoiceAmount) AS SalesAmount
                        FROM SOIVD
                        INNER JOIN SOIVH ON SOIVD.SOIVD_InvoiceID = SOIVH.SOIVH_InvoiceID
                        WHERE
                            SOIVD.SOIVD_OrderDate BETWEEN ? AND ?
                        GROUP BY SOIVH.SOIVH_CustomerID

                        UNION ALL

                        -- 2. SALES CABANG (BRANCH)
                        SELECT
                            SOIVH_Cabang.SOIVH_CustomerID,
                            SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) AS SalesAmount
                        FROM SOIVD_Cabang
                        INNER JOIN SOIVH_Cabang ON SOIVD_Cabang.SOIVD_InvoiceID = SOIVH_Cabang.SOIVH_InvoiceID
                        WHERE
                            SOIVD_Cabang.SOIVD_OrderDate BETWEEN ? AND ?
                        GROUP BY SOIVH_Cabang.SOIVH_CustomerID
                    ),

                    AllReturns AS (
                        -- RETURN tetap pakai ReturnDate (INI BENAR)
                        SELECT
                            SOIVH.SOIVH_CustomerID,
                            SUM(SOORD.SOORD_LineReturnAmount - SOORD.SOORD_TaxAmount) AS ReturnAmount
                        FROM SOORD
                        INNER JOIN SOIVH ON SOORD.SOORD_InvoiceID = SOIVH.SOIVH_InvoiceID
                        INNER JOIN SOORH ON SOORD.SOORD_ReturnID = SOORH.SOORH_ReturnID
                        WHERE
                            SOORH.SOORH_ReturnDate BETWEEN ? AND ?
                        GROUP BY SOIVH.SOIVH_CustomerID

                        UNION ALL

                        SELECT
                            SOIVH_Cabang.SOIVH_CustomerID,
                            SUM(SOORD_Cabang.SOORD_LineReturnAmount - SOORD_Cabang.SOORD_TaxAmount) AS ReturnAmount
                        FROM SOORD_Cabang
                        INNER JOIN SOIVH_Cabang ON SOORD_Cabang.SOORD_InvoiceID = SOIVH_Cabang.SOIVH_InvoiceID
                        INNER JOIN SOORH_Cabang ON SOORD_Cabang.SOORD_ReturnID = SOORH_Cabang.SOORH_ReturnID
                        WHERE
                            SOORH_Cabang.SOORH_ReturnDate BETWEEN ? AND ?
                        GROUP BY SOIVH_Cabang.SOIVH_CustomerID
                    ),

                    TotalSalesGrouped AS (
                        SELECT SOIVH_CustomerID, SUM(SalesAmount) as TotalSales
                        FROM AllSales GROUP BY SOIVH_CustomerID
                    ),

                    TotalReturnsGrouped AS (
                        SELECT SOIVH_CustomerID, SUM(ReturnAmount) as TotalReturn
                        FROM AllReturns GROUP BY SOIVH_CustomerID
                    )

                    SELECT
                        COALESCE(S.SOIVH_CustomerID, R.SOIVH_CustomerID) AS MFCUS_CustomerID,
                        MFCUS.MFCUS_Description AS customer_name,
                        ISNULL(S.TotalSales, 0) AS sale_amount,
                        ISNULL(R.TotalReturn, 0) AS retur_amount,
                        ISNULL(S.TotalSales, 0) - ISNULL(R.TotalReturn, 0) AS net_amount
                    FROM TotalSalesGrouped S
                    FULL OUTER JOIN TotalReturnsGrouped R
                        ON S.SOIVH_CustomerID = R.SOIVH_CustomerID
                    LEFT JOIN MFCUS
                        ON COALESCE(S.SOIVH_CustomerID, R.SOIVH_CustomerID) = MFCUS.MFCUS_CustomerID
                    ORDER BY net_amount DESC;
                ";

        return DB::connection('sqlsrv_wh')->select($sql, [
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
