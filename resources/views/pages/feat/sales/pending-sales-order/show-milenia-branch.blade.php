@extends('layouts.app')

@section('title', 'Detail Pending SO Milenia Cabang')

@push('styles')
    <style>
        .invoice-details-card dt {
            font-weight: 500;
            color: #555;
        }

        .invoice-details-card dd {
            margin-bottom: 0.75rem;
        }

        .invoice-summary table {
            width: 100%;
        }

        .invoice-summary td {
            padding: 0.5rem 0;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('pending-so.landing') }}">Pending Sales Order</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pending-so.milenia-branch.index') }}">Pending Sales Order
                        Milenia
                        Cabang</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        {{-- Tombol Kembali ke Halaman Index --}}
        <div class="page-header-right ms-auto">
            <a href="{{ route('pending-so.milenia-branch.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            {{-- Bagian Informasi Header Order --}}
            <div class="col-12">
                <div class="card stretch-full invoice-details-card">
                    <div class="card-header">
                        <h5 class="card-title">Informasi Sales Order</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">No. Order (SO)</dt>
                                    <dd class="col-sm-8">: {{ $soHeader->SOSOH_OrderID }}</dd>

                                    <dt class="col-sm-4">Nama Customer</dt>
                                    <dd class="col-sm-8">: {{ $soHeader->customer_name }}</dd>

                                    <dt class="col-sm-4">Tanggal Order</dt>
                                    <dd class="col-sm-8">:
                                        {{ \Carbon\Carbon::parse($soHeader->SOSOH_OrderDate)->translatedFormat('d F Y') }}
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Mata Uang</dt>
                                    <dd class="col-sm-8">: {{ $soHeader->SOSOH_CurrencyID }}</dd>

                                    <dt class="col-sm-4">Nama Salesman</dt>
                                    <dd class="col-sm-8">: {{ $soHeader->salesman_name }}</dd>

                                    <dt class="col-sm-4">User Input</dt>
                                    <dd class="col-sm-8">: {{ $soHeader->SOSOH_UserID }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Detail Barang --}}
            <div class="col-12">
                <div class="card stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Rincian Barang</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center align-middle" style="width: 5%;">#</th>
                                        <th class="text-center align-middle">Kode Barang</th>
                                        <th class="text-center align-middle">Nama Barang</th>
                                        <th class="text-center align-middle">Qty</th>
                                        <th class="text-center align-middle">Satuan</th>
                                        <th class="text-center align-middle">Harga Satuan</th>
                                        <th class="text-center align-middle">Diskon</th>
                                        <th class="text-center align-middle">Jumlah Harga <br> (Setelah Diskon)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($soDetails as $detail)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $detail->SOSOD_ItemID }}</td>
                                            <td>{{ $detail->item_name ?? '-' }}
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($detail->SOSOD_OrderQty ?? 0) }}</td>
                                            <td class="text-center">{{ $detail->SOSOD_UM }}
                                            </td>
                                            <td class="text-end">Rp
                                                {{ number_format($detail->SOSOD_OrgPieceOrderAmount) }}
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($detail->SOSOD_DiscMarkPersen ?? 0) }}%
                                            </td>
                                            <td class="text-end">Rp
                                                {{ number_format($detail->SOSOD_LineOrderAmount ?? 0) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada rincian barang untuk transaksi
                                                ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Ringkasan Total --}}
            <div class="col-md-6 ms-auto">
                <div class="card stretch-full">
                    <div class="card-body invoice-summary">
                        <h5 class="card-title mb-3">Ringkasan Total</h5>
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">Rp
                                        {{ number_format($soHeader->SOSOH_OrderAmountGross ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Diskon</td>
                                    <td class="text-end">- Rp
                                        {{ number_format($soHeader->SOSOH_DiscAmount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Pajak</td>
                                    <td class="text-end">+ Rp
                                        {{ number_format($soHeader->SOSOH_TaxAmount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold fs-5">Grand Total</td>
                                    <td class="text-end fw-bold fs-5">Rp
                                        {{ number_format($soHeader->SOSOH_OrderAmount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
