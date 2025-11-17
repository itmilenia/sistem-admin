@extends('layouts.app')

@section('title', 'Edit Pajak')

@section('content')
    <x-alert />

    {{-- Breadcrumb --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Manajemen Fitur</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master-tax.index') }}">Master Pajak</a>
                    </li>
                    <li class="breadcrumb-item">@yield('title')</li>
                </ul>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('master-tax.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-edit me-2"></i>Formulir Edit Pajak</h5>
                    </div>

                    <div class="card-body">
                        {{-- Form --}}
                        <form id="taxUpdateForm" action="{{ route('master-tax.update', $tax->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            {{-- Fieldset --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Detail Pajak</legend>

                                {{-- Field 1: Nama Pajak --}}
                                <div class="row mb-3">
                                    <label for="tax-name" class="col-sm-3 col-form-label">Nama Pajak <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('tax_name') is-invalid @enderror" name="tax_name"
                                                id="tax-name" placeholder="Contoh: PPN 11%, PPN 10%, dsb."
                                                value="{{ old('tax_name', $tax->tax_name) }}" required>
                                            @error('tax_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Field 2: Tarif Pajak (%) --}}
                                <div class="row mb-3">
                                    <label for="tax-rate" class="col-sm-3 col-form-label">Tarif Pajak <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0"
                                                class="form-control @error('tax_rate') is-invalid @enderror" name="tax_rate"
                                                id="tax-rate" placeholder="Contoh: 11.00"
                                                value="{{ old('tax_rate', $tax->tax_rate) }}" required>
                                            <span class="input-group-text">%</span>
                                            @error('tax_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Gunakan titik (.) untuk desimal, misal 10.50</small>
                                    </div>
                                </div>


                                {{-- Field 3: Status --}}
                                <div class="row mb-3">
                                    <label for="tax-status" class="col-sm-3 col-form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <select class="form-select @error('is_active') is-invalid @enderror"
                                                id="tax-status" name="is_active" required>
                                                <option value="1" @selected(old('is_active', $tax->is_active) == '1')>Aktif</option>
                                                <option value="0" @selected(old('is_active', $tax->is_active) == '0')>Tidak Aktif</option>
                                            </select>
                                            @error('is_active')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </fieldset>

                            {{-- Tombol Submit --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="confirmSubmit()">
                                    <i class="feather-save me-2"></i> Update Pajak
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

    {{-- 2. Konfirmasi Submit --}}
    <script>
        function confirmSubmit() {
            const form = document.getElementById('taxUpdateForm');
            // Validasi HTML5
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                // Teks diubah
                title: 'Yakin ingin memperbarui pajak ini?',
                text: "Pastikan data sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, update!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memperbarui data. Mohon tunggu.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        }
    </script>
@endpush
