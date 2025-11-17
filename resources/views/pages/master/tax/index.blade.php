@extends('layouts.app')

@section('title', 'Master Pajak')

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
                            <a href="{{ route('master-tax.create') }}" class="btn btn-primary btn-sm">
                                <i class="feather-plus me-2"></i>
                                Tambah Data
                            </a>
                        </div>
                    </div>

                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered align-middle" id="taxesTable">
                                <thead>
                                    <tr class="text-nowrap">
                                        <th class="text-center align-middle" style="width: 50px;">NO</th>
                                        <th class="align-middle">NAMA PAJAK</th>
                                        <th class="text-center align-middle" style="width: 150px;">TARIF PAJAK (%)</th>
                                        <th class="text-center align-middle" style="width: 100px;">STATUS</th>
                                        <th class="text-center align-middle" style="width: 200px;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($taxes as $index => $tax)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $tax->tax_name }}</td>
                                            <td class="text-center">{{ number_format($tax->tax_rate, 1, ',', '.') }}</td>
                                            <td class="text-center align-middle">
                                                @if ($tax->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-center">
                                                    <a href="{{ route('master-tax.edit', $tax->id) }}"
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
            $('#taxesTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        });
    </script>
@endpush
