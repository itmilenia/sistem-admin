@extends('layouts.app')

@section('title', 'Master Product Brand')

@push('styles')
    <style>
        div.dataTables_wrapper div.dataTables_filter {
            text-align: right;
        }
    </style>
@endpush

@section('content')
    <x-alert />

    {{-- Breadcrumb --}}
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

    {{-- Main Content --}}
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-grid me-2"></i>Data @yield('title')</h5>
                        <div class="card-header-action">
                            {{-- Mengarahkan ke route create product brand --}}
                            <a href="{{ route('master-product-brand.create') }}" class="btn btn-primary btn-sm">
                                <i class="feather-plus me-2"></i>
                                Tambah Data
                            </a>
                        </div>
                    </div>

                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered align-middle"
                                id="productBrandsTable"> {{-- ID Table diubah --}}
                                <thead>
                                    {{-- Header disederhanakan untuk Product Brand --}}
                                    <tr class="text-nowrap">
                                        <th class="text-center align-middle" style="width: 50px;">NO</th>
                                        <th class="align-middle">NAMA BRAND</th>
                                        <th class="align-middle">JENIS PERUSAHAAN</th>
                                        <th class="text-center align-middle" style="width: 100px;">STATUS</th>
                                        <th class="text-center align-middle" style="width: 200px;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loop menggunakan $productBrands dari controller --}}
                                    @foreach ($productBrands as $index => $brand)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $brand->brand_name }}</td>
                                            <td>{{ $brand->company_type ?? 'N/A' }}</td>
                                            <td class="text-center align-middle">
                                                {{-- Status
                                                badge --}}
                                                @if ($brand->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-center">
                                                    <a href="{{ route('master-product-brand.edit', $brand->id) }}"
                                                        class="btn btn-sm btn-warning">
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
    {{-- Script untuk inisialisasi DataTable --}}
    <script>
        $(document).ready(function() {
            // ID Table diubah
            $('#productBrandsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        });
    </script>
@endpush
