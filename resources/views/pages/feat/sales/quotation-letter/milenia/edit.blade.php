@extends('layouts.app')

@section('title', 'Edit Surat Penawaran: ' . $quotationLetter->quotation_letter_number)

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('quotation-letter.landing') }}">Surat Penawaran</a></li>
                <li class="breadcrumb-item"><a href="{{ route('quotation-letter.milenia.index') }}">Data Surat Penawaran
                        Milenia</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('quotation-letter.milenia.index') }}" class="btn btn-secondary ms-2">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-edit me-2"></i>Formulir Edit Data Surat</h5>
                    </div>

                    <div class="card-body">
                        {{-- Form action diarahkan ke method update dengan method spoofing PUT --}}
                        <form id="edit-quotation-form" action="{{ route('quotation-letter.update', $quotationLetter->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

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
                                            value="{{ old('quotation_letter_number', $quotationLetter->quotation_letter_number) }}"
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
                                        {{-- Sesuai dengan StoreRequest: Penawaran, Kontrak, Internal --}}
                                        <select class="form-select @error('letter_type') is-invalid @enderror"
                                            id="letter_type" name="letter_type" required>
                                            <option value="" selected disabled>-- Pilih Tipe --</option>
                                            @foreach (['Milenia'] as $type)
                                                <option value="{{ $type }}"
                                                    {{ old('letter_type', $quotationLetter->letter_type) == $type ? 'selected' : '' }}>
                                                    {{ $type }}</option>
                                            @endforeach
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
                                            name="letter_date"
                                            value="{{ old('letter_date', \Carbon\Carbon::parse($quotationLetter->letter_date)->format('Y-m-d')) }}"
                                            required>
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
                                            id="recipient" name="recipient"
                                            value="{{ old('recipient', $quotationLetter->recipient) }}" required>
                                        @error('recipient')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="subject" class="col-sm-3 col-form-label">Perihal (Subject) <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        {{-- Menyamakan pilihan dengan create.blade.php yang Anda berikan --}}
                                        <select class="form-select @error('subject') is-invalid @enderror" id="subject"
                                            name="subject" required>
                                            <option value="" selected disabled>-- Pilih Perihal --</option>
                                            @foreach (['Surat Penawaran Harga'] as $type)
                                                <option value="{{ $type }}"
                                                    {{ old('subject', $quotationLetter->subject) == $type ? 'selected' : '' }}>
                                                    {{ $type }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="letter_status" class="col-sm-3 col-form-label">Status</label>
                                    <div class="col-sm-9">
                                        {{-- Menyamakan pilihan dengan create.blade.php yang Anda berikan --}}
                                        <select class="form-select @error('letter_status') is-invalid @enderror"
                                            id="letter_status" name="letter_status">
                                            <option value="Belum Terkirim"
                                                {{ old('letter_status', $quotationLetter->letter_status) == 'Belum Terkirim' ? 'selected' : '' }}>
                                                Belum Terkirim</option>
                                            <option value="Sudah Terkirim"
                                                {{ old('letter_status', $quotationLetter->letter_status) == 'Sudah Terkirim' ? 'selected' : '' }}>
                                                Sudah Terkirim</option>
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

                                @if ($quotationLetter->quotation_letter_file)
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="feather-file-text me-2"></i>
                                        File saat ini:
                                        <a href="{{ Storage::url($quotationLetter->quotation_letter_file) }}"
                                            target="_blank" class="ms-1 fw-bold text-decoration-underline">
                                            {{ basename($quotationLetter->quotation_letter_file) }}
                                        </a>
                                        <span class="ms-auto text-sm">(Abaikan upload jika tidak ingin mengganti
                                            file.)</span>
                                    </div>
                                @endif

                                <div class="row mb-3">
                                    <label for="quotation_letter_file" class="col-sm-3 col-form-label">Ganti File
                                        PDF</label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('quotation_letter_file') is-invalid @enderror"
                                            type="file" id="quotation_letter_file" name="quotation_letter_file"
                                            accept=".pdf">
                                        <div class="form-text">Maksimal 2MB. Jika file lama sudah ada dan ini dikosongkan,
                                            file lama akan dipertahankan.</div>
                                        @error('quotation_letter_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            {{-- Tombol Submit --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="confirmUpdateSubmit()">
                                    <i class="feather-check-circle me-2"></i> Perbarui Surat
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
        function confirmUpdateSubmit() {
            // Periksa validitas formulir HTML5 native (misalnya: required fields)
            const form = document.getElementById('edit-quotation-form');
            if (!form.checkValidity()) {
                // Jika tidak valid, panggil event submit untuk memicu pesan error native browser
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Perubahan Data?',
                text: "Anda akan memperbarui data Surat Penawaran ini. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Perbarui!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false // Penting untuk mengaktifkan customClass
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user mengkonfirmasi, submit formulir
                    form.submit();

                    // Tampilkan loading screen saat formulir diproses
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memperbarui data. Mohon tunggu.',
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
