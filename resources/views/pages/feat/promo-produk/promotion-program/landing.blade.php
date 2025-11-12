@extends('layouts.app')

@section('title', 'Pilih Perusahaan')

@section('content')
    <div class="container py-5">
        <div class="card shadow-sm border-0">
            <div class="card-body py-5">
                <div class="text-center mb-5">
                    <h1 class="fw-bold mb-3">Pilih Perusahaan</h1>
                    <p class="lead text-muted">Silakan pilih jenis promosi program dari perusahaan yang ingin ditampilkan</p>
                </div>

                <div class="row justify-content-center">
                    @can('lihat_program_promo_milenia')
                        {{-- ========== MILENIA PUSAT ========== --}}
                        <div class="col-md-6 col-lg-6 mb-4">
                            <a href="{{ route('promotion-program.milenia.index') }}" class="text-decoration-none">
                                <div class="card shadow-lg border-0 h-100 hover-card"
                                    style="background-color: #6B6B8E; color: white; border-radius: 15px;">
                                    <div class="card-body text-center py-5">
                                        <div
                                            class="logo-wrapper bg-white mx-auto mb-4 d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('assets/images/logo/logo-milenia.png') }}"
                                                alt="Milenia Mega Mandiri Logo" class="img-fluid" style="max-height: 60px;">
                                        </div>
                                        <h4 class="fw-bold text-white mb-1">Milenia Mega Mandiri</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endcan

                    @can('lihat_program_promo_map')
                        {{-- ========== MAP PUSAT ========== --}}
                        <div class="col-md-6 col-lg-6 mb-4">
                            <a href="{{ route('promotion-program.map.index') }}" class="text-decoration-none">
                                <div class="card shadow-lg border-0 h-100 hover-card"
                                    style="background-color: #000ACF; color: white; border-radius: 15px;">
                                    <div class="card-body text-center py-5">
                                        <div
                                            class="logo-wrapper bg-white mx-auto mb-4 d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('assets/images/logo/logo-map.png') }}" alt="Mega Auto Prima Logo"
                                                class="img-fluid" style="max-height: 60px;">
                                        </div>
                                        <h4 class="fw-bold text-white mb-1">Mega Auto Prima</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .hover-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        }

        .logo-wrapper {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 576px) {
            .logo-wrapper {
                width: 90px;
                height: 90px;
            }
        }
    </style>
@endsection
