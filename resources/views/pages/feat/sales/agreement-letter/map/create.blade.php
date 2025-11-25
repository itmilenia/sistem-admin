@extends('layouts.app')

@section('title', 'Tambah Surat Agreement Baru')

@section('content')
    <x-alert />

    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('agreement-letter.landing') }}">Surat Agreement</a></li>
                <li class="breadcrumb-item"><a href="{{ route('agreement-letter.map.index') }}">Data Surat Agreement
                        MAP</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('agreement-letter.map.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-plus-circle me-2"></i>Formulir Data Surat Agreement</h5>
                    </div>

                    <div class="card-body">
                        {{-- Ganti form id dan route action --}}
                        <form id="create-agreement-form" action="{{ route('agreement-letter.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            {{-- Section 1: Detail Surat --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Detail Surat Agreement</legend>

                                {{-- Field: customer_id (Select2) --}}
                                <div class="row mb-3">
                                    <label for="customer_id" class="col-sm-3 col-form-label">Customer <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-select @error('customer_id') is-invalid @enderror"
                                            id="customer_id" name="customer_id" required style="width: 100%;">
                                            <option value="" selected disabled>-- Pilih Customer --</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('customer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Field: company_type (Select) --}}
                                <div class="row mb-3">
                                    <label for="company_type" class="col-sm-3 col-form-label">Tipe Perusahaan <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text"
                                            class="form-control @error('company_type') is-invalid @enderror"
                                            id="company_type" name="company_type" value="PT Mega Auto Prima" readonly>
                                        @error('company_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Field: sales_name (Text) --}}
                                <div class="row mb-3">
                                    <label for="sales_name" class="col-sm-3 col-form-label">Nama Sales <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        {{-- Ambil dari salesNames --}}
                                        <select class="form-select @error('sales_name') is-invalid @enderror"
                                            id="sales_name" name="sales_name" required>
                                            <option value="" selected disabled>-- Pilih Sales --</option>
                                            @foreach ($salesNames as $salesName)
                                                <option value="{{ $salesName }}"
                                                    {{ old('sales_name') == $salesName ? 'selected' : '' }}>
                                                    {{ $salesName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('sales_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Field: effective_start_date (Date) --}}
                                <div class="row mb-3">
                                    <label for="effective_start_date" class="col-sm-3 col-form-label">Mulai Efektif
                                        <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="date"
                                            class="form-control @error('effective_start_date') is-invalid @enderror"
                                            id="effective_start_date" name="effective_start_date"
                                            value="{{ old('effective_start_date', date('Y-m-d')) }}" required>
                                        @error('effective_start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Field: effective_end_date (Date) --}}
                                <div class="row mb-3">
                                    <label for="effective_end_date" class="col-sm-3 col-form-label">Selesai Efektif
                                        <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="date"
                                            class="form-control @error('effective_end_date') is-invalid @enderror"
                                            id="effective_end_date" name="effective_end_date"
                                            value="{{ old('effective_end_date') }}" required>
                                        @error('effective_end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Field: is_active (select) --}}
                                <div class="row mb-3">
                                    <label for="is_active" class="col-sm-3 col-form-label">Status Berlaku <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-select @error('is_active') is-invalid @enderror"
                                            id="is_active" name="is_active" required>
                                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>
                                                Berlaku
                                            </option>
                                            <option value="0" {{ old('is_active', '1') == '0' ? 'selected' : '' }}>
                                                Tidak Berlaku
                                            </option>
                                        </select>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            {{-- Section 2: Upload File --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Lampiran File (PDF)</legend>

                                <div class="row mb-3">
                                    {{-- Ganti id dan name ke 'agreement_letter_file' --}}
                                    <label for="agreement_letter_file" class="col-sm-3 col-form-label">Upload File PDF
                                        <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('agreement_letter_file') is-invalid @enderror"
                                            type="file" id="agreement_letter_file" name="agreement_letter_file"
                                            accept=".pdf" required>
                                        <div class="form-text">File harus berupa PDF dan ukuran maksimum 10MB.</div>
                                        {{-- Ganti error key --}}
                                        @error('agreement_letter_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            {{-- Tombol Submit --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="confirmSubmit()">
                                    <i class="feather-save me-2"></i> Simpan Surat
                                </button>
                            </div>
                        </form>
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
            // 3. Inisialisasi Select2 untuk Customer
            $('#customer_id').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Customer --',
                width: '100%',
                allowClear: true
            });

            $('#sales_name').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Sales --',
                dropdownParent: $('body'),
                width: '100%',
                allowClear: true
            });

            $('#is_active').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Status Berlaku --',
                dropdownParent: $('body'),
                width: '100%',
                allowClear: true
            });
        });

        // 4. Fungsi konfirmasi submit
        function confirmSubmit() {
            // Ganti form id
            const form = document.getElementById('create-agreement-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Penyimpanan Data?',
                text: "Pastikan semua data dan file yang diunggah sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false

            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menyimpan data dan mengunggah file. Mohon tunggu.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            });
        }
    </script>
@endpush
