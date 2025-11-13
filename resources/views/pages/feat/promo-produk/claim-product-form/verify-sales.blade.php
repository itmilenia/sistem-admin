@extends('layouts.app')

{{-- 1. Ubah Judul --}}
@section('title', 'Tanda Tangan Sales: ' . $claim->retail_name)

@push('styles')
    <style>
        /* Style untuk Signature Pad (Sama) */
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
            {{-- 3. Ubah Link "Batalkan" --}}
            <a href="{{ $claim->company_type == 'PT Milenia Mega Mandiri' ? route('product-claim-form.milenia.index') : route('product-claim-form.map.index') }}"
                class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Index
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            {{-- Kolom Kiri: Info Utama & Detail Produk (Display Only) --}}
            {{-- (Ini sama persis, tidak perlu diubah) --}}
            <div class="col-lg-7">
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

            {{-- Kolom Kanan: Form Tanda Tangan --}}
            <div class="col-lg-5">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        {{-- 4. Ubah Judul Card --}}
                        <h5 class="card-title"><i class="feather-edit me-2"></i>Tanda Tangan Sales</h5>
                    </div>
                    <div class="card-body">
                        {{-- 5. Ubah Form Action dan ID Form --}}
                        <form id="sales-signature-form"
                            action="{{ route('product-claim-form.sales-signature.store', $claim->id) }}" method="POST">
                            @csrf

                            {{-- 6. Ubah Input Tersembunyi --}}
                            <input type="hidden" name="sales_signature" id="sales_signature">

                            {{-- 8. Sesuaikan Blok Tanda Tangan --}}
                            <div class="mb-3">
                                <label for="signature-pad" class="form-label">Tanda Tangan Sales
                                    ({{ Auth::user()->Nama ?? 'N/A' }}) <span class="text-danger">*</span></label>
                                <div id="signature-pad-container">
                                    <canvas id="signature-pad"></canvas>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clear-signature">
                                    <i class="feather-delete me-1"></i> Hapus TTD
                                </button>
                                <span class="signature-error" id="signature-error-msg">Tanda tangan sales wajib
                                    diisi.</span>
                                @error('sales_signature')
                                    <div class="d-block invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            <div class="d-flex justify-content-end">
                                {{-- 9. Ubah Tombol Submit dan panggil JS --}}
                                <button type="button" class="btn btn-primary" onclick="confirmSignature()">
                                    <i class="feather-save me-2"></i> Simpan Tanda Tangan
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
    {{-- (Load SweetAlert2 & SignaturePad Libraries - Sama) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        // 10. Sesuaikan Inisialisasi JS
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });
        const clearButton = document.getElementById('clear-signature');
        const signatureError = document.getElementById('signature-error-msg');

        // (Ubah ini)
        const hiddenInput = document.getElementById('sales_signature');
        const form = document.getElementById('sales-signature-form');

        // (Fungsi resizeCanvas - SAMA PERSIS, tidak perlu diubah)
        function resizeCanvas() {
            const container = document.getElementById('signature-pad-container');
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = container.offsetWidth * ratio;
            canvas.height = container.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }
        window.addEventListener('resize', resizeCanvas);
        document.addEventListener('DOMContentLoaded', resizeCanvas);

        // (Aksi tombol Hapus TTD - SAMA PERSIS)
        clearButton.addEventListener('click', function() {
            signaturePad.clear();
            signatureError.style.display = 'none';
        });

        // (Hapus error saat mulai menulis - SAMA PERSIS)
        canvas.addEventListener('pointerdown', () => {
            signatureError.style.display = 'none';
        });

        // 11. Sesuaikan Fungsi konfirmasi submit
        function confirmSignature() { // (Nama fungsi diubah)

            // (Validasi HTML5 - SAMA)
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // (Validasi signature pad - SAMA)
            if (signaturePad.isEmpty()) {
                signatureError.style.display = 'block';
                return;
            }

            // (Masukkan data TTD ke input - SAMA)
            hiddenInput.value = signaturePad.toDataURL('image/png');

            // (Tampilkan konfirmasi SweetAlert - Ubah Teks)
            Swal.fire({
                title: 'Konfirmasi Tanda Tangan?',
                text: "Apakah Anda yakin tanda tangan Anda sudah benar?", // (Teks diubah)
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
                        text: 'Sedang menyimpan tanda tangan. Mohon tunggu.', // (Teks diubah)
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
