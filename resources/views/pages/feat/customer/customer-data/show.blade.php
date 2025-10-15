@extends('layouts.app')

@section('title', 'Detail Customer')

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Customer</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer-data.index') }}">Data Customer</a></li>
                <li class="breadcrumb-item">Detail</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-user me-2"></i>Informasi Detail Customer</h5>
                        <a href="{{ route('customer-data.index') }}" class="btn btn-primary">
                            <i class="feather-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">ID Customer</dt>
                            <dd class="col-sm-9">{{ $customer->MFCUS_CustomerID }}</dd>

                            <dt class="col-sm-3">Nama Customer</dt>
                            <dd class="col-sm-9">{{ $customer->MFCUS_Description }}</dd>

                            <dt class="col-sm-3">Kontak Person</dt>
                            <dd class="col-sm-9">{{ $customer->MFCUS_Contact ?? '-' }}</dd>

                            <dt class="col-sm-3">Alamat</dt>
                            <dd class="col-sm-9">{{ trim($customer->MFCUS_Address1 . ' ' . $customer->MFCUS_Address2) }}
                            </dd>

                            <dt class="col-sm-3">Region</dt>
                            <dd class="col-sm-9">{{ $customer->region_name ?? '-' }}</dd>

                            <dt class="col-sm-3">Telepon</dt>
                            <dd class="col-sm-9">{{ $customer->MFCUS_Telephone ?? '-' }}</dd>

                            <dt class="col-sm-3">No. HP</dt>
                            <dd class="col-sm-9">{{ $customer->MFCUS_Mobilephone ?? '-' }}</dd>

                            <dt class="col-sm-3">Nama Sales</dt>
                            <dd class="col-sm-9">{{ $customer->salesman_name ?? '-' }}</dd>

                            <dt class="col-sm-3">Transaksi Terakhir</dt>
                            <dd class="col-sm-9">
                                {{ $customer->MFCUS_LASTBUY ? \Carbon\Carbon::parse($customer->MFCUS_LASTBUY)->translatedFormat('d F Y') : '-' }}
                            </dd>

                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
