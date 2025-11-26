@extends('layouts.app')

@section('title', 'Data Klaim Produk MAP')

@push('styles')
    <style>
        div.dataTables_wrapper div.dataTables_filter {
            text-align: right;
        }

        .swal2-styled.swal2-confirm {
            margin-right: 5px;
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
                <li class="breadcrumb-item"><a href="{{ route('product-claim-form.landing') }}">Form Klaim Produk</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-file-text me-2"></i>Daftar Semua Klaim Produk MAP
                        </h5>
                        @can('buat_klaim_produk_map')
                            <a href="{{ route('product-claim-form.map.create') }}" class="btn btn-primary btn-sm">
                                <i class="feather-plus me-2"></i> Tambah Baru
                            </a>
                        @endcan
                    </div>

                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="claims-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;" class="text-center align-middle">#</th>
                                        <th class="text-center align-middle">Nama Ritel</th>
                                        <th class="text-center align-middle">Tipe Perusahaan</th>
                                        <th class="text-center align-middle">Jenis Klaim</th>
                                        <th class="text-center align-middle">Tanggal Klaim</th>
                                        <th class="text-center align-middle">Sales</th>
                                        <th class="text-center align-middle">Sales Head</th>
                                        <th class="text-center align-middle">Status Verifikasi</th>
                                        <th class="text-center align-middle">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {{-- Loop data dari controller --}}
                                    @foreach ($claims as $claim)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $claim->retail_name }}</td>
                                            <td>{{ $claim->company_type }}</td>
                                            <td>{{ $claim->claim_type ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($claim->claim_date)->translatedFormat('d M Y') }}
                                            </td>
                                            <td>{{ $claim->sales->Nama ?? 'N/A' }}</td>
                                            <td>{{ $claim->salesHead->Nama ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                @if ($claim->verification_date)
                                                    <span class="badge bg-success">Terverifikasi</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="hstack gap-2 justify-content-center">
                                                    {{-- Tombol Lihat --}}
                                                    <a href="{{ route('product-claim-form.map.show', $claim->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="feather-eye me-2"></i> Detail
                                                    </a>

                                                    {{-- Tombol Verifikasi untuk yang belum terverifikasi --}}
                                                    @can('verifikasi_klaim_produk_map')
                                                        @if (!$claim->verification_date)
                                                            <a href="{{ route('product-claim-form.verify', $claim->id) }}"
                                                                class="btn btn-sm btn-success">
                                                                <i class="feather-check me-2"></i> Verifikasi Checker
                                                            </a>
                                                        @endif
                                                    @endcan

                                                    @can('tanda_tangan_sales_klaim_produk_map')
                                                        @if ($claim->verification_date && !$claim->sales_signature_path)
                                                            <a href="{{ route('product-claim-form.sales-signature', $claim->id) }}"
                                                                class="btn btn-sm btn-success">
                                                                <i class="feather-check me-2"></i> Verifikasi Sales
                                                            </a>
                                                        @endif
                                                    @endcan

                                                    @can('tanda_tangan_head_sales_klaim_produk_map')
                                                        @if (
                                                            $claim->verification_date &&
                                                                $claim->checker_signature_path &&
                                                                $claim->sales_signature_path &&
                                                                !$claim->sales_head_signature_path)
                                                            <a href="{{ route('product-claim-form.sales-head-signature', $claim->id) }}"
                                                                class="btn btn-sm btn-success">
                                                                <i class="feather-check me-2"></i> Verifikasi Head Sales
                                                            </a>
                                                        @endif
                                                    @endcan

                                                    @can('ubah_klaim_produk_map')
                                                        {{-- Tombol Edit --}}
                                                        @if (!$claim->checker_signature_path || !$claim->sales_signature_path || !$claim->sales_head_signature_path)
                                                            <a href="{{ route('product-claim-form.map.edit', $claim->id) }}"
                                                                class="btn btn-sm btn-warning">
                                                                <i class="feather-edit me-2"></i> Edit
                                                            </a>
                                                        @endif
                                                    @endcan

                                                    @can('buat_klaim_produk_map')
                                                        <a href="{{ route('product-claim-form.export-pdf', $claim->id) }}"
                                                            class="btn btn-primary btn-sm" target="_blank">
                                                            <i class="feather-printer me-2"></i> Cetak PDF
                                                        </a>
                                                    @endcan

                                                    @can('hapus_klaim_produk_map')
                                                        {{-- Form Hapus --}}
                                                        @if (!$claim->checker_signature_path || !$claim->sales_signature_path || !$claim->sales_head_signature_path)
                                                            <form
                                                                action="{{ route('product-claim-form.destroy', $claim->id) }}"
                                                                method="POST" class="d-inline delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="feather-trash-2 me-2"></i> Hapus
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endcan
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
    {{-- Load SweetAlert2 Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#claims-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });

            // Intersepsi Form Hapus
            $('.delete-form').on('submit', function(e) {
                e.preventDefault(); // Hentikan submit bawaan

                const form = this;
                // Ambil nama ritel dari kolom ke-2 (index 1)
                const retailName = $(this).closest('tr').find('td').eq(1).text().trim();

                Swal.fire({
                    title: 'Konfirmasi Penghapusan?',
                    html: "Anda yakin ingin menghapus Klaim Produk untuk <b>" + retailName +
                        "</b>?<br>Data ini akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Sedang menghapus data. Mohon tunggu.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
