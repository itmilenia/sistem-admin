<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SalesOrderDetailMileniaBranch extends Model
{
    protected $table = 'SOIVD_Cabang';
    protected $connection = 'sqlsrv_wh';
    protected $primaryKey = 'SOIVD_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function salesManMileniaBranch()
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
                        SUM(SOIVD_Cabang.SOIVD_LineInvoiceAmount) AS GrossSales
                    FROM
                        SOIVD_Cabang
                    INNER JOIN MFIMA ON SOIVD_Cabang.SOIVD_ItemID = MFIMA.MFIMA_ItemID
                    INNER JOIN MFIB ON MFIMA.MFIMA_Brand = MFIB.MFIB_BrandID
                    -- Optional: Join ke SOIVH_Cabang jika ingin memfilter berdasarkan Tanggal Invoice Header
                    -- INNER JOIN SOIVH_Cabang ON SOIVD_Cabang.SOIVD_InvoiceID = SOIVH_Cabang.SOIVH_InvoiceID
                    WHERE
                        SOIVD_Cabang.SOIVD_OrderDate BETWEEN ? AND ?
                    GROUP BY
                        MFIB.MFIB_BrandID, MFIB.MFIB_Description
                ) AS SalesTable
            FULL OUTER JOIN
                (
                    SELECT
                        MFIB.MFIB_BrandID,
                        SUM(SOORD_Cabang.SOORD_LineReturnAmount - SOORD_Cabang.SOORD_TaxAmount) AS TotalReturn
                    FROM
                        SOORD_Cabang
                    INNER JOIN SOORH_Cabang ON SOORD_Cabang.SOORD_ReturnID = SOORH_Cabang.SOORH_ReturnID
                    INNER JOIN MFIMA ON SOORD_Cabang.SOORD_ItemID = MFIMA.MFIMA_ItemID
                    INNER JOIN MFIB ON MFIMA.MFIMA_Brand = MFIB.MFIB_BrandID
                    WHERE
                        SOORH_Cabang.SOORH_ReturnDate BETWEEN ? AND ?
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
}
