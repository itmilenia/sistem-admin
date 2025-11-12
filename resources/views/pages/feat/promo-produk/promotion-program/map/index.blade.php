@extends('layouts.app')

@section('title', 'Data Program Promosi MAP')

@push('styles')
    {{-- CSS untuk DataTables & integrasi Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        div.dataTables_wrapper div.dataTables_filter {
            text-align: right;
        }

        /* Pastikan kolom aksi tidak ter-wrap */
        #program-table .aksi-kolom {
            white-space: nowrap;
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
                {{-- Sesuaikan route landing jika ada --}}
                <li class="breadcrumb-item"><a href="{{ route('promotion-program.landing') }}">Program Promosi</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-award me-2"></i>Daftar Semua Program Promosi MAP
                        </h5>
                        {{-- Sesuaikan dengan route create Anda --}}
                        <a href="{{ route('promotion-program.map.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-2"></i> Tambah Baru
                        </a>
                    </div>

                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="program-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;" class="text-center align-middle">#</th>
                                        <th class="text-center align-middle">Nama Program</th>
                                        <th class="text-center align-middle">Tipe Customer</th>
                                        <th class="text-center align-middle">Periode Efektif</th>
                                        <th class="text-center align-middle">Jumlah Item</th>
                                        <th class="text-center align-middle">Status</th>
                                        <th class="text-center align-middle">Lampiran</th>
                                        <th class="text-center align-middle">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($promotionPrograms as $program)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $program->program_name }}</td>
                                            <td>{{ str_replace('_', ' ', $program->customer_type) }}</td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                {{ \Carbon\Carbon::parse($program->effective_start_date)->translatedFormat('d M Y') }}
                                                -
                                                {{ \Carbon\Carbon::parse($program->effective_end_date)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $program->details->count() }} Item</span>
                                            </td>
                                            <td class="text-center">
                                                @if ($program->is_active)
                                                    <span class="badge bg-success">Berlaku</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Berlaku</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($program->program_file)
                                                    <a href="{{ Storage::url($program->program_file) }}" target="_blank"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="feather-file-text me-2"></i> Lihat
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center aksi-kolom">
                                                <div class="hstack gap-2 justify-content-center">
                                                    {{-- Tombol Lihat --}}
                                                    <a href="{{ route('promotion-program.map.show', $program->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="feather-eye me-2"></i> Detail
                                                    </a>

                                                    {{-- Tombol Edit --}}
                                                    <a href="{{ route('promotion-program.map.edit', $program->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="feather-edit me-2"></i> Edit
                                                    </a>

                                                    {{-- Form Hapus --}}
                                                    <form action="{{ route('promotion-program.destroy', $program->id) }}"
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
    {{-- 2. Load DataTables --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // 3. Inisialisasi Tooltip Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            // 4. Inisialisasi DataTables
            $('#program-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                // Menonaktifkan sorting untuk kolom aksi
                "columnDefs": [{
                    "orderable": false,
                    "targets": 7
                }]
            });

            // 5. Intersepsi Form Hapus
            $('.delete-form').on('submit', function(e) {
                e.preventDefault(); // Hentikan submit bawaan

                const form = this;
                // Ambil nama program dari kolom kedua (index 1)
                const programName = $(this).closest('tr').find('td').eq(1).text().trim();

                Swal.fire({
                    title: 'Konfirmasi Penghapusan?',
                    html: "Anda yakin ingin menghapus Program Promosi <b>" + programName +
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
                            text: 'Sedang menghapus data. Mohon tunggu.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        form.submit(); // Lanjutkan submit form
                    }
                });
            });
        });
    </script>
@endpush
