@extends('layouts.app')

@section('title', 'Detail Penjualan Sales')

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title'): {{ $salesperson->MFSSM_Description }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('salesperson-sales.landing') }}">Sales</a></li>
                <li class="breadcrumb-item"><a href="{{ route('salesperson-sales.transactions.map.branch.index') }}">Data
                        Penjualan Sales</a></li>
                <li class="breadcrumb-item">Detail</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('salesperson-sales.transactions.map.branch.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            {{-- Kartu Filter --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" id="start_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" id="end_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-start gap-2">
                                    <button id="resetBtn" class="btn btn-light">Reset</button>
                                    <button id="filterBtn" class="btn btn-primary">Terapkan Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu Daftar Transaksi --}}
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Daftar Transaksi</h5>
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <strong>Total Penjualan (sesuai filter):</strong>
                                <span id="filteredTotal" class="fs-5 fw-bold text-success">Memuat...</span>
                            </div>
                            <a href="#" id="exportBtn" class="btn btn-success disabled">
                                <i class="feather-download me-2"></i> Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th class="text-center">No. Invoice</th>
                                        <th class="text-center">Tanggal Invoice</th>
                                        <th class="text-center">ID Barang</th>
                                        <th class="text-center">Nama Barang</th>
                                        <th class="text-center">Nama Brand</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Harga Satuan</th>
                                        <th class="text-center">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody id="transactions-data-body">
                                    <tr>
                                        <td colspan="8" class="text-center">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <div class="btn-group" role="group">
                                <button id="prevPage" class="btn btn-outline-secondary" disabled>← Sebelumnya</button>
                                <button id="nextPage" class="btn btn-outline-primary">Selanjutnya →</button>
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
            const tableBody = document.getElementById('transactions-data-body');
            const filteredTotalSpan = document.getElementById('filteredTotal');

            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');
            const filterBtn = document.getElementById('filterBtn');
            const resetBtn = document.getElementById('resetBtn');

            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const exportBtn = document.getElementById('exportBtn');

            function updateExportLink() {
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;

                if (startDate && endDate) {
                    const params = new URLSearchParams({
                        start_date: startDate,
                        end_date: endDate
                    });

                    const baseUrl =
                        `{{ route('salesperson-sales.transactions.milenia-branch.export-sales-by-brand', $salesperson->MFSSM_SalesmanID) }}`;
                    exportBtn.href = `${baseUrl}?${params.toString()}`;
                    exportBtn.classList.remove('disabled');
                } else {
                    exportBtn.href = '#';
                    exportBtn.classList.add('disabled');
                }
            }

            function loadData(cursor = null) {
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center">Memuat data...</td></tr>`;
                filteredTotalSpan.textContent = 'Menghitung...';

                const params = new URLSearchParams({
                    cursor: cursor ?? '',
                    start_date: startDateInput.value,
                    end_date: endDateInput.value
                });

                const url =
                    `{{ route('salesperson-sales.transactions.map-branch.data.details', $salesperson->MFSSM_SalesmanID) }}?${params}`;

                fetch(url)
                    .then(res => res.json())
                    .then(res => {
                        nextCursor = res.next_cursor;
                        prevCursor = res.prev_cursor;
                        prevBtn.disabled = !prevCursor;
                        nextBtn.disabled = !nextCursor;

                        filteredTotalSpan.textContent = res.total_filtered_sales || 'Rp 0';

                        tableBody.innerHTML = '';

                        if (res.data.length === 0) {
                            tableBody.innerHTML =
                                `<tr><td colspan="8" class="text-center">Tidak ada transaksi ditemukan.</td></tr>`;
                            return;
                        }

                        res.data.forEach(item => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.SOIVH_InvoiceID}</td>
                                    <td class="text-center">${item.SOIVH_InvoiceDate}</td>
                                    <td class="text-center">${item.item_id}</td>
                                    <td>${item.item_name}</td>
                                    <td>${item.brand_name}</td>
                                    <td class="text-center">${Math.round(item.order_qty)}</td>
                                    <td class="text-end">${item.unit_price}</td>
                                    <td class="text-end">${item.SOIVD_LineInvoiceAmount}</td>
                                </tr>
                            `;
                        });
                    })
                    .catch(() => {
                        tableBody.innerHTML =
                            `<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                        filteredTotalSpan.textContent = 'Error';
                    });
            }

            loadData();
            updateExportLink();

            nextBtn.addEventListener('click', () => {
                if (nextCursor) loadData(nextCursor);
            });
            prevBtn.addEventListener('click', () => {
                if (prevCursor) loadData(prevCursor);
            });
            filterBtn.addEventListener('click', () => {
                loadData();
                updateExportLink();
            });

            resetBtn.addEventListener('click', () => {
                startDateInput.value = '';
                endDateInput.value = '';
                loadData();
                updateExportLink();
            });

            startDateInput.addEventListener('input', updateExportLink);
            endDateInput.addEventListener('input', updateExportLink);
        });
    </script>
@endpush
