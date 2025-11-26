@extends('layouts.app')

@section('title', 'Data Surat Agreement MAP')

@push('styles')
    {{-- CSS untuk DataTables & integrasi Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
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
                <li class="breadcrumb-item"><a href="{{ route('agreement-letter.landing') }}">Surat Agreement</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-file-text me-2"></i>Daftar Semua Surat Agreement MAP
                        </h5>
                        {{-- Sesuaikan dengan route create Anda --}}
                        <a href="{{ route('agreement-letter.map.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-2"></i> Tambah Baru
                        </a>
                    </div>

                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="agreement-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;" class="text-center align-middle">#</th>
                                        <th class="text-center align-middle">Customer</th>
                                        <th class="text-center align-middle">Tipe Perusahaan</th>
                                        <th class="text-center align-middle">Sales</th>
                                        <th class="text-center align-middle">Periode Efektif</th>
                                        <th class="text-center align-middle">Lampiran</th>
                                        <th class="text-center align-middle">Status Berlaku</th>
                                        <th class="text-center align-middle">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($agreementLetters as $agreementLetter)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $agreementLetter->customer->name ?? 'N/A' }} </td>
                                            <td>{{ $agreementLetter->company_type }}</td>
                                            <td>{{ $agreementLetter->sales_name }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($agreementLetter->effective_start_date)->translatedFormat('d M Y') }}
                                                -
                                                {{ \Carbon\Carbon::parse($agreementLetter->effective_end_date)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($agreementLetter->agreement_letter_path)
                                                    <a href="{{ asset('storage/' . $agreementLetter->agreement_letter_path) }}"
                                                        target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="feather-file-text me-2"></i> Lihat Lampiran
                                                    </a>
                                                @else
                                                    <span class="text-muted">Tidak ada file</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($agreementLetter->is_active == 1)
                                                    <span class="badge bg-success">Berlaku</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Berlaku</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="hstack gap-2 justify-content-center">
                                                    {{-- Tombol Lihat --}}
                                                    <a href="{{ route('agreement-letter.map.show', $agreementLetter->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="feather-eye me-2"></i> Lihat
                                                    </a>

                                                    {{-- Tombol Edit --}}
                                                    <a href="{{ route('agreement-letter.map.edit', $agreementLetter->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="feather-edit me-2"></i> Edit
                                                    </a>

                                                    {{-- Form Hapus --}}
                                                    <form
                                                        action="{{ route('agreement-letter.destroy', $agreementLetter->id) }}"
                                                        method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="feather-trash-2 me-2"></i> Hapus
                                                        </button>
                                                    </form>
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
    {{-- 1. Load SweetAlert2 Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#agreement-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });

            // 2. Intersepsi Form Hapus
            $('.delete-form').on('submit', function(e) {
                e.preventDefault(); // Hentikan submit bawaan

                const form = this;
                const customerName = $(this).closest('tr').find('td').eq(1).text().trim();

                Swal.fire({
                    title: 'Konfirmasi Penghapusan?',
                    html: "Anda yakin ingin menghapus Surat Agreement untuk <b>" + customerName +
                        "</b>?<br>Data dan file lampiran akan dihapus permanen.",
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
                            text: 'Sedang menghapus data dan file. Mohon tunggu.',
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
