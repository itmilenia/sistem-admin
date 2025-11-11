@extends('layouts.app')

@section('title', 'Tambah Product Brand Baru')

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
                    <li class="breadcrumb-item"><a href="{{ route('master-product-brand.index') }}">Master Product Brand</a>
                    </li>
                    <li class="breadcrumb-item">@yield('title')</li>
                </ul>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('master-product-brand.index') }}" class="btn btn-secondary">
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
                        <h5 class="card-title"><i class="feather-plus-circle me-2"></i>Formulir Product Brand</h5>
                    </div>

                    <div class="card-body">
                        {{-- Form --}}
                        <form id="productBrandStoreForm" action="{{ route('master-product-brand.store') }}" method="POST">
                            @csrf

                            {{-- Fieldset --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Detail Brand</legend>

                                {{-- Field 1: Nama Brand --}}
                                <div class="row mb-3">
                                    <label for="pb-brand-name" class="col-sm-3 col-form-label">Nama Brand <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('brand_name') is-invalid @enderror"
                                                name="brand_name" id="pb-brand-name"
                                                placeholder="Contoh: Ultima II, MAD, dsb." value="{{ old('brand_name') }}"
                                                required>
                                            @error('brand_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Field 2: Company Type --}}
                                <div class="row mb-3">
                                    <label for="pb-company-type" class="col-sm-3 col-form-label">Jenis Perusahaan <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <select class="form-select @error('company_type') is-invalid @enderror"
                                                id="pb-company-type" name="company_type" required>
                                                <option value="PT Mega Auto Prima" @selected(old('company_type', 'PT Mega Auto Prima') == 'PT Mega Auto Prima')>PT Mega Auto Prima</option>
                                                <option value="PT Milenia Mega Mandiri" @selected(old('company_type', 'PT Milenia Mega Mandiri') == 'PT Milenia Mega Mandiri')>PT Milenia Mega Mandiri</option>
                                            </select>
                                            @error('company_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Field 3: Status --}}
                                <div class="row mb-3">
                                    <label for="pb-status" class="col-sm-3 col-form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <select class="form-select @error('is_active') is-invalid @enderror"
                                                id="pb-status" name="is_active" required>
                                                <option value="1" @selected(old('is_active', '1') == '1')>Aktif</option>
                                                <option value="0" @selected(old('is_active', '1') == '0')>Tidak Aktif</option>
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
                                    <i class="feather-save me-2"></i> Simpan Brand
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
            const form = document.getElementById('productBrandStoreForm');
            // Validasi HTML5
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Yakin ingin menambahkan brand ini?',
                text: "Pastikan data sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, tambah!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menyimpan data. Mohon tunggu.',
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
