@extends('layouts.app')

@section('title', 'Pending Sales Order Milenia Cabang')

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('pending-so.landing') }}">Pending Sales Order</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Daftar Pending Sales Order Milenia Cabang</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4 align-items-end">
                            <div class="col-md-5">
                                <label for="start_date" class="form-label">Tanggal Mulai:</label>
                                <input type="date" id="start_date" class="form-control">
                            </div>
                            <div class="col-md-5">
                                <label for="end_date" class="form-label">Tanggal Akhir:</label>
                                <input type="date" id="end_date" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <button id="filterBtn" class="btn btn-primary w-100">
                                    <i class="feather-filter me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">No. Order</th>
                                        <th class="text-center">Tanggal Order</th>
                                        <th>Nama Customer</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="transaction-body">
                                    <tr>
                                        <td colspan="4" class="text-center">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Tombol navigasi --}}
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
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script>
        const baseDetailRoute = "{{ url('sales/pending-sales-order/milenia-cabang') }}/";
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let nextCursor = null;
            let prevCursor = null;

            // Set default dates (Start and End of current month)
            const today = moment();
            const startOfMonth = today.clone().startOf('month').format('YYYY-MM-DD');
            const endOfMonth = today.clone().endOf('month').format('YYYY-MM-DD');

            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const filterBtn = document.getElementById('filterBtn');
            const tableBody = document.getElementById('transaction-body');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');

            // Set Initial Values
            startDateInput.value = startOfMonth;
            endDateInput.value = endOfMonth;

            function loadData(cursor = null) {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Memuat data...</td></tr>`;

                const params = new URLSearchParams({
                    cursor: cursor ?? '',
                    start_date: startDateInput.value,
                    end_date: endDateInput.value
                });

                fetch(`{{ route('pending-so.milenia-branch.data') }}?${params}`)
                    .then(res => res.json())
                    .then(res => {
                        nextCursor = res.next_cursor;
                        prevCursor = res.prev_cursor;
                        prevBtn.disabled = !prevCursor;
                        nextBtn.disabled = !nextCursor;

                        tableBody.innerHTML = '';

                        if (res.data.length === 0) {
                            tableBody.innerHTML =
                                `<tr><td colspan="4" class="text-center">Tidak ada data ditemukan.</td></tr>`;
                            return;
                        }

                        res.data.forEach(item => {
                            tableBody.innerHTML += `
                            <tr>
                                <td class="text-center">${item.SOSOH_OrderID}</td>
                                <td class="text-center">${item.SOSOH_OrderDate}</td>
                                <td>${item.CustomerName}</td>
                                <td class="text-center">
                                   <a href="${baseDetailRoute}${item.SOSOH_OrderID}" 
                                        class="btn btn-sm btn-info"><i class="feather-eye me-1"></i> Lihat</a>
                                </td>
                            </tr>
                        `;
                        });

                    })
                    .catch(() => {
                        tableBody.innerHTML =
                            `<tr><td colspan="3" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    });
            }

            // Load Initial Data
            loadData();

            // Event Listeners
            filterBtn.addEventListener('click', () => {
                loadData();
            });

            nextBtn.addEventListener('click', () => {
                if (nextCursor) loadData(nextCursor);
            });
            prevBtn.addEventListener('click', () => {
                if (prevCursor) loadData(prevCursor);
            });
        });
    </script>
@endpush
