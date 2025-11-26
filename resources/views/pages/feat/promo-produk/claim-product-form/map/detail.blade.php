@extends('layouts.app')

{{-- Judul akan dinamis berdasarkan nama ritel --}}
@section('title', 'Detail Klaim Produk: ' . $claim->retail_name)

@section('content')
    <x-alert />

    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product-claim-form.landing') }}">Form Klaim Produk</a></li>
                <li class="breadcrumb-item"><a href="{{ route('product-claim-form.map.index') }}">Data Klaim Produk
                        MAP</a>
                </li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('product-claim-form.map.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            {{-- Kolom Kiri: Info Utama & Tanda Tangan --}}
            <div class="col-lg-5">
                <!-- Card Informasi Utama -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-file-text me-2"></i>Informasi Utama Klaim</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Tipe Perusahaan</dt>
                            <dd class="col-sm-8"><strong>{{ $claim->company_type }}</strong></dd>

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

                            <hr class="my-2">

                            <dt class="col-sm-4">Status Verifikasi</dt>
                            <dd class="col-sm-8">
                                @if ($claim->verification_date)
                                    <span class="badge bg-success">Terverifikasi</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </dd>

                            @if ($claim->verification_date)
                                <dt class="col-sm-4">Tanggal Verifikasi</dt>
                                <dd class="col-sm-8">
                                    {{ \Carbon\Carbon::parse($claim->verification_date)->translatedFormat('d F Y') }}</dd>
                            @endif

                            @if ($claim->verification_result)
                                <dt class="col-sm-4">Hasil Verifikasi</dt>
                                <dd class="col-sm-8">{{ $claim->verification_result }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Card Tanda Tangan -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-edit-3 me-2"></i>Tanda Tangan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h6>Checker</h6>
                                @if ($claim->checker_signature_path)
                                    <img src="{{ Storage::url($claim->checker_signature_path) }}" alt="TTD Checker"
                                        class="img-thumbnail" style="max-height: 150px;">
                                @else
                                    <div class="p-3 border rounded bg-light text-muted"
                                        style="height: 150px; display: flex; align-items: center; justify-content: center;">
                                        (Belum TTD)
                                    </div>
                                @endif
                                <small class="d-block mt-1">{{ $claim->checker->Nama ?? '' }}</small>
                            </div>
                            <div class="col-4">
                                <h6>Sales</h6>
                                @if ($claim->sales_signature_path)
                                    <img src="{{ Storage::url($claim->sales_signature_path) }}" alt="TTD Sales"
                                        class="img-thumbnail" style="max-height: 150px;">
                                @else
                                    <div class="p-3 border rounded bg-light text-muted"
                                        style="height: 150px; display: flex; align-items: center; justify-content: center;">
                                        (Belum TTD)
                                    </div>
                                @endif
                                <small class="d-block mt-1">{{ $claim->sales->Nama ?? '' }}</small>
                            </div>
                            <div class="col-4">
                                <h6>Sales Head</h6>
                                @if ($claim->sales_head_signature_path)
                                    <img src="{{ Storage::url($claim->sales_head_signature_path) }}" alt="TTD Sales Head"
                                        class="img-thumbnail" style="max-height: 150px;">
                                @else
                                    <div class="p-3 border rounded bg-light text-muted"
                                        style="height: 150px; display: flex; align-items: center; justify-content: center;">
                                        (Belum TTD)
                                    </div>
                                @endif
                                <small class="d-block mt-1">{{ $claim->salesHead->Nama ?? '' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Kolom Kanan: Detail Produk & Audit --}}
            <div class="col-lg-7">
                <!-- Card Detail Produk -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-package me-2"></i>Detail Produk yang Diklaim</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center align-middle">#</th>
                                        <th class="text-center align-middle">No. Invoice</th>
                                        <th class="text-center align-middle">Produk</th>
                                        <th class="text-center align-middle">Gambar/Video</th>
                                        <th class="text-center align-middle">Qty</th>
                                        <th class="text-center align-middle">Tgl. Order</th>
                                        <th class="text-center align-middle">Tgl. Pengiriman</th>
                                        <th class="text-center align-middle">Alasan Retur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($claim->claimDetails as $detail)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $detail->invoice_id }}</td>
                                            <td>
                                                {{ $products[$detail->product_id]->MFIMA_Description ?? 'N/A (ID: ' . $detail->product_id . ')' }}
                                            </td>
                                            <td class="text-center" style="width: 100px;">
                                                @php
                                                    $extension = $detail->product_image
                                                        ? pathinfo($detail->product_image, PATHINFO_EXTENSION)
                                                        : '';
                                                    $isVideo = in_array(strtolower($extension), [
                                                        'mp4',
                                                        'mov',
                                                        'avi',
                                                        'wmv',
                                                    ]);
                                                @endphp
                                                @if ($detail->product_image)
                                                    @if ($isVideo)
                                                        <video width="150" height="100" controls>
                                                            <source src="{{ Storage::url($detail->product_image) }}"
                                                                type="video/{{ $extension == 'mov' ? 'quicktime' : $extension }}">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    @else
                                                        <a href="{{ Storage::url($detail->product_image) }}"
                                                            target="_blank" title="Klik untuk perbesar">
                                                            <img src="{{ Storage::url($detail->product_image) }}"
                                                                alt="Gambar Produk" class="img-thumbnail"
                                                                style="width: 80px; height: 80px;">
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $detail->quantity }}</td>
                                            <td>{{ \Carbon\Carbon::parse($detail->order_date)->translatedFormat('d M Y') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($detail->delivery_date)->translatedFormat('d M Y') }}
                                            </td>
                                            <td>{{ $detail->return_reason }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Tidak ada detail produk
                                                untuk
                                                klaim ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Card Informasi Audit -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-info me-2"></i>Informasi Audit</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Dibuat Oleh</dt>
                            <dd class="col-sm-8">{{ $claim->createdBy->Nama ?? '-' }}</dd>

                            <dt class="col-sm-4">Dibuat Pada</dt>
                            <dd class="col-sm-8">
                                {{ \Carbon\Carbon::parse($claim->created_at)->translatedFormat('d F Y, H:i') }}</dd>

                            <dt class="col-sm-4">Terakhir Diperbarui Oleh</dt>
                            <dd class="col-sm-8">{{ $claim->updatedBy->Nama ?? '-' }}</dd>

                            <dt class="col-sm-4">Terakhir Diperbarui Pada</dt>
                            <dd class="col-sm-8">
                                {{ \Carbon\Carbon::parse($claim->updated_at)->translatedFormat('d F Y, H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
