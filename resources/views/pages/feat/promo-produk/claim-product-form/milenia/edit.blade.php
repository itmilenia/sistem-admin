@extends('layouts.app')

@section('title', 'Edit Formulir Klaim Produk: ' . $claim->retail_name)

@push('styles')
    <style>
        .select2-container--open {
            z-index: 9999 !important;
        }

        .table-responsive .btn {
            white-space: nowrap;
        }
    </style>
@endpush


@section('content')
    <x-alert />

    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                {{-- Sesuaikan dengan rute Anda --}}
                <li class="breadcrumb-item"><a href="{{ route('product-claim-form.milenia.index') }}">Data Klaim Produk</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('product-claim-form.milenia.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-edit me-2"></i>Formulir Edit Data Klaim Produk</h5>
                    </div>

                    <div class="card-body">
                        {{-- Arahkan ke route 'update' dengan method 'PUT' --}}
                        <form id="edit-claim-form" action="{{ route('product-claim-form.update', $claim->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Section 1: Informasi Utama (Header) --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Informasi Utama</legend>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="company_type" class="form-label">Tipe Perusahaan <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('company_type') is-invalid @enderror"
                                            id="company_type" name="company_type"
                                            value="{{ old('company_type', $claim->company_type) }}" readonly required>
                                        @error('company_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="retail_name" class="form-label">Nama Ritel <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('retail_name') is-invalid @enderror" id="retail_name"
                                            name="retail_name" value="{{ old('retail_name', $claim->retail_name) }}"
                                            required>
                                        @error('retail_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="sales_id" class="form-label">Sales <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2-basic @error('sales_id') is-invalid @enderror"
                                            id="sales_id" name="sales_id" required>
                                            <option value="">-- Pilih Sales --</option>
                                            @foreach ($salesUsers as $user)
                                                <option value="{{ $user->ID }}"
                                                    {{ old('sales_id', $claim->sales_id) == $user->ID ? 'selected' : '' }}>
                                                    {{ $user->Nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('sales_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="claim_date" class="form-label">Tanggal Klaim <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('claim_date') is-invalid @enderror"
                                            id="claim_date" name="claim_date"
                                            value="{{ old('claim_date', $claim->claim_date) }}" required>
                                        @error('claim_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="sales_head_id" class="form-label">Sales Head <span
                                                class="text-danger">*</span></label>
                                        <select
                                            class="form-select select2-basic @error('sales_head_id') is-invalid @enderror"
                                            id="sales_head_id" name="sales_head_id" required>
                                            <option value="">-- Pilih Sales Head --</option>
                                            @foreach ($salesHeads as $user)
                                                <option value="{{ $user->ID }}"
                                                    {{ old('sales_head_id', $claim->sales_head_id) == $user->ID ? 'selected' : '' }}>
                                                    {{ $user->Nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('sales_head_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="checker_id" class="form-label">Checker <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2-basic @error('checker_id') is-invalid @enderror"
                                            id="checker_id" name="checker_id" required>
                                            <option value="">-- Pilih Checker --</option>
                                            @foreach ($checkers as $user)
                                                <option value="{{ $user->ID }}"
                                                    {{ old('checker_id', $claim->checker_id) == $user->ID ? 'selected' : '' }}>
                                                    {{ $user->Nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('checker_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            {{-- Section 2: Detail Produk Klaim (Dinamis) --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Detail Produk Klaim</legend>

                                @error('details')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                @enderror
                                @error('details.*')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                @enderror

                                <div class="table-responsive">
                                    <table class="table table-bordered" style="min-width: 1000px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 15%;">No. Invoice <span class="text-danger">*</span></th>
                                                <th style="width: 25%;">Produk <span class="text-danger">*</span></th>
                                                <th style="width: 8%;">Qty <span class="text-danger">*</span></th>
                                                <th style="width: 14%;">Tgl. Order <span class="text-danger">*</span></th>
                                                <th style="width: 14%;">Tgl. Terima <span class="text-danger">*</span>
                                                </th>
                                                <th style="width: 19%;">Alasan Retur <span class="text-danger">*</span>
                                                </th>
                                                <th style="width: 5%;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="product-details-tbody">
                                            {{-- Menangani 'old' input atau data dari $claim --}}
                                            @php
                                                $details = old('details', $claim->claimDetails);
                                            @endphp

                                            @if ($details)
                                                @foreach ($details as $i => $detail)
                                                    @php
                                                        $invoiceId = is_array($detail)
                                                            ? $detail['invoice_id'] ?? ''
                                                            : $detail->invoice_id;
                                                        $productId = is_array($detail)
                                                            ? $detail['product_id'] ?? ''
                                                            : $detail->product_id;
                                                        $quantity = is_array($detail)
                                                            ? $detail['quantity'] ?? ''
                                                            : $detail->quantity;
                                                        $orderDate = is_array($detail)
                                                            ? $detail['order_date'] ?? ''
                                                            : $detail->order_date;
                                                        $deliveryDate = is_array($detail)
                                                            ? $detail['delivery_date'] ?? ''
                                                            : $detail->delivery_date;
                                                        $returnReason = is_array($detail)
                                                            ? $detail['return_reason'] ?? ''
                                                            : $detail->return_reason;
                                                    @endphp
                                                    <tr class="detail-row">
                                                        <td><input type="text"
                                                                name="details[{{ $i }}][invoice_id]"
                                                                class="form-control" value="{{ $invoiceId }}"
                                                                required></td>
                                                        <td>
                                                            <select name="details[{{ $i }}][product_id]"
                                                                class="form-select product-select" required
                                                                style="width: 100%;">
                                                                <option value="">-- Pilih Produk --</option>
                                                                @foreach ($products as $product)
                                                                    <option value="{{ $product->MFIMA_ItemID }}"
                                                                        {{ $productId == $product->MFIMA_ItemID ? 'selected' : '' }}>
                                                                        {{ $product->MFIMA_ItemID }} -
                                                                        {{ $product->MFIMA_Description }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number"
                                                                name="details[{{ $i }}][quantity]"
                                                                class="form-control" value="{{ $quantity }}"
                                                                min="1" required></td>
                                                        <td><input type="date"
                                                                name="details[{{ $i }}][order_date]"
                                                                class="form-control" value="{{ $orderDate }}"
                                                                required></td>
                                                        <td><input type="date"
                                                                name="details[{{ $i }}][delivery_date]"
                                                                class="form-control" value="{{ $deliveryDate }}"
                                                                required></td>
                                                        <td><input type="text"
                                                                name="details[{{ $i }}][return_reason]"
                                                                class="form-control" value="{{ $returnReason }}"
                                                                required></td>
                                                        <td class="text-center"><button type="button"
                                                                class="btn btn-danger btn-sm remove-row-btn"><i
                                                                    class="feather-trash-2"></i></button></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm mt-2" id="add-row-btn">
                                    <i class="feather-plus me-2"></i> Tambah Baris
                                </button>
                            </fieldset>

                            {{-- Tombol Submit --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="confirmSubmit()">
                                    <i class="feather-save me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TEMPLATE untuk baris detail baru (dihidden) --}}
    <template id="detail-row-template">
        <tr class="detail-row">
            <td><input type="text" name="details[__INDEX__][invoice_id]" class="form-control" required></td>
            <td>
                <select name="details[__INDEX__][product_id]" class="form-select product-select" required
                    style="width: 100%;">
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->MFIMA_ItemID }}">{{ $product->MFIMA_ItemID }} -
                            {{ $product->MFIMA_Description }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="details[__INDEX__][quantity]" class="form-control" min="1" required>
            </td>
            <td><input type="date" name="details[__INDEX__][order_date]" class="form-control" required></td>
            <td><input type="date" name="details[__INDEX__][delivery_date]" class="form-control" required></td>
            <td><input type="text" name="details[__INDEX__][return_reason]" class="form-control" required></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row-btn"><i
                        class="feather-trash-2"></i></button></td>
        </tr>
    </template>

@endsection


@push('scripts')
    {{-- 1. Load SweetAlert2 Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // 2. Inisialisasi Select2 untuk header
            $('.select2-basic').select2({
                theme: 'bootstrap-5',
                placeholder: $(this).data('placeholder'),
                width: '100%',
            });

            // Biar langsung fokus ke search saat dropdown terbuka
            $(document).on('select2:open', () => {
                // Tunggu sedikit supaya elemen search muncul, lalu fokuskan
                if (document.querySelector('.select2-container--open .select2-search__field')) {
                    document.querySelector('.select2-container--open .select2-search__field').focus();
                }
            });

            // 3. Inisialisasi Select2 untuk baris yang sudah ada (dari 'old' input atau $claim)
            initExistingSelect2();

            // 4. Logika Tambah/Hapus Baris
            const tableBody = $('#product-details-tbody');
            const template = $('#detail-row-template').html();

            // Tentukan row index awal berdasarkan jumlah baris yang sudah ada
            let rowIndex = tableBody.find('.detail-row').length;

            $('#add-row-btn').on('click', function() {
                // Ganti placeholder index
                const newRowHtml = template.replace(/__INDEX__/g, rowIndex);
                const newRow = $(newRowHtml);

                // Tambahkan baris baru ke tabel
                tableBody.append(newRow);

                // Inisialisasi Select2 pada baris baru
                const $select = newRow.find('.product-select').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Pilih Produk --',
                    width: '100%',
                });

                // Buka dan fokus otomatis ke kolom search (biar langsung bisa ngetik)
                $select.select2('open');

                rowIndex++;
            });

            // Event delegation untuk tombol hapus
            tableBody.on('click', '.remove-row-btn', function() {
                const row = $(this).closest('.detail-row');

                // Hancurkan Select2 sebelum menghapus row
                row.find('.product-select').select2('destroy');

                row.remove();

                // Update ulang index (penting agar urut)
                updateRowIndexes();
            });

            // Tambahkan 1 baris kosong jika tabel masih kosong
            if (rowIndex === 0) {
                $('#add-row-btn').click();
            }
        });

        // Fungsi untuk inisialisasi Select2 pada baris 'old' atau 'claim'
        function initExistingSelect2() {
            $('#product-details-tbody .detail-row').each(function() {
                $(this).find('.product-select').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Pilih Produk --',
                    width: '100%',
                });
            });
        }

        // Fungsi untuk update index setelah menghapus (agar urut)
        function updateRowIndexes() {
            $('#product-details-tbody .detail-row').each(function(index) {
                $(this).find('input, select').each(function() {
                    if (this.name) {
                        this.name = this.name.replace(/details\[\d+\]/, `details[${index}]`);
                    }
                });
            });
        }

        // 5. Fungsi konfirmasi submit
        function confirmSubmit() {
            const form = document.getElementById('edit-claim-form'); // Ganti ID form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Perubahan Data?', // Ganti judul
                text: "Pastikan semua data pada formulir sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false

            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menyimpan perubahan data. Mohon tunggu.', // Ganti teks
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            });
        }
    </script>
@endpush
