@extends('layouts.app')

@section('title', 'Transaksi Pembelian Customer')

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('customer-transaction.landing') }}">Customer</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Daftar Semua Transaksi Pembelian (Mega Auto Prima - Pusat)</h5>
                    </div>

                    <div class="card-body">
                        {{-- 🔍 Kolom pencarian --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" id="search_invoice" class="form-control"
                                    placeholder="Cari No. Invoice...">
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="search_customer" class="form-control"
                                    placeholder="Cari Nama Customer...">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th class="text-center">No. Invoice</th>
                                        <th>Nama Customer</th>
                                        <th class="text-center">Tanggal Invoice</th>
                                        <th class="text-center">Jatuh Tempo</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center" width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="transaction-body">
                                    <tr>
                                        <td colspan="7" class="text-center">Memuat data...</td>
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
            const tableBody = document.getElementById('transaction-body');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');
            const searchInvoice = document.getElementById('search_invoice');
            const searchCustomer = document.getElementById('search_customer');

            function loadData(cursor = null) {
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center">Memuat data...</td></tr>`;

                const params = new URLSearchParams({
                    cursor: cursor ?? '',
                    search_invoice: searchInvoice.value,
                    search_customer: searchCustomer.value
                });

                fetch(`{{ route('customer-transaction-map.data') }}?${params}`)
                    .then(res => res.json())
                    .then(res => {
                        nextCursor = res.next_cursor;
                        prevCursor = res.prev_cursor;
                        prevBtn.disabled = !prevCursor;
                        nextBtn.disabled = !nextCursor;

                        tableBody.innerHTML = '';

                        if (res.data.length === 0) {
                            tableBody.innerHTML =
                                `<tr><td colspan="7" class="text-center">Tidak ada data ditemukan.</td></tr>`;
                            return;
                        }

                        res.data.forEach((item, index) => {
                            tableBody.innerHTML += `
                            <tr>
                                <td class="text-center">${item.SOIVH_InvoiceID}</td>
                                <td>${item.customer_name}</td>
                                <td class="text-center">${item.SOIVH_InvoiceDate}</td>
                                <td class="text-center">${item.SOIVH_DueDate}</td>
                                <td class="text-end">${item.SOIVH_InvoiceAmount}</td>
                                <td class="text-center">${item.action}</td>
                            </tr>
                        `;
                        });
                    })
                    .catch(() => {
                        tableBody.innerHTML =
                            `<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    });
            }

            // Load pertama
            loadData();

            // Navigasi
            nextBtn.addEventListener('click', () => {
                if (nextCursor) loadData(nextCursor);
            });
            prevBtn.addEventListener('click', () => {
                if (prevCursor) loadData(prevCursor);
            });

            // Event search (debounce 500ms)
            let typingTimer;
            [searchInvoice, searchCustomer].forEach(input => {
                input.addEventListener('input', () => {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => loadData(), 500);
                });
            });
        });
    </script>
@endpush
