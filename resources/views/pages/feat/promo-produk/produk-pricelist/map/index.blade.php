@extends('layouts.app')

@section('title', 'Data Pricelist Produk')

@push('styles')
    <style>
        /* Style agar search bar DataTables lebih pas */
        div.dataTables_wrapper div.dataTables_filter {
            text-align: right;
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
                <li class="breadcrumb-item"><a href="#">Promo & Produk</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pricelist-produk.landing') }}">Pricelist Produk</a></li>
                <li class="breadcrumb-item active">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Daftar Pricelist Produk Mega Auto Prima</h5>
                    </div>
                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="pricelist-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;" class="text-center align-middle" rowspan="2">#</th>
                                        <th class="text-center align-middle" rowspan="2">Price ID</th>
                                        <th class="text-center align-middle" rowspan="2">Currency</th>
                                        <th class="text-center align-middle" rowspan="2">Customer Class</th>
                                        <th class="text-center align-middle" rowspan="2">Item ID</th>
                                        <th class="text-center align-middle" rowspan="2">Nama Produk</th>
                                        <th class="text-center align-middle" colspan="2">Harga</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center align-middle">Sebelum <br> PPN</th>
                                        <th class="text-center align-middle">Setelah <br> PPN (11%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pricelists as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $item->SOMPD_PriceID }}</td>
                                            <td class="text-center">{{ $item->SOMPD_CurrencyID }}</td>
                                            <td class="text-center">{{ $item->SOMPD_CusClass ?? '-' }}</td>
                                            <td class="text-center">{{ $item->SOMPD_ItemID }}</td>
                                            <td>{{ $item->SOMPD_ItemDesc ?? '-' }}</td>

                                            {{-- Harga Sebelum PPN --}}
                                            <td class="text-end">
                                                {{ number_format($item->SOMPD_PriceAmount, 0, ',', '.') }}
                                            </td>

                                            {{-- Harga Setelah PPN --}}
                                            <td class="text-end">
                                                @php
                                                    $taxRate = $taxActive->tax_rate ?? 0;
                                                    $taxRate = $taxRate / 100;
                                                    $total = $item->SOMPD_PriceAmount * (1 + $taxRate);
                                                @endphp

                                                {{ number_format($total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#pricelist-table').DataTable({
                deferRender: true,
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
            });
        });
    </script>
@endpush
