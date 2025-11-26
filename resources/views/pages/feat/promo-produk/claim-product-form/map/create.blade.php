@extends('layouts.app')

@section('title', 'Tambah Formulir Klaim Produk')

@push('styles')
    {{-- Ini adalah style dari referensi Anda untuk Select2 Bootstrap 5 --}}
    <style>
        .select2-container--open {
            z-index: 9999 !important;
        }

        /* Style untuk tombol hapus di dalam tabel */
        .table-responsive .btn {
            white-space: nowrap;
        }

        /* Custom width for Select2 to handle long text */
        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
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
                {{-- Sesuaikan 'home' dengan route dashboard Anda --}}
                <li class="breadcrumb-item"><a href="{{ route('product-claim-form.landing') }}">Form Klaim Produk</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('product-claim-form.map.index') }}">Data Klaim Produk
                        MAP
                    </a>
                </li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('product-claim-form.map.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-plus-circle me-2"></i>Formulir Data Klaim Produk</h5>
                    </div>

                    <div class="card-body">
                        <form id="create-claim-form" action="{{ route('product-claim-form.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

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
                                            value="{{ old('company_type', 'PT Mega Auto Prima') }}" readonly required>
                                        @error('company_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="claim_type" class="form-label">Jenis Klaim <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2-basic @error('claim_type') is-invalid @enderror"
                                            id="claim_type" name="claim_type" required>
                                            <option value="">-- Pilih Jenis Klaim --</option>
                                            <option value="WHOLESALE"
                                                {{ old('claim_type') == 'WHOLESALE' ? 'selected' : '' }}>
                                                WHOLESALE</option>
                                            <option value="ECOMMERCE"
                                                {{ old('claim_type') == 'ECOMMERCE' ? 'selected' : '' }}>
                                                ECOMMERCE</option>
                                        </select>
                                        @error('claim_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="retail_name" class="form-label">Nama Ritel <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('retail_name') is-invalid @enderror" id="retail_name"
                                            name="retail_name" value="{{ old('retail_name') }}" required>
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
                                                    {{ old('sales_id') == $user->ID ? 'selected' : '' }}>
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
                                            value="{{ old('claim_date', date('Y-m-d')) }}" required>
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
                                                    {{ old('sales_head_id') == $user->ID ? 'selected' : '' }}>
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
                                                    {{ old('checker_id') == $user->ID ? 'selected' : '' }}>
                                                    {{ $user->Nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('checker_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            {{-- Section: Cari Invoice --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Cari Produk via Invoice</legend>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="invoice_search"
                                        placeholder="Masukkan Nomor Invoice (Contoh: INV...)">
                                    <button class="btn btn-primary" type="button" id="btn-search-invoice">
                                        <i class="feather-search me-2"></i>Cari Invoice
                                    </button>
                                </div>
                            </fieldset>

                            {{-- Section 2: Detail Produk Klaim (Dinamis) --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Detail Produk Klaim</legend>

                                @error('details')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                @enderror
                                @error('details.*.product_image')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                @enderror
                                @error('details.*')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                @enderror

                                <div class="table-responsive">
                                    <table class="table table-bordered" style="min-width: 100%; table-layout: fixed;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 15%;">No. Invoice <span
                                                        class="text-danger">*</span>
                                                </th>
                                                <th class="text-center" style="width: 20%;">Produk <span
                                                        class="text-danger">*</span></th>
                                                <th class="text-center" style="width: 10%;">Gambar/Video</th>
                                                <th class="text-center" style="width: 8%;">Qty <span
                                                        class="text-danger">*</span></th>
                                                <th class="text-center" style="width: 12%;">Tgl. Order <span
                                                        class="text-danger">*</span></th>
                                                <th class="text-center" style="width: 12%;">Tgl. Terima <span
                                                        class="text-danger">*</span>
                                                </th>
                                                <th class="text-center" style="width: 18%;">Alasan Retur <span
                                                        class="text-danger">*</span>
                                                </th>
                                                <th class="text-center" style="width: 5%;">#</th>
                                            </tr>
                                        </thead>
                                        <tbody id="product-details-tbody">
                                            {{-- Baris akan ditambahkan oleh JS --}}
                                            {{-- Menangani jika ada 'old' input setelah validasi gagal --}}
                                            @if (old('details'))
                                                @foreach (old('details') as $i => $detail)
                                                    <tr class="detail-row">
                                                        <td><input type="text"
                                                                name="details[{{ $i }}][invoice_id]"
                                                                class="form-control" value="{{ $detail['invoice_id'] }}"
                                                                required></td>
                                                        <td>
                                                            <select name="details[{{ $i }}][product_id]"
                                                                class="form-select product-select" required
                                                                style="width: 100%;">
                                                                <option value="">-- Pilih Produk --</option>
                                                                @foreach ($products as $product)
                                                                    <option value="{{ $product->MFIMA_ItemID }}"
                                                                        {{ $detail['product_id'] == $product->MFIMA_ItemID ? 'selected' : '' }}>
                                                                        {{ $product->MFIMA_Description }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="file"
                                                                name="details[{{ $i }}][product_image]"
                                                                class="form-control"
                                                                accept=".jpg,.jpeg,.png,.mp4,.mov,.avi,.wmv">
                                                        </td>
                                                        <td><input type="number"
                                                                name="details[{{ $i }}][quantity]"
                                                                class="form-control" value="{{ $detail['quantity'] }}"
                                                                min="1" required></td>
                                                        <td><input type="date"
                                                                name="details[{{ $i }}][order_date]"
                                                                class="form-control" value="{{ $detail['order_date'] }}"
                                                                required></td>
                                                        <td><input type="date"
                                                                name="details[{{ $i }}][delivery_date]"
                                                                class="form-control"
                                                                value="{{ $detail['delivery_date'] }}" required></td>
                                                        <td><input type="text"
                                                                name="details[{{ $i }}][return_reason]"
                                                                class="form-control"
                                                                value="{{ $detail['return_reason'] }}" required></td>
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
                                    <i class="feather-save me-2"></i> Simpan Formulir
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Pilih Item Invoice --}}
    @push('modals')
        <div class="modal fade" id="invoiceItemsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Item dari Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="invoice-items-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">Pilih</th>
                                        <th>Kode Item</th>
                                        <th>Nama Produk</th>
                                        <th>Qty Invoice</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Diisi via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn-add-selected-items">Tambahkan Item
                            Terpilih</button>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    {{-- TEMPLATE untuk baris detail baru (dihidden) --}}
    <template id="detail-row-template">
        <tr class="detail-row">
            <td><input type="text" name="details[__INDEX__][invoice_id]" class="form-control" required></td>
            <td>
                <select name="details[__INDEX__][product_id]" class="form-select product-select" required
                    style="width: 100%;">
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->MFIMA_ItemID }}">{{ $product->MFIMA_Description }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="file" name="details[__INDEX__][product_image]" class="form-control"
                    accept=".jpg,.jpeg,.png,.mp4,.mov,.avi,.wmv">
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
                document.querySelector('.select2-container--open .select2-search__field').focus();
            });

            // 3. Inisialisasi Select2 untuk baris yang sudah ada (dari 'old' input)
            initExistingSelect2();

            // 4. Logika Tambah/Hapus Baris
            const tableBody = $('#product-details-tbody');
            const template = $('#detail-row-template').html();
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
                    width: '100%'
                });

                // Fokus otomatis ke kolom search (biar langsung bisa ngetik)
                setTimeout(() => {
                    document.querySelector('.select2-container--open .select2-search__field')
                        .focus();
                }, 100);

                rowIndex++;
            });

            // Event delegation untuk tombol hapus
            tableBody.on('click', '.remove-row-btn', function() {
                const row = $(this).closest('.detail-row');

                // Hancurkan Select2 sebelum menghapus row
                row.find('.product-select').select2('destroy');

                row.remove();

                // Update ulang index (opsional tapi rapi)
                updateRowIndexes();
            });

            if (rowIndex === 0) {
                $('#add-row-btn').click();
            }

            // --- LOGIKA CARI INVOICE ---
            $('#btn-search-invoice').on('click', function() {
                const invoiceNo = $('#invoice_search').val().trim();
                const companyType = $('#company_type').val();

                if (!invoiceNo) {
                    Swal.fire('Peringatan', 'Mohon masukkan nomor invoice.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Mencari Invoice...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "{{ route('product-claim-form.get-invoice-details') }}",
                    type: "GET",
                    data: {
                        invoice_no: invoiceNo,
                        company_type: companyType
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.length > 0) {
                            populateInvoiceModal(response);
                            $('#invoiceItemsModal').modal('show');
                        } else {
                            Swal.fire('Info', 'Invoice tidak ditemukan atau tidak ada item.',
                                'info');
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let msg = 'Gagal mencari invoice.';
                        if (xhr.status === 404) {
                            msg = 'Invoice tidak ditemukan.';
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            function populateInvoiceModal(items) {
                const tbody = $('#invoice-items-table tbody');
                tbody.empty();

                items.forEach((item, index) => {
                    const tr = `
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="invoice-item-checkbox" value="${index}"
                                    data-invoice="${item.invoice_no}"
                                    data-date="${item.invoice_date}"
                                    data-order-date="${item.order_date}"
                                    data-product-id="${item.product_id}"
                                    data-qty="${item.quantity}">
                            </td>
                            <td>${item.product_id}</td>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                        </tr>
                    `;
                    tbody.append(tr);
                });
            }

            $('#btn-add-selected-items').on('click', function() {
                const selectedCheckboxes = $('.invoice-item-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    Swal.fire('Peringatan', 'Pilih setidaknya satu item.', 'warning');
                    return;
                }

                // Cek apakah ada baris pertama yang kosong
                const rows = $('#product-details-tbody .detail-row');
                if (rows.length === 1) {
                    const firstRow = rows.first();
                    const invoiceVal = firstRow.find('input[name*="[invoice_id]"]').val();
                    const productVal = firstRow.find('.product-select').val();

                    // Jika kosong, hapus dulu sebelum tambah yang baru
                    if (!invoiceVal && !productVal) {
                        firstRow.find('.remove-row-btn').click();
                    }
                }

                let duplicateCount = 0;

                selectedCheckboxes.each(function() {
                    const $cb = $(this);
                    const invoiceNo = $cb.data('invoice');
                    const invoiceDate = $cb.data('date');
                    const orderDate = $cb.data('order-date');
                    const productId = $cb.data('product-id');
                    // const qty = $cb.data('qty'); // Kita biarkan user input qty retur manual, atau default 1?

                    // Cek duplikasi sebelum menambah
                    let isDuplicate = false;
                    $('#product-details-tbody .product-select').each(function() {
                        if ($(this).val() == productId) {
                            isDuplicate = true;
                            return false; // break loop
                        }
                    });

                    if (isDuplicate) {
                        duplicateCount++;
                        return; // skip adding this item
                    }

                    // Tambah baris baru
                    $('#add-row-btn').click();

                    // Ambil baris terakhir yang baru saja ditambahkan
                    const lastRow = $('#product-details-tbody .detail-row').last();

                    // Isi data
                    lastRow.find('input[name*="[invoice_id]"]').val(invoiceNo);

                    // Kita isi order_date dengan data dari DB
                    lastRow.find('input[name*="[order_date]"]').val(orderDate);

                    // Set Product ID di Select2
                    const productSelect = lastRow.find('.product-select');
                    if (productSelect.find("option[value='" + productId + "']").length) {
                        productSelect.val(productId).trigger('change');
                    } else {
                        // Jika produk tidak ada di list (misal inactive), bisa tambahkan option sementara atau alert
                        console.warn('Product ID not found in list:', productId);
                    }
                });

                $('#invoiceItemsModal').modal('hide');
                // Reset search
                $('#invoice_search').val('');

                if (duplicateCount > 0) {
                    Swal.fire('Info',
                        `${duplicateCount} item tidak ditambahkan karena sudah ada di daftar.`, 'info');
                }
            });

            // --- LOGIKA CEK DUPLIKASI MANUAL ---
            $(document).on('change', '.product-select', function() {
                const currentSelect = $(this);
                const currentValue = currentSelect.val();

                if (!currentValue) return;

                let isDuplicate = false;
                $('#product-details-tbody .product-select').not(currentSelect).each(function() {
                    if ($(this).val() == currentValue) {
                        isDuplicate = true;
                        return false; // break loop
                    }
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Produk Duplikat',
                        text: 'Produk ini sudah ada di daftar. Silakan pilih produk lain.',
                    });
                    currentSelect.val('').trigger('change');
                }
            });

        });

        // Fungsi untuk inisialisasi Select2 pada baris 'old'
        function initExistingSelect2() {
            $('#product-details-tbody .detail-row').each(function() {
                $(this).find('.product-select').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Pilih Produk --',
                    width: '100%'
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

        // 5. Fungsi konfirmasi submit (dari referensi Anda)
        function confirmSubmit() {
            const form = document.getElementById('create-claim-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Penyimpanan Data?',
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
                        text: 'Sedang menyimpan data formulir. Mohon tunggu.',
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
