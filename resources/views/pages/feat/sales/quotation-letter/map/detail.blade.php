@extends('layouts.app')

@section('title', 'Detail Surat Penawaran: ' . $quotationLetter->quotation_letter_number)

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
            {{-- KOLOM KIRI: Informasi Utama --}}
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-file-text me-2"></i>Header Surat</h5>
                        {{-- Tombol Download File Asli (Jika ada file hasil generate/upload) --}}
                        @if ($quotationLetter->quotation_letter_file)
                            <a href="{{ Storage::url($quotationLetter->quotation_letter_file) }}" target="_blank"
                                class="btn btn-primary btn-sm">
                                <i class="feather-download me-2"></i> Unduh File
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Info Surat --}}
                            <div class="col-md-6">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Nomor Surat</dt>
                                    <dd class="col-sm-8 fw-bold text-primary">
                                        {{ $quotationLetter->quotation_letter_number }}</dd>

                                    <dt class="col-sm-4">Tanggal</dt>
                                    <dd class="col-sm-8">
                                        {{ \Carbon\Carbon::parse($quotationLetter->letter_date)->translatedFormat('d F Y') }}
                                    </dd>

                                    <dt class="col-sm-4">Perihal</dt>
                                    <dd class="col-sm-8">{{ $quotationLetter->subject }}</dd>

                                    <dt class="col-sm-4">Tipe</dt>
                                    <dd class="col-sm-8"><span
                                            class="badge bg-success">{{ $quotationLetter->letter_type }}</span></dd>
                                </dl>
                            </div>

                            {{-- Info Penerima (Detail Baru) --}}
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2">Tujuan Penerima</h6>
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Perusahaan</dt>
                                        <dd class="col-sm-8 fw-bold">{{ $quotationLetter->recipient_company_name }}</dd>

                                        <dt class="col-sm-4">U.P.</dt>
                                        <dd class="col-sm-8">{{ $quotationLetter->recipient_attention_to }}</dd>

                                        <dt class="col-sm-4">Alamat</dt>
                                        <dd class="col-sm-8">
                                            {{ $quotationLetter->recipient_address_line1 }}<br>
                                            @if ($quotationLetter->recipient_address_line2)
                                                {{ $quotationLetter->recipient_address_line2 }}<br>
                                            @endif
                                            {{ $quotationLetter->recipient_city }},
                                            {{ $quotationLetter->recipient_province }} -
                                            {{ $quotationLetter->recipient_postal_code }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM TENGAH: Detail Items --}}
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-shopping-cart me-2"></i>Detail Barang / Item</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center" width="5%">#</th>
                                        <th class="text-center" width="25%">Item ID</th>
                                        <th class="text-center" width="10%">SKU</th>
                                        <th class="text-center" width="10%">Size</th>
                                        <th class="text-center" width="10%">Warranty</th>
                                        <th class="text-center" width="10%">Item Type</th>
                                        <th class="text-center" width="15%">Harga Satuan</th>
                                        <th class="text-center" width="10%">Disc (%)</th>
                                        <th class="text-center" width="15%">Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @forelse($quotationLetter->details as $index => $item)
                                        @php $grandTotal += $item->total_price; @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-bold">{{ $item->itemMap->MFIMA_Description ?? $item->item_id }}
                                            </td>
                                            <td class="text-center">{{ $item->sku_number }}</td>
                                            <td class="text-center">{{ $item->size_number ?? '-' }}</td>
                                            <td class="text-center">{{ $item->warranty_period ?? '-' }}</td>
                                            <td class="text-center">{{ $item->item_type ?? '-' }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($item->unit_price * (1 + $taxRate / 100), 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">{{ $item->discount_percentage + 0 }}%</td>
                                            {{-- +0 agar desimal .00 hilang --}}
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-3">Tidak ada item barang.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8" class="text-center fw-bold text-uppercase">Total Akhir (Termasuk
                                            PPN)</td>
                                        <td class="text-end fw-bold text-primary fs-6">Rp
                                            {{ number_format($grandTotal, 2, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM BAWAH: Note & Signature --}}
            <div class="col-lg-12 mt-4">
                <div class="row">
                    {{-- Catatan --}}
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title"><i class="feather-edit-3 me-2"></i>Catatan Surat</h5>
                            </div>
                            <div class="card-body">
                                @if ($quotationLetter->letter_note)
                                    <div class="trix-content p-3 bg-light rounded border">
                                        {!! $quotationLetter->letter_note !!}
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">- Tidak ada catatan -</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Tanda Tangan --}}
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title"><i class="feather-pen-tool me-2"></i>Otorisasi</h5>
                            </div>
                            <div class="card-body text-center">
                                <p class="mb-2 text-muted small">Penanda Tangan:</p>
                                <div class="border rounded p-2 bg-light d-inline-block"
                                    style="min-width: 200px; min-height: 120px;">
                                    @if ($quotationLetter->signature_path)
                                        <img src="{{ Storage::url($quotationLetter->signature_path) }}"
                                            alt="Tanda Tangan" class="img-fluid" style="max-height: 150px;">
                                    @else
                                        <div
                                            class="d-flex align-items-center justify-content-center h-100 text-muted small">
                                            - Belum TTD -
                                        </div>
                                    @endif
                                </div>
                                <h6 class="fw-bold mt-3">{{ $quotationLetter->signer->Nama ?? 'Unknown User' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER: Audit Info --}}
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-info me-2"></i>Informasi Audit</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-2">Dibuat Oleh</dt>
                            <dd class="col-sm-4">{{ $quotationLetter->creator->Nama ?? '-' }}</dd>

                            <dt class="col-sm-2">Dibuat Pada</dt>
                            <dd class="col-sm-4">
                                {{ \Carbon\Carbon::parse($quotationLetter->created_at)->translatedFormat('d F Y H:i') }}
                            </dd>

                            <dt class="col-sm-2">Update Terakhir</dt>
                            <dd class="col-sm-4">{{ $quotationLetter->updater->Nama ?? '-' }}</dd>

                            <dt class="col-sm-2">Waktu Update</dt>
                            <dd class="col-sm-4">
                                {{ $quotationLetter->updated_at ? \Carbon\Carbon::parse($quotationLetter->updated_at)->translatedFormat('d F Y H:i') : '-' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
