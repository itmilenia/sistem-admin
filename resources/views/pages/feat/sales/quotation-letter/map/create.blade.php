@extends('layouts.app')

@section('title', 'Tambah Surat Penawaran Baru')

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('quotation-letter.landing') }}">Surat Penawaran</a></li>
                <li class="breadcrumb-item"><a href="{{ route('quotation-letter.map.index') }}">Data Surat Penawaran
                        MAP</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('quotation-letter.map.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-plus-circle me-2"></i>Formulir Data Surat</h5>
                    </div>

                    <div class="card-body">
                        <form id="create-quotation-form" action="{{ route('quotation-letter.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            {{-- Section 1: Detail Surat --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Detail Surat</legend>

                                <div class="row mb-3">
                                    <label for="quotation_letter_number" class="col-sm-3 col-form-label">No. Surat Penawaran
                                        <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text"
                                            class="form-control @error('quotation_letter_number') is-invalid @enderror"
                                            id="quotation_letter_number" name="quotation_letter_number"
                                            value="{{ old('quotation_letter_number') }}" placeholder="Cth. 000/MAP/IX/2025"
                                            required>
                                        @error('quotation_letter_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="letter_type" class="col-sm-3 col-form-label">Tipe Surat <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-select @error('letter_type') is-invalid @enderror"
                                            id="letter_type" name="letter_type" required>
                                            <option value="Map" selected>Map</option>
                                        </select>
                                        @error('letter_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="letter_date" class="col-sm-3 col-form-label">Tanggal Surat <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="date"
                                            class="form-control @error('letter_date') is-invalid @enderror" id="letter_date"
                                            name="letter_date" value="{{ old('letter_date', date('Y-m-d')) }}" required>
                                        @error('letter_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="recipient" class="col-sm-3 col-form-label">Penerima <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control @error('recipient') is-invalid @enderror"
                                            id="recipient" name="recipient" value="{{ old('recipient') }}"
                                            placeholder="Cth. Bayu" required>
                                        @error('recipient')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="subject" class="col-sm-3 col-form-label">Perihal (Subjek) <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-select @error('subject') is-invalid @enderror" id="subject"
                                            name="subject" required>
                                            <option value="" selected disabled>-- Pilih Perihal --</option>
                                            @foreach (['Surat Penawaran Harga'] as $type)
                                                <option value="{{ $type }}"
                                                    {{ old('subject') == $type ? 'selected' : '' }}>{{ $type }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="letter_status" class="col-sm-3 col-form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="form-select @error('letter_status') is-invalid @enderror"
                                            id="letter_status" name="letter_status" required>
                                            <option value="" selected disabled>-- Pilih Status --</option>
                                            @foreach (['Belum Terkirim', 'Sudah Terkirim'] as $type)
                                                <option value="{{ $type }}"
                                                    {{ old('letter_status') == $type ? 'selected' : '' }}>
                                                    {{ $type }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('letter_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            {{-- Section 2: Upload File --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Lampiran File (PDF)</legend>

                                <div class="row mb-3">
                                    <label for="quotation_letter_file" class="col-sm-3 col-form-label">Upload File PDF <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('quotation_letter_file') is-invalid @enderror"
                                            type="file" id="quotation_letter_file" name="quotation_letter_file"
                                            accept=".pdf" required>
                                        <div class="form-text">File harus berupa PDF dan ukuran maksimum 10MB.</div>
                                        @error('quotation_letter_file')
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
    {{-- 1. Load SweetAlert2 Library (Cari CDN yang sesuai jika belum ada di layouts.app) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmSubmit() {
            const form = document.getElementById('create-quotation-form');
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
