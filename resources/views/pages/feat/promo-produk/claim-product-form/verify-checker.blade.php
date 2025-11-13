@extends('layouts.app')

@section('title', 'Verifikasi Klaim: ' . $claim->retail_name)

@push('styles')
    <style>
        /* Style untuk Signature Pad */
        #signature-pad-container {
            position: relative;
            width: 100%;
            height: 250px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            background-color: #ffffff;
        }

        #signature-pad {
            width: 100%;
            height: 100%;
        }

        .signature-error {
            color: #dc3545;
            font-size: 0.875em;
            display: none;
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
                <li class="breadcrumb-item"><a href="{{ route('product-claim-form.landing') }}">Form Klaim Produk</a></li>
                @if ($claim->company_type == 'PT Milenia Mega Mandiri')
                    <li class="breadcrumb-item"><a href="{{ route('product-claim-form.milenia.index') }}">Data Klaim Produk
                            Milenia</a>
                    </li>
                    @else
                    <li class="breadcrumb-item"><a href="{{ route('product-claim-form.map.index') }}">Data Klaim Produk
                            MAP</a>
                    </li>
                @endif
                <li class="breadcrumb-item active">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="#" class="btn btn-secondary">
                <i class="feather-x me-2"></i> Batalkan Verifikasi
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            {{-- Kolom Kiri: Info Utama & Detail Produk (Display Only) --}}
            <div class="col-lg-7">
                <!-- Card Informasi Utama (Display) -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-file-text me-2"></i>Informasi Utama Klaim</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Nama Ritel</dt>
                            <dd class="col-sm-8">{{ $claim->retail_name }}</dd>
                            <dt class="col-sm-4">Tanggal Klaim</dt>
                            <dd class="col-sm-8">{{ \Carbon\Carbon::parse($claim->claim_date)->translatedFormat('d F Y') }}
                            </dd>
                            <hr class="my-2">
                            <dt class="col-sm-4">Sales</dt>
                            <dd class="col-sm-8">{{ $claim->sales->Nama ?? 'N/A' }}</dd>
                            <dt class="col-sm-4">Sales Head</dt>
                            <dd class="col-sm-8">{{ $claim->salesHead->Nama ?? 'N/A' }}</dd>
                            <dt class="col-sm-4">Checker</dt>
                            <dd class="col-sm-8">{{ $claim->checker->Nama ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Card Detail Produk (Display) -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-package me-2"></i>Detail Produk yang Diklaim</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No. Invoice</th>
                                        <th>Produk</th>
                                        <th class="text-center">Qty</th>
                                        <th>Alasan Retur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($claim->claimDetails as $detail)
                                        <tr>
                                            <td>{{ $detail->invoice_id }}</td>
                                            <td>{{ $products[$detail->product_id]->MFIMA_Description ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $detail->quantity }}</td>
                                            <td>{{ $detail->return_reason }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada detail produk.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Form Verifikasi --}}
            <div class="col-lg-5">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-check-square me-2"></i>Formulir Verifikasi</h5>
                    </div>
                    <div class="card-body">
                        <form id="verify-claim-form" action="{{ route('product-claim-form.verify.store', $claim->id) }}"
                            method="POST">
                            @csrf

                            {{-- Input Tersembunyi untuk Tanda Tangan --}}
                            <input type="hidden" name="checker_signature" id="checker_signature">

                            <div class="mb-3">
                                <label for="verification_date" class="form-label">Tanggal Verifikasi <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('verification_date') is-invalid @enderror"
                                    id="verification_date" name="verification_date"
                                    value="{{ old('verification_date', date('Y-m-d')) }}" required>
                                @error('verification_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="verification_result" class="form-label">Hasil Verifikasi <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('verification_result') is-invalid @enderror" id="verification_result"
                                    name="verification_result" rows="4" placeholder="Tulis hasil verifikasi checker di sini..." required>{{ old('verification_result') }}</textarea>
                                @error('verification_result')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="signature-pad" class="form-label">Tanda Tangan Checker <span
                                        class="text-danger">*</span></label>
                                <div id="signature-pad-container">
                                    <canvas id="signature-pad"></canvas>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clear-signature">
                                    <i class="feather-delete me-1"></i> Hapus TTD
                                </button>
                                <span class="signature-error" id="signature-error-msg">Tanda tangan checker wajib
                                    diisi.</span>
                                @error('checker_signature')
                                    <div class="d-block invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="confirmVerify()">
                                    <i class="feather-save me-2"></i> Simpan Verifikasi
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
    {{-- 1. Load SweetAlert2 & SignaturePad Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        // 2. Inisialisasi Signature Pad
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });
        const clearButton = document.getElementById('clear-signature');
        const signatureError = document.getElementById('signature-error-msg');
        const hiddenInput = document.getElementById('checker_signature');
        const form = document.getElementById('verify-claim-form');

        // Fungsi untuk menyesuaikan ukuran canvas
        function resizeCanvas() {
            const container = document.getElementById('signature-pad-container');
            const ratio = Math.max(window.devicePixelRatio || 1, 1);

            canvas.width = container.offsetWidth * ratio;
            canvas.height = container.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);

            // Hapus data (jika ada) saat resize agar tidak rusak
            signaturePad.clear();
        }

        // Panggil resize saat load dan saat window di-resize
        window.addEventListener('resize', resizeCanvas);
        document.addEventListener('DOMContentLoaded', resizeCanvas);

        // Aksi tombol Hapus TTD
        clearButton.addEventListener('click', function() {
            signaturePad.clear();
            signatureError.style.display = 'none';
        });

        // Hapus error saat mulai menulis
        canvas.addEventListener('pointerdown', () => {
            signatureError.style.display = 'none';
        });

        // 3. Fungsi konfirmasi submit
        function confirmVerify() {
            // Cek validasi form bawaan HTML5
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Cek validasi signature pad
            if (signaturePad.isEmpty()) {
                signatureError.style.display = 'block';
                return;
            }

            // Jika valid, masukkan data TTD ke input tersembunyi
            hiddenInput.value = signaturePad.toDataURL('image/png');

            // Tampilkan konfirmasi SweetAlert
            Swal.fire({
                title: 'Konfirmasi Verifikasi?',
                text: "Apakah Anda yakin data verifikasi dan tanda tangan sudah benar?",
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
                        text: 'Sedang menyimpan data verifikasi. Mohon tunggu.',
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
