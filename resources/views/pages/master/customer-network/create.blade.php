@extends('layouts.app')

@section('title', 'Tambah Jaringan Customer Baru')

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
                <li class="breadcrumb-item"><a href="{{ route('master-customer-network.index') }}">Master Jaringan
                        Customer</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('master-customer-network.index') }}" class="btn btn-secondary">
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
                        <h5 class="card-title"><i class="feather-plus-circle me-2"></i>Formulir Jaringan Customer</h5>
                    </div>

                    <div class="card-body">
                        {{-- Form --}}
                        <form id="networkCustomerStoreForm" action="{{ route('master-customer-network.store') }}"
                            method="POST">
                            @csrf

                            {{-- Fieldset --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Detail Customer</legend>

                                {{-- Field 1: Nama Customer --}}
                                <div class="row mb-3">
                                    <label for="nc-name" class="col-sm-3 col-form-label">Nama Customer <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                name="name" id="nc-name" placeholder="Contoh: PT Indonesia Cemerlang"
                                                value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Field 2: Kategori --}}
                                <div class="row mb-3">
                                    <label for="nc-category" class="col-sm-3 col-form-label">Kategori <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <select class="form-select @error('category') is-invalid @enderror"
                                                id="nc-category" name="category[]" multiple required>
                                                @php
                                                    $oldCategories = old('category', []); // kosong default
                                                    if (!is_array($oldCategories)) {
                                                        $oldCategories = [$oldCategories];
                                                    }
                                                    $oldCategories = array_map('strtoupper', $oldCategories);
                                                @endphp
                                                <option value="ULTIME" @if (in_array('ULTIME', $oldCategories)) selected @endif>
                                                    ULTIME</option>
                                                <option value="MAD" @if (in_array('MAD', $oldCategories)) selected @endif>MAD
                                                </option>
                                                <option value="POD" @if (in_array('POD', $oldCategories)) selected @endif>POD
                                                </option>
                                                <option value="KEY_ACCOUNT"
                                                    @if (in_array('KEY_ACCOUNT', $oldCategories)) selected @endif>KEY ACCOUNT</option>
                                                <option value="RESSELER" @if (in_array('RESSELER', $oldCategories)) selected @endif>
                                                    RESSELER</option>
                                                <option value="SAD" @if (in_array('SAD', $oldCategories)) selected @endif>SAD
                                                </option>
                                            </select>
                                            <small class="text-muted">Bisa pilih lebih dari satu kategori.</small>
                                            @error('category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Field 3: Brand --}}
                                <div class="row mb-3">
                                    <label for="nc-brands" class="col-sm-3 col-form-label">Brand <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <select class="form-select @error('brand_id') is-invalid @enderror"
                                                id="nc-brands" name="brand_id[]" multiple required>
                                                @foreach ($productBrands as $pb)
                                                    <option value="{{ $pb->id }}"
                                                        @if (is_array(old('brand_id')) && in_array($pb->id, old('brand_id'))) selected @endif>
                                                        {{ $pb->brand_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Bisa pilih lebih dari satu brand.</small>
                                    </div>
                                </div>

                                {{-- Field 4: Status --}}
                                <div class="row mb-3">
                                    <label for="nc-status" class="col-sm-3 col-form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <select class="form-select @error('is_active') is-invalid @enderror"
                                                id="nc-status" name="is_active" required>
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
                                    <i class="feather-save me-2"></i> Simpan Customer
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
    {{-- 2. Load Select2 Library --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- 3. Inisialisasi Select2 --}}
    <script>
        $(document).ready(function() {
            $('#nc-category').select2({
                placeholder: 'Pilih Kategori',
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: true
            });

            $('#nc-brands').select2({
                placeholder: 'Pilih Brand',
                theme: 'bootstrap-5',
                width: '10%',
                allowClear: true
            });
        });
    </script>

    {{-- 4. Konfirmasi Submit --}}
    <script>
        function confirmSubmit() {
            const form = document.getElementById('networkCustomerStoreForm');
            // Validasi HTML5
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Yakin ingin menambahkan customer ini?',
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

    {{-- 5. Toggle ikon status --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('nc-status');
            const statusIconElement = document.getElementById('nc-status-icon-i');

            function updateStatusIcon() {
                const selectedValue = statusSelect.value;
                statusIconElement.classList.remove('feather-toggle-right', 'feather-toggle-left', 'text-success',
                    'text-danger');

                if (selectedValue === '1') {
                    statusIconElement.classList.add('feather-toggle-right', 'text-success');
                } else {
                    statusIconElement.classList.add('feather-toggle-left', 'text-danger');
                }
            }

            statusSelect.addEventListener('change', updateStatusIcon);
            updateStatusIcon();
        });
    </script>
@endpush
