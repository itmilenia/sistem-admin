<?php

namespace App\Http\Controllers\Feat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function landing()
    {
        return view('pages.feat.customer.customer-data.landing');
    }

    public function indexMilenia()
    {
        $customers = DB::connection('sqlsrv_wh')->table('MFCUS')
            ->select(
                'MFCUS_CustomerID',
                'MFCUS_Description',
                'MFCUS_Contact',
                'MFCUS_Telephone',
                'MFCUS_Mobilephone',
                'MFCUS_Active'
            )
            ->orderBy('MFCUS_Active', 'desc')       // aktif duluan
            ->orderBy('MFCUS_Description', 'asc')   // baru urut nama
            ->get();

        return view('pages.feat.customer.customer-data.index-milenia', compact('customers'));
    }

    public function indexMega()
    {
        $customers = DB::connection('sqlsrv_snx')->table('MFCUS')
            ->select(
                'MFCUS_CustomerID',
                'MFCUS_Description',
                'MFCUS_Contact',
                'MFCUS_Telephone',
                'MFCUS_Mobilephone',
                'MFCUS_Active'
            )
            ->orderBy('MFCUS_Active', 'desc')       // aktif duluan
            ->orderBy('MFCUS_Description', 'asc')   // baru urut nama
            ->get();

        return view('pages.feat.customer.customer-data.index-mega', compact('customers'));
    }

    public function showMilenia($id)
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
                'MFCUS_LASTBUY',
                'MFCUS_Active'
            )
            ->leftJoin('MFSRM', 'MFCUS.MFCUS_RegionID', '=', 'MFSRM.MFSRM_RegionalID')
            ->leftJoin('MFSSM', 'MFCUS.MFCUS_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
            ->where('MFCUS_CustomerID', $id)
            ->first();

        if (!$customer) {
            abort(404);
        }

        return view('pages.feat.customer.customer-data.show-milenia', compact('customer'));
    }

    public function showMega($id)
    {
        $customer = DB::connection('sqlsrv_snx')->table('MFCUS')
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
                'MFCUS_LASTBUY',
                'MFCUS_Active'
            )
            ->leftJoin('MFSRM', 'MFCUS.MFCUS_RegionID', '=', 'MFSRM.MFSRM_RegionalID')
            ->leftJoin('MFSSM', 'MFCUS.MFCUS_SalesmanID', '=', 'MFSSM.MFSSM_SalesmanID')
            ->where('MFCUS_CustomerID', $id)
            ->first();

        if (!$customer) {
            abort(404);
        }

        return view('pages.feat.customer.customer-data.show-mega', compact('customer'));
    }
}
