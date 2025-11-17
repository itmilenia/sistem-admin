@extends('layouts.app')

@section('title', 'Data Customer')

@push('styles')
    {{-- CSS untuk DataTables & integrasi Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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
                <li class="breadcrumb-item"><a href="{{ route('customer-data.landing') }}">Customer</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Daftar Semua Customer Mega Auto Prima</h5>
                    </div>
                    <div class="card-body custom-card-action p-0">
                        <div class="row p-3">
                            <div class="col-md-3">
                                <label for="status_filter" class="form-label">Filter Status:</label>
                                <select id="status_filter" class="form-select">
                                    <option value="">Tampilkan Semua</option>
                                    <option value="Aktif">Hanya Aktif</option>
                                    <option value="Tidak Aktif">Hanya Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="customer-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;" class="text-center">#</th>
                                        <th class="text-center">ID Customer</th>
                                        <th class="text-center">Nama Customer</th>
                                        <th class="text-center">Kontak Person</th>
                                        <th class="text-center">Telepon</th>
                                        <th class="text-center">No.Handphone</th>
                                        <th class="text-center">Status</th>
                                        <th style="width: 10%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $customer->MFCUS_CustomerID }}</td>
                                            <td>{{ $customer->MFCUS_Description }}</td>
                                            <td>{{ $customer->MFCUS_Contact ?? '-' }}</td>
                                            <td>{{ $customer->MFCUS_Telephone ?? '-' }}</td>
                                            <td>{{ $customer->MFCUS_Mobilephone ?? '-' }}</td>
                                            <td class="text-center">
                                                @if ($customer->MFCUS_Active == 1)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-center">
                                                    <a href="{{ route('customer-data-map.show', $customer->MFCUS_CustomerID) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="feather-eye me-2"></i> Lihat
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
    {{-- jQuery (diperlukan oleh DataTables) --}}
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    {{-- JavaScript untuk DataTables & integrasi Bootstrap 5 --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. Simpan instance DataTable ke dalam variabel 'table'
            var table = $('#customer-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });

            // 2. Tambahkan event listener untuk dropdown
            $('#status_filter').on('change', function() {
                var status = $(this).val();

                if (status) {
                    table.column(6)
                        .search('^' + status + '$', true, false)
                        .draw();
                } else {
                    table.column(6)
                        .search('', false, false)
                        .draw();
                }
            });
        });
    </script>
@endpush
