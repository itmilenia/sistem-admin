@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $startDate = \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y');
    $endDate = \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y');
@endphp

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Dashboard</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">Dashboard</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        {{-- =========================== --}}
        {{-- BAGIAN FILTER TANGGAL --}}
        {{-- =========================== --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label fw-bold">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ request('start_date', date('Y-m-01')) }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label fw-bold">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ request('end_date', date('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-filter"></i> Filter
                                    </button>
                                    <a href="{{ url()->current() }}" class="btn btn-secondary w-100">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Card 1 --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-bold small">Total Revenue</div>
                                <h4 class="my-1 count-up" data-value="{{ $totalSummary['total_amount'] }}"
                                    data-format="rupiah">0</h4>
                            </div>
                            <i class="bi bi-cash-stack fs-1 text-success text-opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-bold small">Total Barang Terjual</div>
                                <h4 class="my-1 count-up" data-value="{{ $totalSummary['total_qty'] }}">0</h4>
                            </div>
                            <i class="bi bi-box-seam-fill fs-1 text-primary text-opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-bold small">Total Customer Jaringan</div>
                                <h4 class="my-1 count-up" data-value="{{ $totalCustomerNetwork }}">0</h4>
                            </div>
                            <i class="bi bi-diagram-3-fill fs-1 text-warning text-opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4 --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-bold small">Total User Aktif</div>
                                <h4 class="my-1 count-up" data-value="{{ $totalActiveUser }}">0</h4>
                            </div>
                            <i class="bi bi-people-fill fs-1 text-info text-opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🏆 Top Salesman ({{ $startDate }} - {{ $endDate }})</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="pricelistTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="salesman-milenia-tab" data-bs-toggle="tab"
                                    data-bs-target="#salesman-milenia" type="button" role="tab"
                                    aria-controls="salesman-milenia" aria-selected="true">
                                    MMM Pusat
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="salesman-milenia-branch-tab" data-bs-toggle="tab"
                                    data-bs-target="#salesman-milenia-branch" type="button" role="tab"
                                    aria-controls="salesman-milenia-branch" aria-selected="false">
                                    MMM Cabang
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="salesman-map-tab" data-bs-toggle="tab"
                                    data-bs-target="#salesman-map" type="button" role="tab"
                                    aria-controls="salesman-map" aria-selected="true">
                                    MAP Pusat
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="salesman-map-branch-tab" data-bs-toggle="tab"
                                    data-bs-target="#salesman-map-branch" type="button" role="tab"
                                    aria-controls="salesman-map-branch" aria-selected="false">
                                    MAP Cabang
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="salesmanTabsContent">

                            {{-- TAB 1: MILENIA PUSAT --}}
                            <div class="tab-pane fade show active" id="salesman-milenia" role="tabpanel"
                                aria-labelledby="salesman-milenia-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-salesman-milenia" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="align-middle">Nama Sales</th>
                                                <th class="align-middle">Sale Amount</th>
                                                <th class="align-middle">Return Amount</th>
                                                <th class="align-middle">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($salesManSalesMilenia as $salesman)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $salesman->salesman_name ?? 'N/A' }}
                                                        <small
                                                            class="d-block text-muted">{{ $salesman->SOIVD_SalesmanID }}</small>
                                                    </td>
                                                    <td>Rp {{ number_format($salesman->total_amount, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($salesman->total_return_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($salesman->net_amount, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- TAB 2: MILENIA CABANG --}}
                            <div class="tab-pane fade" id="salesman-milenia-branch" role="tabpanel"
                                aria-labelledby="salesman-milenia-branch-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-salesman-milenia-branch" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="align-middle">Nama Sales</th>
                                                <th class="align-middle">Sale Amount</th>
                                                <th class="align-middle">Return Amount</th>
                                                <th class="align-middle">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($salesManSalesMileniaBranch as $salesman)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $salesman->salesman_name ?? 'N/A' }}
                                                        <small
                                                            class="d-block text-muted">{{ $salesman->SOIVD_SalesmanID }}</small>
                                                    </td>
                                                    <td>Rp {{ number_format($salesman->total_amount, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($salesman->total_return_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($salesman->net_amount, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- TAB 3: MAP PUSAT --}}
                            <div class="tab-pane fade" id="salesman-map" role="tabpanel"
                                aria-labelledby="salesman-map-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-salesman-map" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="align-middle">Nama Sales</th>
                                                <th class="align-middle">Sale Amount</th>
                                                <th class="align-middle">Return Amount</th>
                                                <th class="align-middle">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($salesManSalesMap as $salesman)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $salesman->salesman_name ?? 'N/A' }}
                                                        <small
                                                            class="d-block text-muted">{{ $salesman->SOIVD_SalesmanID }}</small>
                                                    </td>
                                                    <td>Rp {{ number_format($salesman->total_amount, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($salesman->total_return_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($salesman->net_amount, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- TAB 4: MAP CABANG --}}
                            <div class="tab-pane fade" id="salesman-map-branch" role="tabpanel"
                                aria-labelledby="salesman-map-branch-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-salesman-map-branch" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="align-middle">Nama Sales</th>
                                                <th class="align-middle">Sale Amount</th>
                                                <th class="align-middle">Return Amount</th>
                                                <th class="align-middle">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($salesManSalesMapBranch as $salesman)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $salesman->salesman_name ?? 'N/A' }}
                                                        <small
                                                            class="d-block text-muted">{{ $salesman->SOIVD_SalesmanID }}</small>
                                                    </td>
                                                    <td>Rp {{ number_format($salesman->total_amount, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($salesman->total_return_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($salesman->net_amount, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🏆 Top Transaksi per Brand ({{ $startDate }} - {{ $endDate }})</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="pricelistTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="transaction-milenia-brand-tab" data-bs-toggle="tab"
                                    data-bs-target="#transaction-milenia-brand" type="button" role="tab"
                                    aria-controls="transaction-milenia-brand" aria-selected="true">
                                    MMM Pusat
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="transaction-milenia-brand-branch-tab" data-bs-toggle="tab"
                                    data-bs-target="#transaction-milenia-brand-branch" type="button" role="tab"
                                    aria-controls="transaction-milenia-brand-branch" aria-selected="false">
                                    MMM Cabang
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="transaction-map-brand-tab" data-bs-toggle="tab"
                                    data-bs-target="#transaction-map-brand" type="button" role="tab"
                                    aria-controls="transaction-map-brand" aria-selected="true">
                                    MAP Pusat
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="transaction-map-brand-branch-tab" data-bs-toggle="tab"
                                    data-bs-target="#transaction-map-brand-branch" type="button" role="tab"
                                    aria-controls="transaction-map-brand-branch" aria-selected="false">
                                    MAP Cabang
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="salesmanTabsContent">

                            {{-- TAB 1: MILENIA PUSAT --}}
                            <div class="tab-pane fade show active" id="transaction-milenia-brand" role="tabpanel"
                                aria-labelledby="transaction-milenia-brand-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-transaction-milenia-brand" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="align-middle">Nama Brand</th>
                                                <th class="align-middle">Sale Amount</th>
                                                <th class="align-middle">Return Amount</th>
                                                <th class="align-middle">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($brandTransactionMilenia as $brandTransaction)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $brandTransaction->brand_name ?? 'N/A' }}
                                                        <small
                                                            class="d-block text-muted">{{ $brandTransaction->MFIB_BrandID }}</small>
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->sale_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->retur_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->net_amount, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- TAB 2: MILENIA CABANG --}}
                            <div class="tab-pane fade" id="transaction-milenia-brand-branch" role="tabpanel"
                                aria-labelledby="transaction-milenia-brand-branch-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-transaction-milenia-brand-branch" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="align-middle">Nama Brand</th>
                                                <th class="align-middle">Sale Amount</th>
                                                <th class="align-middle">Return Amount</th>
                                                <th class="align-middle">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($brandTransactionMileniaBranch as $brandTransaction)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $brandTransaction->brand_name ?? 'N/A' }}
                                                        <small
                                                            class="d-block text-muted">{{ $brandTransaction->MFIB_BrandID }}</small>
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->sale_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->retur_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->net_amount, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- TAB 3: MAP PUSAT --}}
                            <div class="tab-pane fade" id="transaction-map-brand" role="tabpanel"
                                aria-labelledby="transaction-map-brand-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-transaction-map-brand" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="align-middle">Nama Brand</th>
                                                <th class="align-middle">Sale Amount</th>
                                                <th class="align-middle">Return Amount</th>
                                                <th class="align-middle">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($brandTransactionMap as $brandTransaction)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $brandTransaction->brand_name ?? 'N/A' }}
                                                        <small
                                                            class="d-block text-muted">{{ $brandTransaction->MFIB_BrandID }}</small>
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->sale_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->retur_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->net_amount, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- TAB 4: MAP CABANG --}}
                            <div class="tab-pane fade" id="transaction-map-brand-branch" role="tabpanel"
                                aria-labelledby="transaction-map-brand-branch-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-transaction-map-brand-branch" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="align-middle">Nama Brand</th>
                                                <th class="align-middle">Sale Amount</th>
                                                <th class="align-middle">Return Amount</th>
                                                <th class="align-middle">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($brandTransactionMapBranch as $brandTransaction)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $brandTransaction->brand_name ?? 'N/A' }}
                                                        <small
                                                            class="d-block text-muted">{{ $brandTransaction->MFIB_BrandID }}</small>
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->sale_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->retur_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp
                                                        {{ number_format($brandTransaction->net_amount, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🏆 Top Transaksi per Customer ({{ $startDate }} - {{ $endDate }})</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="pricelistTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="transaction-milenia-customer-tab"
                                    data-bs-toggle="tab" data-bs-target="#transaction-milenia-customer" type="button"
                                    role="tab" aria-controls="transaction-milenia-customer" aria-selected="true">
                                    Customer MMM
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="transaction-map-customer-tab" data-bs-toggle="tab"
                                    data-bs-target="#transaction-map-customer" type="button" role="tab"
                                    aria-controls="transaction-map-customer" aria-selected="true">
                                    Customer MAP
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="salesmanTabsContent">

                            {{-- TAB 1: MILENIA PUSAT --}}
                            <div class="tab-pane fade show active" id="transaction-milenia-customer" role="tabpanel"
                                aria-labelledby="transaction-milenia-customer-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-transaction-milenia-customer" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="align-middle">Nama Customer</th>
                                                <th class="align-middle">Sale Amount</th>
                                                <th class="align-middle">Return Amount</th>
                                                <th class="align-middle">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($finalReportMilenia as $report)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $report->customer_name ?? 'N/A' }}
                                                        <small
                                                            class="d-block text-muted">{{ $report->MFCUS_CustomerID }}</small>
                                                    </td>
                                                    <td>Rp {{ number_format($report->sale_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($report->retur_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($report->net_amount, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- TAB 2: MAP PUSAT --}}
                            <div class="tab-pane fade" id="transaction-map-customer" role="tabpanel"
                                aria-labelledby="transaction-map-customer-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-transaction-map-customer" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="align-middle">Nama Customer</th>
                                                <th class="align-middle">Sale Amount</th>
                                                <th class="align-middle">Return Amount</th>
                                                <th class="align-middle">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($finalReportMap as $report)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ $report->customer_name ?? 'N/A' }}
                                                        <small
                                                            class="d-block text-muted">{{ $report->MFCUS_CustomerID }}</small>
                                                    </td>
                                                    <td>Rp {{ number_format($report->sale_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($report->retur_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($report->net_amount, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📈 Pricelist Terbaru ({{ $startDate }} - {{ $endDate }})</h5>
                    </div>
                    <div class="card-body">

                        {{-- Navigasi Tabs --}}
                        <ul class="nav nav-tabs" id="pricelistTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="milenia-tab" data-bs-toggle="tab"
                                    data-bs-target="#milenia" type="button" role="tab" aria-controls="milenia"
                                    aria-selected="true">
                                    Pricelist MMM
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="map-tab" data-bs-toggle="tab" data-bs-target="#map"
                                    type="button" role="tab" aria-controls="map" aria-selected="false">
                                    Pricelist MAP
                                </button>
                            </li>
                        </ul>

                        {{-- Konten Tabs --}}
                        <div class="tab-content" id="pricelistTabsContent">

                            {{-- Tab Pane 1: Milenia --}}
                            <div class="tab-pane fade show active" id="milenia" role="tabpanel"
                                aria-labelledby="milenia-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-milenia" class="table table-striped table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Price ID</th>
                                                <th>Nama Item</th>
                                                <th>Harga <br> Sebelum PPN
                                                    ({{ number_format($taxActive->tax_rate, 0, ',', '.') }})%</th>
                                                <th>Harga <br> Sesudah PPN
                                                    ({{ number_format($taxActive->tax_rate, 0, ',', '.') }})%</th>
                                                <th>Terakhir Update</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($updatedPricelistsMilenia as $pricelist)
                                                <tr>
                                                    <td>{{ $pricelist->SOMPD_PriceID }}</td>
                                                    <td>
                                                        {{ $pricelist->ItemMilenia->MFIMA_Description ?? $pricelist->SOMPD_ItemDesc }}
                                                        <small
                                                            class="d-block text-muted">{{ $pricelist->SOMPD_ItemID }}</small>
                                                    </td>
                                                    <td>
                                                        Rp {{ number_format($pricelist->SOMPD_PriceAmount, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $taxRate = $taxActive->tax_rate ?? 0;
                                                            $taxRate = $taxRate / 100;
                                                            $total = $pricelist->SOMPD_PriceAmount * (1 + $taxRate);
                                                        @endphp
                                                        Rp {{ number_format($total, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        {{ $pricelist->SOMPD_UPDATE->format('d M Y, H:i') }}
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Tab Pane 2: Map --}}
                            <div class="tab-pane fade" id="map" role="tabpanel" aria-labelledby="map-tab">
                                <div class="table-responsive mt-3">
                                    <table id="table-map" class="table table-striped table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Price ID</th>
                                                <th>Nama Item</th>
                                                <th>Harga <br> Sebelum PPN
                                                    ({{ number_format($taxActive->tax_rate, 0, ',', '.') }})%</th>
                                                <th>Harga <br> Sesudah PPN
                                                    ({{ number_format($taxActive->tax_rate, 0, ',', '.') }})%</th>
                                                <th>Terakhir Update</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($updatedPricelistsMap as $pricelist)
                                                <tr>
                                                    <td>{{ $pricelist->SOMPD_PriceID }}</td>
                                                    <td>
                                                        {{ $pricelist->ItemMilenia->MFIMA_Description ?? $pricelist->SOMPD_ItemDesc }}
                                                        <small
                                                            class="d-block text-muted">{{ $pricelist->SOMPD_ItemID }}</small>
                                                    </td>
                                                    <td>
                                                        Rp {{ number_format($pricelist->SOMPD_PriceAmount, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $taxRate = $taxActive->tax_rate ?? 0;
                                                            $taxRate = $taxRate / 100;
                                                            $total = $pricelist->SOMPD_PriceAmount * (1 + $taxRate);
                                                        @endphp
                                                        Rp {{ number_format($total, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        {{ $pricelist->SOMPD_UPDATE->format('d M Y, H:i') }}
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Script Inisialisasi Anda (tidak diubah) --}}
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable untuk tabel pertama (Milenia)
            var tableMilenia = $('#table-milenia').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            // Inisialisasi DataTable untuk tabel kedua (Map)
            var tableMap = $('#table-map').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            // Inisialisasi DataTable untuk tabel ketiga (Salesman Milenia)
            var tableSalesmanMilenia = $('#table-salesman-milenia').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            // Inisialisasi DataTable untuk tabel keempat (Salesman Milenia Branch)
            var tableSalesmanMileniaBranch = $('#table-salesman-milenia-branch').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            var tableSalesmanMap = $('#table-salesman-map').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            var tableSalesmanMapBranch = $('#table-salesman-map-branch').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            var tableTransactionBrandMilenia = $('#table-transaction-milenia-brand').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            var tableTransactionBrandMileniaBranch = $('#table-transaction-milenia-brand-branch').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            var tableTransactionBrandMap = $('#table-transaction-map-brand').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            var tableTransactionBrandMapBranch = $('#table-transaction-map-brand-branch').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            var tableTransactionCustomerMilenia = $('#table-transaction-milenia-customer').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            var tableTransactionCustomerMap = $('#table-transaction-map-customer').DataTable({
                "width": "100%",
                'pageLength': 5,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            // Intersepsi perubahan tab
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                var targetTabId = $(e.target).attr('data-bs-target');
                if (targetTabId === '#milenia') {
                    tableMilenia.columns.adjust().draw();
                } else if (targetTabId === '#map') {
                    tableMap.columns.adjust().draw();
                } else if (targetTabId === '#salesman-milenia') {
                    tableSalesmanMilenia.columns.adjust().draw();
                } else if (targetTabId === '#salesman-milenia-branch') {
                    tableSalesmanMileniaBranch.columns.adjust().draw();
                } else if (targetTabId === '#salesman-map') {
                    tableSalesmanMap.columns.adjust().draw();
                } else if (targetTabId === '#salesman-map-branch') {
                    tableSalesmanMapBranch.columns.adjust().draw();
                } else if (targetTabId === '#transaction-milenia-brand') {
                    tableTransactionBrandMilenia.columns.adjust().draw();
                } else if (targetTabId === '#transaction-milenia-brand-branch') {
                    tableTransactionBrandMileniaBranch.columns.adjust().draw();
                } else if (targetTabId === '#transaction-map-brand') {
                    tableTransactionBrandMap.columns.adjust().draw();
                } else if (targetTabId === '#transaction-map-brand-branch') {
                    tableTransactionBrandMapBranch.columns.adjust().draw();
                } else if (targetTabId === '#transaction-milenia-customer') {
                    tableTransactionCustomerMilenia.columns.adjust().draw();
                } else if (targetTabId === '#transaction-map-customer') {
                    tableTransactionCustomerMap.columns.adjust().draw();
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            function formatRibuan(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function formatRupiah(num) {
                return "Rp " + formatRibuan(num);
            }

            const counters = document.querySelectorAll('.count-up');
            const duration = 1000;

            counters.forEach(counter => {
                let start = 0;
                let end = parseInt(counter.getAttribute("data-value"));
                let isRupiah = counter.getAttribute("data-format") === "rupiah";
                let increment = end / (duration / 16);

                function updateCounter() {
                    start += increment;
                    let currentValue = Math.floor(start);

                    if (start < end) {
                        counter.innerText = isRupiah ?
                            formatRupiah(currentValue) :
                            formatRibuan(currentValue);

                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.innerText = isRupiah ?
                            formatRupiah(end) :
                            formatRibuan(end);
                    }
                }

                updateCounter();
            });
        });
    </script>
@endpush
