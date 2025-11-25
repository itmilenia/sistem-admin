@extends('layouts.app')

@section('title', 'Data Surat Penawaran Milenia')

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
                <li class="breadcrumb-item"><a href="{{ route('quotation-letter.landing') }}">Surat Penawaran</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-file-text me-2"></i>Daftar Semua Surat Penawaran Milenia
                        </h5>
                        <a href="{{ route('quotation-letter.milenia.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-2"></i> Tambah Baru
                        </a>
                    </div>

                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="quotation-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;" class="text-center">#</th>
                                        <th class="text-center">No Surat Penawaran</th>
                                        <th class="text-center">Perihal</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Penerima</th>
                                        <th style="width: 20%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($quotationLetters as $quotationLetter)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $quotationLetter->quotation_letter_number }} </td>
                                            <td class="text-center">{{ $quotationLetter->subject }} </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($quotationLetter->letter_date)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="text-center">{{ $quotationLetter->recipient_company_name }}</td>
                                            <td class="text-center">
                                                <div class="hstack gap-2 justify-content-center">
                                                    {{-- Tombol Lihat --}}
                                                    <a href="{{ route('quotation-letter.milenia.show', $quotationLetter->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="feather-eye me-2"></i> Lihat
                                                    </a>

                                                    {{-- Tombol Export Pdf --}}
                                                    <a href="{{ route('quotation-letter.export-pdf', $quotationLetter->id) }}"
                                                        class="btn btn-sm btn-success" target="_blank">
                                                        <i class="feather-download me-2"></i> Export PDF
                                                    </a>

                                                    {{-- Tombol Edit --}}
                                                    <a href="{{ route('quotation-letter.milenia.edit', $quotationLetter->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="feather-edit me-2"></i> Edit
                                                    </a>

                                                    {{-- Form Hapus --}}
                                                    <form
                                                        action="{{ route('quotation-letter.destroy', $quotationLetter->id) }}"
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
            $('#quotation-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });

            // Pemberitahuan berhasil
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 2500,
                    showConfirmButton: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    buttonsStyling: false
                });
            @endif

            // Pemberitahuan error
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: '{{ session('error') }}',
                    timer: 3000,
                    showConfirmButton: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    buttonsStyling: false
                });
            @endif


            // 2. Intersepsi Form Hapus
            $('.delete-form').on('submit', function(e) {
                e.preventDefault(); // Hentikan submit bawaan

                const form = this;
                // Ambil nomor surat dari kolom kedua (index 1) di baris (tr) yang sama
                const letterNumber = $(this).closest('tr').find('td').eq(1).text().trim();

                Swal.fire({
                    title: 'Konfirmasi Penghapusan?',
                    html: "Anda yakin ingin menghapus Surat Penawaran <strong>" + letterNumber +
                        "</strong>?<br>Data dan file lampiran akan dihapus permanen.",
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
