@extends('layouts.app')

{{-- Judul akan dinamis berdasarkan nama customer --}}
@section('title', 'Detail Surat Agreement: ' . ($agreementLetter->customer->name ?? $agreementLetter->id))

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('agreement-letter.landing') }}">Surat Agreement</a></li>
                <li class="breadcrumb-item"><a href="{{ route('agreement-letter.map.index') }}">Data Surat Agreement
                        MAP</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('agreement-letter.map.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-file-text me-2"></i>Informasi Surat Agreement</h5>
                        @if ($agreementLetter->agreement_letter_path)
                            <a href="{{ Storage::url($agreementLetter->agreement_letter_path) }}" target="_blank"
                                class="btn btn-primary btn-sm">
                                <i class="feather-download me-2"></i> Unduh Lampiran
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            {{-- Baris 1: Tipe Perusahaan & Customer --}}
                            <dt class="col-sm-3">Tipe Perusahaan</dt>
                            <dd class="col-sm-9">
                                <strong>{{ $agreementLetter->company_type }}</strong>
                            </dd>

                            <dt class="col-sm-3">Customer</dt>
                            <dd class="col-sm-9">{{ $agreementLetter->customer->name ?? 'N/A' }}</dd>

                            {{-- Baris 2: Sales & Periode Efektif --}}
                            <dt class="col-sm-3">Sales Name</dt>
                            <dd class="col-sm-9">{{ $agreementLetter->sales_name }}</dd>

                            <dt class="col-sm-3">Periode Efektif</dt>
                            <dd class="col-sm-9">
                                {{ \Carbon\Carbon::parse($agreementLetter->effective_start_date)->translatedFormat('d F Y') }}
                                -
                                {{ \Carbon\Carbon::parse($agreementLetter->effective_end_date)->translatedFormat('d F Y') }}
                            </dd>

                            {{-- Baris 3: File Lampiran --}}
                            <dt class="col-sm-3">Status Berlaku Surat</dt>
                            <dd class="col-sm-9">
                                @if ($agreementLetter->is_active == 1)
                                    <span class="badge bg-success">Berlaku</span>
                                @else
                                    <span class="badge bg-danger">Tidak Berlaku</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">File Lampiran</dt>
                            <dd class="col-sm-9">
                                @if ($agreementLetter->agreement_letter_path)
                                    <a href="{{ Storage::url($agreementLetter->agreement_letter_path) }}" target="_blank">
                                        {{-- Menampilkan nama file saja --}}
                                        {{ basename($agreementLetter->agreement_letter_path) }}
                                    </a>
                                @else
                                    <span class="text-muted">- Tidak ada lampiran -</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Card untuk Informasi Audit, sesuai referensi --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-info me-2"></i>Informasi Audit</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Dibuat Oleh</dt>
                            {{-- Asumsi Anda memiliki relasi 'creator' di model AgreementLetter --}}
                            <dd class="col-sm-9">{{ $agreementLetter->creator->Nama ?? '-' }}</dd>

                            <dt class="col-sm-3">Dibuat Pada</dt>
                            <dd class="col-sm-9">
                                {{ \Carbon\Carbon::parse($agreementLetter->created_at)->translatedFormat('d F Y, H:i') }}
                            </dd>

                            <dt class="col-sm-3">Terakhir Diperbarui Oleh</dt>
                            {{-- Asumsi Anda memiliki relasi 'updater' di model AgreementLetter --}}
                            <dd class="col-sm-9">{{ $agreementLetter->updater->Nama ?? '-' }}</dd>

                            <dt class="col-sm-3">Terakhir Diperbarui Pada</dt>
                            <dd class="col-sm-9">
                                {{ \Carbon\Carbon::parse($agreementLetter->updated_at)->translatedFormat('d F Y, H:i    ') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
