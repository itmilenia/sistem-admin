<?php

namespace App\Http\Controllers\Feat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = DB::connection('sqlsrv_wh')->table('MFCUS')
            ->select(
                'MFCUS_CustomerID',
                'MFCUS_Description',
                'MFCUS_Contact',
                'MFCUS_Telephone',
                'MFCUS_Mobilephone'
            )
            ->where('MFCUS_Active', 1)
            ->orderBy('MFCUS_Description', 'asc')
            ->get();

        return view('pages.feat.customer.customer-data.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = DB::connection('sqlsrv_wh')->table('MFCUS')
            ->select(
                'MFCUS_CustomerID',
                'MFCUS_Description',
                'MFCUS_Contact',
                'MFCUS_Address1',
                'MFCUS_Address2',
                'MFCUS_RegionID',
                'MFCUS_Telephone',
                'MFCUS_Mobilephone',
                'MFSRM.MFSRM_Description as region_name',
                'MFSSM.MFSSM_Description as salesman_name',
                'MFCUS_LASTBUY'
            )
            ->leftJoin('MFSRM', 'MFCUS.MFCUS_RegionID', '=', 'MFSRM.MFSRM_RegionalID')
            ->leftJoin('MFSSM', 'MFCUS.MFCUS_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
            ->where('MFCUS_Active', 1)
            ->where('MFCUS_CustomerID', $id)
            ->first();

        if (!$customer) {
            abort(404);
        }

        return view('pages.feat.customer.customer-data.show', compact('customer'));
    }
}
