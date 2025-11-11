@extends('layouts.app')

@section('title', 'Master Jaringan Customer')

@push('styles')
    <style>
        .check-icon {
            color: #198754;
            font-size: 1.25rem;
        }

        div.dataTables_wrapper div.dataTables_filter {
            text-align: right;
        }
    </style>
@endpush

@section('content')
    <x-alert />

    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Manajemen Fitur</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-grid me-2"></i>Data Master Jaringan Customer</h5>
                        <div class="card-header-action">
                            <a href="{{ route('master-customer-network.create') }}" class="btn btn-primary btn-sm">
                                <i class="feather-plus me-2"></i>
                                Tambah Data
                            </a>
                        </div>
                    </div>

                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered align-middle"
                                id="networkCustomersTable">
                                <thead>
                                    <tr class="text-nowrap">
                                        <th class="text-center align-middle align-middle" rowspan="2">NO</th>
                                        <th rowspan="2" class="text-center align-middle">CUSTOMER</th>
                                        <th class="text-center align-middle" colspan="6">KATEGORI</th>
                                        <th rowspan="2" class="text-center align-middle">BRAND</th>
                                        <th rowspan="2" class="text-center align-middle">STATUS</th>
                                        <th rowspan="2" class="text-center align-middle">AKSI</th>
                                    </tr>
                                    <tr class="text-nowrap">
                                        <th class="text-center align-middle">ULTIME</th>
                                        <th class="text-center align-middle">MAD</th>
                                        <th class="text-center align-middle">POD</th>
                                        <th class="text-center align-middle">KEY ACCOUNT</th>
                                        <th class="text-center align-middle">RESSELER</th>
                                        <th class="text-center align-middle">SAD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customerNetworks as $index => $cust)
                                        @php
                                            $categories = explode(',', strtoupper($cust->category)); // ubah string jadi array
                                            $hasCategory = fn($x) => in_array($x, $categories);
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $cust->name }}</td>
                                            <td class="text-center">{!! $hasCategory('ULTIME') ? '<i class="feather-check-circle check-icon"></i>' : '' !!}</td>
                                            <td class="text-center">{!! $hasCategory('MAD') ? '<i class="feather-check-circle check-icon"></i>' : '' !!}</td>
                                            <td class="text-center">{!! $hasCategory('POD') ? '<i class="feather-check-circle check-icon"></i>' : '' !!}</td>
                                            <td class="text-center">{!! $hasCategory('KEY_ACCOUNT') ? '<i class="feather-check-circle check-icon"></i>' : '' !!}</td>
                                            <td class="text-center">{!! $hasCategory('RESSELER') ? '<i class="feather-check-circle check-icon"></i>' : '' !!}</td>
                                            <td class="text-center">{!! $hasCategory('SAD') ? '<i class="feather-check-circle check-icon"></i>' : '' !!}</td>
                                            <td>
                                                @forelse($cust->brands_collection as $b)
                                                    <span
                                                        class="badge bg-primary text-uppercase">{{ $b->brand_name }}</span>
                                                @empty
                                                    <span class="text-muted">-</span>
                                                @endforelse
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($cust->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-center">
                                                    <a href="{{ route('master-customer-network.detail', $cust->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="feather-eye me-2"></i> Detail
                                                    </a>
                                                    <a href="{{ route('master-customer-network.edit', $cust->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="feather-edit me-2"></i> Edit
                                                    </a>
                                                </div>
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
            $('#networkCustomersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        });
    </script>
@endpush
