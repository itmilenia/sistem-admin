@extends('layouts.app')

@section('title', 'Data Penjualan Sales')

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('salesperson-sales.landing') }}">Sales</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Daftar Penjualan per Sales (Milenia Mega Mandiri - Cabang)</h5>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="search_salesperson" class="form-label">Cari Nama Sales</label>
                                <input type="text" id="search_salesperson" class="form-control"
                                    placeholder="Cari Nama Sales...">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nama Sales</th>
                                        <th class="text-center">Total Invoice</th>
                                        <th class="text-center">Total Penjualan</th>
                                        <th class="text-center" width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="sales-data-body">
                                    <tr>
                                        <td colspan="4" class="text-center">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Tombol navigasi custom --}}
                        <div class="d-flex justify-content-end mt-3">
                            <div class="btn-group" role="group">
                                <button id="prevPage" class="btn btn-outline-secondary" disabled>
                                    ← Sebelumnya
                                </button>
                                <button id="nextPage" class="btn btn-outline-primary">
                                    Selanjutnya →
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let nextCursor = null;
            let prevCursor = null;
            const tableBody = document.getElementById('sales-data-body');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');
            const searchSalesperson = document.getElementById('search_salesperson');

            function loadData(cursor = null) {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Memuat data...</td></tr>`;

                const params = new URLSearchParams({
                    cursor: cursor ?? '',
                    search_salesperson: searchSalesperson.value,
                });

                fetch(`{{ route('salesperson-sales.transactions.milenia.branch.data') }}?${params}`)
                    .then(res => res.json())
                    .then(res => {
                        nextCursor = res.next_cursor;
                        prevCursor = res.prev_cursor;
                        prevBtn.disabled = !prevCursor;
                        nextBtn.disabled = !nextCursor;

                        tableBody.innerHTML = ''; // Kosongkan tabel sebelum diisi

                        if (res.data.length === 0) {
                            tableBody.innerHTML =
                                `<tr><td colspan="4" class="text-center">Tidak ada data ditemukan.</td></tr>`;
                            return;
                        }

                        res.data.forEach(item => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.salesman_name}</td>
                                    <td class="text-center">${item.total_invoices}</td>
                                    <td class="text-end">${item.total_sales}</td>
                                    <td class="text-center">${item.action}</td>
                                </tr>
                            `;
                        });
                    })
                    .catch(() => {
                        tableBody.innerHTML =
                            `<tr><td colspan="4" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    });
            }

            // Load pertama kali
            loadData();

            // Navigasi halaman
            nextBtn.addEventListener('click', () => {
                if (nextCursor) loadData(nextCursor);
            });
            prevBtn.addEventListener('click', () => {
                if (prevCursor) loadData(prevCursor);
            });

            // Event search (dengan debounce 500ms agar tidak request terus-menerus)
            let typingTimer;
            searchSalesperson.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => loadData(), 500);
            });
        });
    </script>
@endpush
