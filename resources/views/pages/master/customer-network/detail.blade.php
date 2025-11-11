@extends('layouts.app')

@section('title', 'Detail Jaringan Customer')

@section('content')
    {{-- Menampilkan alert --}}
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
            <div class="col-lg-7">
                {{-- Card 1: Informasi Customer --}}
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-user me-2"></i>Informasi Customer</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Nama Customer</dt>
                            <dd class="col-sm-8"><strong>{{ $customerNetwork->name }}</strong></dd>

                            <dt class="col-sm-4">Kategori</dt>
                            <dd class="col-sm-8">{{ $customerNetwork->category }}</dd>

                            <dt class="col-sm-4">Brand</dt>
                            <dd class="col-sm-8">
                                @forelse ($customerNetwork->brands_collection as $brand)
                                    <span class="badge bg-primary me-1">{{ $brand->brand_name }}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </dd>

                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                @if ($customerNetwork->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Card 2: Informasi Audit (Jika relasi creator/updater ada) --}}
            <div class="col-lg-5">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-info me-2"></i>Informasi Audit</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Dibuat Pada</dt>
                            <dd class="col-sm-8">
                                {{ $customerNetwork->created_at ? $customerNetwork->created_at->translatedFormat('d F Y H:i:s') : '-' }}
                            </dd>

                            <dt class="col-sm-4">Diperbarui Pada</dt>
                            <dd class="col-sm-8">
                                {{ $customerNetwork->updated_at ? $customerNetwork->updated_at->translatedFormat('d F Y H:i:s') : '-' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
