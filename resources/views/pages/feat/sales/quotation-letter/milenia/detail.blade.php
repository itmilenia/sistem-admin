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
                <li class="breadcrumb-item"><a href="{{ route('quotation-letter.milenia.index') }}">Data Surat Penawaran Milenia</a></li>
                <li class="breadcrumb-item">Detail</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('quotation-letter.milenia.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="feather-file-text me-2"></i>Informasi Surat Penawaran</h5>
                        {{-- Tampilkan tombol download jika file ada --}}
                        @if ($quotationLetter->quotation_letter_file)
                            <a href="{{ Storage::url($quotationLetter->quotation_letter_file) }}" target="_blank"
                                class="btn btn-primary btn-sm">
                                <i class="feather-download me-2"></i> Unduh Lampiran
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            {{-- Baris 1: Nomor Surat & Tipe Surat --}}
                            <dt class="col-sm-3">Nomor Surat</dt>
                            <dd class="col-sm-9">
                                <strong>{{ $quotationLetter->quotation_letter_number }}</strong>
                            </dd>

                            <dt class="col-sm-3">Tipe Surat</dt>
                            <dd class="col-sm-9">{{ $quotationLetter->letter_type }}</dd>

                            {{-- Baris 2: Tanggal & Penerima --}}
                            <dt class="col-sm-3">Tanggal Surat</dt>
                            <dd class="col-sm-9">
                                {{ \Carbon\Carbon::parse($quotationLetter->letter_date)->translatedFormat('d F Y') }}
                            </dd>

                            <dt class="col-sm-3">Penerima</dt>
                            <dd class="col-sm-9">{{ $quotationLetter->recipient }}</dd>

                            {{-- Baris 3: Perihal & Status --}}
                            <dt class="col-sm-3">Perihal (Subject)</dt>
                            <dd class="col-sm-9">{{ $quotationLetter->subject }}</dd>

                            <dt class="col-sm-3">Status Surat</dt>
                            <dd class="col-sm-9">
                                <span
                                    class="badge
                                    @if ($quotationLetter->letter_status == 'Disetujui') bg-success
                                    @elseif ($quotationLetter->letter_status == 'Draft') bg-secondary
                                    @elseif ($quotationLetter->letter_status == 'Ditolak') bg-danger
                                    @else bg-info @endif
                                ">
                                    {{ $quotationLetter->letter_status }}
                                </span>
                            </dd>

                            {{-- Baris 4: File Lampiran (Path) --}}
                            <dt class="col-sm-3">File Lampiran</dt>
                            <dd class="col-sm-9">
                                @if ($quotationLetter->quotation_letter_file)
                                    <a href="{{ Storage::url($quotationLetter->quotation_letter_file) }}" target="_blank">
                                        {{ basename($quotationLetter->quotation_letter_file) }}
                                    </a>
                                @else
                                    <span class="text-muted">- Tidak ada lampiran -</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-info me-2"></i>Informasi Audit</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Dibuat Oleh</dt>
                            <dd class="col-sm-9">{{ $quotationLetter->creator->Nama ?? '-' }}</dd>

                            <dt class="col-sm-3">Dibuat Pada</dt>
                            <dd class="col-sm-9">
                                {{ \Carbon\Carbon::parse($quotationLetter->created_at)->translatedFormat('d F Y') }}
                            </dd>

                            <dt class="col-sm-3">Terakhir Diperbarui Oleh</dt>
                            <dd class="col-sm-9">{{ $quotationLetter->updater->Nama ?? '-' }}</dd>

                            <dt class="col-sm-3">Terakhir Diperbarui Pada</dt>
                            <dd class="col-sm-9">
                                {{ \Carbon\Carbon::parse($quotationLetter->updated_at)->translatedFormat('d F Y') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
