@extends('layouts.app')

{{-- Judul dinamis berdasarkan nama program --}}
@section('title', 'Detail Program Promosi: ' . $promotionProgram->program_name)

@push('styles')
    <style>
        /* Memberi jarak antar card */
        .card+.card {
            margin-top: 1.5rem;
        }

        /* Styling untuk description list agar lebih rapi */
        .info-list dt {
            font-weight: 600;
            color: #555;
        }

        .info-list dd {
            color: #333;
        }
    </style>
@endpush

@section('content')
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
            {{-- Card 1: Informasi Utama Program Promosi --}}
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-award me-2"></i>Informasi Program Promosi</h5>
                        @if ($promotionProgram->program_file)
                            <a href="{{ Storage::url($promotionProgram->program_file) }}" target="_blank"
                                class="btn btn-primary btn-sm">
                                <i class="feather-download me-2"></i> Unduh Lampiran
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <dl class="row info-list">
                            {{-- Baris 1: Nama Program & Tipe Customer --}}
                            <dt class="col-sm-3">Nama Program</dt>
                            <dd class="col-sm-9">
                                <strong>{{ $promotionProgram->program_name }}</strong>
                            </dd>

                            <dt class="col-sm-3">Tipe Customer</dt>
                            <dd class="col-sm-9">{{ str_replace('_', ' ', $promotionProgram->customer_type) }}</dd>

                            {{-- Baris 2: Periode Efektif & Status --}}
                            <dt class="col-sm-3">Periode Efektif</dt>
                            <dd class="col-sm-9">
                                {{ \Carbon\Carbon::parse($promotionProgram->effective_start_date)->translatedFormat('d F Y') }}
                                -
                                {{ \Carbon\Carbon::parse($promotionProgram->effective_end_date)->translatedFormat('d F Y') }}
                            </dd>

                            <dt class="col-sm-3">Status Program</dt>
                            <dd class="col-sm-9">
                                @if ($promotionProgram->is_active == 1)
                                    <span class="badge bg-success">Berlaku</span>
                                @else
                                    <span class="badge bg-danger">Tidak Berlaku</span>
                                @endif
                            </dd>

                            {{-- Baris 3: Deskripsi --}}
                            <dt class="col-sm-3">Deskripsi</dt>
                            <dd class="col-sm-9">
                                {!! nl2br(e($promotionProgram->program_description)) !!}
                            </dd>

                            {{-- Baris 4: File Lampiran --}}
                            <dt class="col-sm-3">File Lampiran</dt>
                            <dd class="col-sm-9">
                                @if ($promotionProgram->program_file)
                                    <a href="{{ Storage::url($promotionProgram->program_file) }}" target="_blank">
                                        {{-- Menampilkan nama file saja --}}
                                        {{ basename($promotionProgram->program_file) }}
                                    </a>
                                @else
                                    <span class="text-muted">- Tidak ada lampiran -</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Card 2: Daftar Item Termasuk Promosi --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-list me-2"></i>Daftar Item Dalam Program
                            ({{ $promotionProgram->details->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;" class="text-center">#</th>
                                        <th class="text-center">Item ID</th>
                                        <th class="text-center">Nama Item</th>
                                        <th class="text-center">Brand</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($promotionProgram->details as $detail)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">
                                                {{ $detail->itemMilenia->MFIMA_ItemID ?? $detail->item_id }}</td>
                                            <td>{{ $detail->itemMilenia->MFIMA_Description ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $detail->itemMilenia->mileniaBrands->MFIB_Description ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="4" class="text-center text-muted py-4">
                                                Belum ada item yang ditambahkan ke program ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Card BARU: Tampilan PDF Lampiran --}}
                @if ($promotionProgram->program_file)
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title"><i class="feather-file-text me-2"></i>Tampilan Lampiran</h5>
                            {{-- Tombol unduh di sini juga untuk akses mudah --}}
                            <a href="{{ Storage::url($promotionProgram->program_file) }}" target="_blank"
                                class="btn btn-primary btn-sm">
                                <i class="feather-download me-2"></i> Unduh / Buka di Tab Baru
                            </a>
                        </div>
                        <div class="card-body p-0" style="height: 800px; overflow: hidden;">
                            <iframe src="{{ Storage::url($promotionProgram->program_file) }}" width="100%" height="100%"
                                style="border: none;" title="Tampilan PDF Program Promosi">
                                <p class="p-3 text-muted">
                                    Browser Anda tidak mendukung tampilan PDF inline.
                                    Silakan <a href="{{ Storage::url($promotionProgram->program_file) }}"
                                        target="_blank">unduh file</a> untuk melihatnya.
                                </p>
                            </iframe>
                        </div>
                    </div>
                @endif
                {{-- Akhir Card BARU --}}


                {{-- Card 3: Informasi Audit --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-info me-2"></i>Informasi Audit</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row info-list">
                            <dt class="col-sm-3">Dibuat Oleh</dt>
                            <dd class="col-sm-9">{{ $promotionProgram->createdBy->Nama ?? '-' }}</dd>

                            <dt class="col-sm-3">Dibuat Pada</dt>
                            <dd class="col-sm-9">
                                {{ \Carbon\Carbon::parse($promotionProgram->created_at)->translatedFormat('d F Y, H:i') }}
                                WIB
                            </dd>

                            <dt class="col-sm-3">Terakhir Diperbarui Oleh</dt>
                            <dd class="col-sm-9">{{ $promotionProgram->updatedBy->Nama ?? '-' }}</dd>

                            <dt class="col-sm-3">Terakhir Diperbarui Pada</dt>
                            <dd class="col-sm-9">
                                {{ \Carbon\Carbon::parse($promotionProgram->updated_at)->translatedFormat('d F Y, H:i') }}
                                WIB
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
