@extends('layouts.app')

@section('title', 'Detail Transaksi Pembelian')

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
                <li class="breadcrumb-item"><a href="{{ route('customer-transaction.landing') }}">Customer</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer-transaction-map-branch.index') }}">Transaksi
                        Pembelian</a>
                </li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        {{-- Tombol Kembali ke Halaman Index --}}
        <div class="page-header-right ms-auto">
            <a href="{{ route('customer-transaction-map-branch.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            {{-- Bagian Informasi Header Invoice --}}
            <div class="col-12">
                <div class="card stretch-full invoice-details-card">
                    <div class="card-header">
                        <h5 class="card-title">Informasi Header Invoice</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">No. Invoice</dt>
                                    <dd class="col-sm-8">: {{ $invoiceHeader->SOIVH_InvoiceID }}</dd>

                                    <dt class="col-sm-4">Nama Customer</dt>
                                    <dd class="col-sm-8">: {{ $invoiceHeader->customer_name }}</dd>

                                    <dt class="col-sm-4">Tanggal Invoice</dt>
                                    <dd class="col-sm-8">:
                                        {{ \Carbon\Carbon::parse($invoiceHeader->SOIVH_InvoiceDate)->translatedFormat('d F Y') }}
                                    </dd>

                                    <dt class="col-sm-4">Jatuh Tempo</dt>
                                    <dd class="col-sm-8">:
                                        {{ \Carbon\Carbon::parse($invoiceHeader->SOIVH_DueDate)->translatedFormat('d F Y') }}
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Termin</dt>
                                    <dd class="col-sm-8">: {{ $invoiceHeader->terms_description ?? '-' }}</dd>

                                    <dt class="col-sm-4">Mata Uang</dt>
                                    <dd class="col-sm-8">: {{ $invoiceHeader->SOIVH_CurrencyID }}</dd>

                                    <dt class="col-sm-4">Jenis Pajak</dt>
                                    <dd class="col-sm-8">: {{ $invoiceHeader->SOIVH_TaxID }}</dd>

                                    <dt class="col-sm-4">User Input</dt>
                                    <dd class="col-sm-8">: {{ $invoiceHeader->SOIVH_UserID }}</dd>
                                </dl>
                            </div>
                            @if ($invoiceHeader->SOIVH_Note)
                                <div class="col-12 mt-2">
                                    <hr>
                                    <p><strong>Catatan:</strong></p>
                                    <p>{{ $invoiceHeader->SOIVH_Note }}</p>
                                </div>
                            @endif
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
                                        <th class="text-center align-middle">Nama Barang</th>
                                        <th class="text-center align-middle">Sales</th>
                                        <th class="text-center align-middle">Qty</th>
                                        <th class="text-center align-middle">Satuan</th>
                                        <th class="text-center align-middle">Harga Satuan</th>
                                        <th class="text-center align-middle">Diskon</th>
                                        <th class="text-center align-middle">Jumlah Harga <br> (Setelah Diskon)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($invoiceDetails as $detail)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $detail->item_name }}</td>
                                            <td>{{ $detail->salesman_name ?? '-' }}</td>
                                            <td class="text-center">
                                                {{ number_format($detail->SOIVD_OrderQty) }}</td>
                                            <td class="text-center">{{ $detail->SOIVD_UM }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($detail->SOIVD_OrgPieceInvoiceAmount) }}</td>
                                            <td class="text-center">
                                                {{ number_format($detail->SOIVD_DiscMarkPersen) }}%</td>
                                            <td class="text-end">Rp
                                                {{ number_format($detail->SOIVD_LineInvoiceAmount) }}</td>
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
                        <h5 class="card-title mb-3">Ringkasan Pembayaran</h5>
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">Rp
                                        {{ number_format($invoiceHeader->SOIVH_InvoiceAmountGross, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Diskon</td>
                                    <td class="text-end">- Rp
                                        {{ number_format($invoiceHeader->SOIVH_DiscAmount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Pajak</td>
                                    <td class="text-end">+ Rp
                                        {{ number_format($invoiceHeader->SOIVH_TaxAmount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold fs-5">Grand Total</td>
                                    <td class="text-end fw-bold fs-5">Rp
                                        {{ number_format($invoiceHeader->SOIVH_InvoiceAmount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
