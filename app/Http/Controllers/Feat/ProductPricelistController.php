<?php

namespace App\Http\Controllers\Feat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductPricelistController extends Controller
{
    public function landing()
    {
        return view('pages.feat.promo-produk.produk-pricelist.landing');
    }

    public function indexMilenia()
    {
        $pricelists = DB::connection('sqlsrv_wh')
            ->table('SOMPD')
            ->join('MFIMA', 'SOMPD.SOMPD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->select(
                'SOMPD.SOMPD_PriceID',
                'SOMPD.SOMPD_CurrencyID',
                'SOMPD.SOMPD_CusClass',
                'SOMPD.SOMPD_ItemID',
                'SOMPD.SOMPD_ItemDesc',
                'SOMPD.SOMPD_PriceAmount',
                'SOMPD.SOMPD_UPDATE'
            )
            ->where('MFIMA.MFIMA_Active', 1)
            ->orderBy('SOMPD.SOMPD_LineItem', 'asc')
            ->get();

        return view('pages.feat.promo-produk.produk-pricelist.milenia.index', [
            'pricelists' => $pricelists,
        ]);
    }


    public function indexMap()
    {
        $pricelists = DB::connection('sqlsrv_wh')
            ->table('SOMPD')
            ->join('MFIMA', 'SOMPD.SOMPD_ItemID', '=', 'MFIMA.MFIMA_ItemID')
            ->select(
                'SOMPD.SOMPD_PriceID',
                'SOMPD.SOMPD_CurrencyID',
                'SOMPD.SOMPD_CusClass',
                'SOMPD.SOMPD_ItemID',
                'SOMPD.SOMPD_ItemDesc',
                'SOMPD.SOMPD_PriceAmount',
                'SOMPD.SOMPD_UPDATE'
            )
            ->where('MFIMA.MFIMA_Active', 1)
            ->orderBy('SOMPD.SOMPD_LineItem', 'asc')
            ->get();

        return view('pages.feat.promo-produk.produk-pricelist.map.index', [
            'pricelists' => $pricelists
        ]);
    }
}
