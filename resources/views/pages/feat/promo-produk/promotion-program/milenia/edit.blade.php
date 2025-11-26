@extends('layouts.app')

@section('title', 'Edit Program Promosi: ' . $promotionProgram->program_name)

@push('styles')
    <style>
        .select2-container--bootstrap-5 .select2-selection--multiple {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            min-height: 38px !important;
            padding: 4px !important;
            gap: 4px !important;
            cursor: text;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-search--inline {
            display: inline-flex !important;
            align-items: center !important;
            flex: 1 1 auto !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-search__field {
            display: inline-block !important;
            width: auto !important;
            min-width: 120px !important;
            height: 28px !important;
            line-height: 1.5 !important;
            padding: 4px 0 !important;
            margin: 0 !important;
            resize: none !important;
            /* biar gak bisa diubah ukuran */
            overflow: hidden !important;
            border: none !important;
            outline: none !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        #items.form-select {
            display: none !important;
        }

        .select2-container--open {
            z-index: 9999;
        }

        .current-file-link {
            font-weight: 600;
            text-decoration: none;
        }

        .current-file-link:hover {
            text-decoration: underline;
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
                <li class="breadcrumb-item"><a href="{{ route('promotion-program.landing') }}">Program Promosi</a></li>
                <li class="breadcrumb-item"><a href="{{ route('promotion-program.milenia.index') }}">Data Program Promosi
                        Milenia</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('promotion-program.milenia.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-edit me-2"></i>Formulir Edit Program Promosi</h5>
                    </div>

                    <div class="card-body">
                        <form id="edit-program-form" action="{{ route('promotion-program.update', $promotionProgram->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="company_type" value="PT Milenia Mega Mandiri">

                            {{-- Section 1: Detail Program --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Detail Program Promosi</legend>

                                {{-- Field: program_name (Text) --}}
                                <div class="row mb-3">
                                    <label for="program_name" class="col-sm-3 col-form-label">Nama Program <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text"
                                            class="form-control @error('program_name') is-invalid @enderror"
                                            id="program_name" name="program_name"
                                            value="{{ old('program_name', $promotionProgram->program_name) }}" required
                                            placeholder="Cth: Promo Lebaran 2024 (Retail)">
                                        @error('program_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Field: customer_type (Select) --}}
                                <div class="row mb-3">
                                    <label for="customer_type" class="col-sm-3 col-form-label">Tipe Customer <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-select @error('customer_type') is-invalid @enderror"
                                            id="customer_type" name="customer_type" required style="width: 100%;">
                                            <option value="" disabled>-- Pilih Tipe Customer --</option>
                                            @php
                                                $selectedCustomerType = old(
                                                    'customer_type',
                                                    $promotionProgram->customer_type,
                                                );
                                            @endphp
                                            <option value="JARINGAN"
                                                {{ $selectedCustomerType == 'JARINGAN' ? 'selected' : '' }}>
                                                JARINGAN</option>
                                            <option value="NON_JARINGAN"
                                                {{ $selectedCustomerType == 'NON_JARINGAN' ? 'selected' : '' }}>
                                                NON JARINGAN</option>
                                            <option value="JARINGAN_DAN_NON_JARINGAN"
                                                {{ $selectedCustomerType == 'JARINGAN_DAN_NON_JARINGAN' ? 'selected' : '' }}>
                                                JARINGAN DAN NON JARINGAN</option>
                                        </select>
                                        @error('customer_type')
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
                                            value="{{ old('effective_start_date', $promotionProgram->effective_start_date->format('Y-m-d')) }}"
                                            required>
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
                                            value="{{ old('effective_end_date', $promotionProgram->effective_end_date->format('Y-m-d')) }}"
                                            required>
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
                                        @php
                                            $selectedStatus = old('is_active', $promotionProgram->is_active);
                                        @endphp
                                        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active"
                                            name="is_active" required style="width: 100%;">
                                            <option value="1" {{ $selectedStatus == '1' ? 'selected' : '' }}>
                                                Berlaku
                                            </option>
                                            <option value="0" {{ $selectedStatus == '0' ? 'selected' : '' }}>
                                                Tidak Berlaku
                                            </option>
                                        </select>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Field: program_description (Textarea) --}}
                                <div class="row mb-3">
                                    <label for="program_description" class="col-sm-3 col-form-label">Deskripsi <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control @error('program_description') is-invalid @enderror" id="program_description"
                                            name="program_description" rows="4" placeholder="Jelaskan detail atau syarat & ketentuan program di sini..."
                                            required>{{ old('program_description', $promotionProgram->program_description) }}</textarea>
                                        @error('program_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            {{-- Section 2: Pilih Item Promosi (Multi-select) --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Pilih Item Promosi</legend>
                                <div class="row mb-3">
                                    <label for="items" class="col-sm-3 col-form-label">Item Promosi <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-select @error('items') is-invalid @enderror" id="items"
                                            name="items[]" multiple="multiple" required style="width: 100%;">
                                            {{-- Pre-populate items yang sudah dipilih --}}
                                            @foreach ($selectedItems as $item)
                                                <option value="{{ $item->MFIMA_ItemID }}" selected>
                                                    {{ $item->MFIMA_ItemID }} - {{ $item->MFIMA_Description }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Ketik minimal 3 huruf ID atau Nama Item untuk mencari.</div>
                                        @error('items')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('items.*')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            {{-- Section 3: Upload File --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Lampiran File (Opsional)</legend>
                                <div class="row mb-3">
                                    <label for="program_file" class="col-sm-3 col-form-label">Ganti File
                                        (PDF)</label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('program_file') is-invalid @enderror"
                                            type="file" id="program_file" name="program_file" accept=".pdf">
                                        <div class="form-text">Kosongkan jika tidak ingin mengubah file lampiran. (Maks:
                                            10MB)</div>
                                        @error('program_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        @if ($promotionProgram->program_file)
                                            <div class="mt-2 form-text">
                                                File saat ini:
                                                <a href="{{ asset('storage/' . $promotionProgram->program_file) }}"
                                                    target="_blank" class="current-file-link">
                                                    <i class="feather-file-text me-1"></i>
                                                    {{ basename($promotionProgram->program_file) }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </fieldset>

                            {{-- Tombol Submit --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="confirmSubmit()">
                                    <i class="feather-save me-2"></i> Simpan Perubahan
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
            // 3. Inisialisasi Select2
            $('#customer_type').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Tipe Customer --',
                width: '100%',
                allowClear: true
            });

            $('#is_active').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Status Berlaku --',
                width: '100%',
                minimumResultsForSearch: Infinity // Sembunyikan search box
            });

            // Ajax Select2 Items
            $('#items').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Ketik ID atau Nama Item --',
                width: '100%',
                allowClear: true,
                closeOnSelect: false,
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('promotion-program.milenia.searchItems') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });
        });

        // 4. Fungsi konfirmasi submit
        function confirmSubmit() {
            const form = document.getElementById('edit-program-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Perubahan Data?',
                text: "Pastikan semua data yang diubah sudah benar.",
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
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menyimpan perubahan data. Mohon tunggu.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    form.submit();
                }
            });
        }
    </script>
@endpush
