@extends('layouts.app')

@section('title', 'Tambah Surat Penawaran Baru')

@push('styles')
    {{-- 1. Trix Editor CSS --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

    <style>
        /* Custom Style Signature Pad */
        .signature-pad {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            width: 100%;
            height: 200px;
            background-color: #f8f9fa;
            cursor: crosshair;
        }

        /* Trix Editor Height */
        trix-editor {
            min-height: 150px;
        }

        /* Utility Helper */
        .d-none {
            display: none !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .quotation-row,
        .quotation-row input,
        .quotation-row select {
            font-size: 12px !important;
            padding: 4px !important;
        }

        .quotation-row .select2-container .select2-selection--single {
            font-size: 12px !important;
            height: 28px !important;
            padding: 2px !important;
        }

        .quotation-row .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 12px !important;
            line-height: 26px !important;
        }

        .quotation-row .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
        }

        trix-toolbar [data-trix-button-group="file-tools"] {
            display: none;
        }

        /* Fix lebar Select2 */
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('quotation-letter.landing') }}">Surat Penawaran</a></li>
                <li class="breadcrumb-item"><a href="{{ route('quotation-letter.map.index') }}">Data Surat Penawaran
                        MAP</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('quotation-letter.map.index') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-plus-circle me-2"></i>Formulir Data Surat</h5>
                    </div>

                    <div class="card-body">
                        <form id="create-quotation-form" action="{{ route('quotation-letter.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            {{-- Input Hidden: Base64 Signature (Jika pilih Draw) --}}
                            <input type="hidden" name="signature_base64" id="signature_base64_input">

                            {{-- ================= SECTION 1: HEADER SURAT ================= --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Informasi Surat</legend>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No. Surat Penawaran <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('quotation_letter_number') is-invalid @enderror"
                                            name="quotation_letter_number" value="{{ old('quotation_letter_number') }}"
                                            placeholder="Cth. 001/MAP/IX/2025" required>
                                        @error('quotation_letter_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('letter_date') is-invalid @enderror"
                                            name="letter_date" value="{{ old('letter_date', date('Y-m-d')) }}" required>
                                        @error('letter_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Perihal <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                            name="subject" value="{{ old('subject', 'Surat Penawaran Harga') }}" required>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipe Surat</label>
                                        <input type="text" class="form-control" name="letter_type" value="Map"
                                            readonly>
                                        @error('letter_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            {{-- ================= SECTION 2: PENERIMA ================= --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Data Penerima (Customer)</legend>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('recipient_company_name') is-invalid @enderror"
                                            name="recipient_company_name" value="{{ old('recipient_company_name') }}"
                                            required>
                                        @error('recipient_company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Penerima (UP) <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('recipient_attention_to') is-invalid @enderror"
                                            name="recipient_attention_to" value="{{ old('recipient_attention_to') }}"
                                            required>
                                        @error('recipient_attention_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Alamat Lengkap</label>
                                        <input type="text"
                                            class="form-control mb-2 @error('recipient_address_line1') is-invalid @enderror"
                                            name="recipient_address_line1" value="{{ old('recipient_address_line1') }}"
                                            placeholder="Baris 1 (Jalan, No, RT/RW)">

                                        <input type="text"
                                            class="form-control @error('recipient_address_line2') is-invalid @enderror"
                                            name="recipient_address_line2" value="{{ old('recipient_address_line2') }}"
                                            placeholder="Baris 2 (Keluran, Kecamatan)">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('recipient_province') is-invalid @enderror"
                                            name="recipient_province" value="{{ old('recipient_province') }}" required>
                                        @error('recipient_province')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Kota/Kabupaten <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('recipient_city') is-invalid @enderror"
                                            name="recipient_city" value="{{ old('recipient_city') }}" required>
                                        @error('recipient_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('recipient_postal_code') is-invalid @enderror"
                                            name="recipient_postal_code" value="{{ old('recipient_postal_code') }}"
                                            required>
                                        @error('recipient_postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            {{-- ================= SECTION 2.5: PEMBUKA SURAT ================= --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Pembuka Surat (Opening)</legend>
                                <input id="letter_opening_input" type="hidden" name="letter_opening"
                                    value="{{ old('letter_opening') }}" required>
                                <trix-editor input="letter_opening_input"
                                    placeholder="Tuliskan pembuka surat disini..."></trix-editor>
                            </fieldset>

                            {{-- ================= SECTION 3: ITEMS (AJAX) ================= --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Detail Barang (MAP)</legend>

                                <div class="alert alert-info py-2 small d-flex justify-content-between align-items-center">
                                    <span><i class="feather-info me-1"></i> Ketik Kode/Nama Barang. PPN Aktif:
                                        <strong>{{ $taxRate }}%</strong>. Tipe, SKU, Size, dan Warranty bersifat
                                        opsional.</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="items-table">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="25%">Item</th>
                                                <th width="12%">SKU Number</th>
                                                <th width="8%">Size</th>
                                                <th width="10%">Warranty</th>
                                                <th width="10%">Item Type</th> {{-- Kolom Baru --}}
                                                <th width="15%">Harga Satuan (Rp)</th>
                                                <th width="8%">Disc (%)</th>
                                                <th width="15%">Total (Rp)</th>
                                                <th width="5%" class="text-center">#</th>
                                            </tr>
                                        </thead>
                                        <tbody id="items-container">
                                            {{-- Baris akan ditambahkan via JS --}}
                                        </tbody>
                                    </table>
                                </div>
                                @error('items')
                                    <div class="text-danger small mb-2">{{ $message }}</div>
                                @enderror

                                <button type="button" class="btn btn-success btn-sm mt-3" onclick="addItemRow()">
                                    <i class="feather-plus me-1"></i> Tambah Barang
                                </button>
                            </fieldset>

                            {{-- ================= SECTION 4: NOTE & SIGNATURE ================= --}}
                            <div class="row">
                                <div class="col-md-8 mb-4">
                                    <fieldset class="p-3 border rounded-3 mb-3">
                                        <legend class="float-none w-auto px-2 fs-6 fw-bold">Catatan Surat (Note)</legend>
                                        <input id="letter_note_input" type="hidden" name="letter_note"
                                            value="{{ old('letter_note') }}">
                                        <trix-editor input="letter_note_input"
                                            placeholder="Tuliskan catatan tambahan disini..."></trix-editor>
                                    </fieldset>

                                    <fieldset class="p-3 border rounded-3">
                                        <legend class="float-none w-auto px-2 fs-6 fw-bold">Penutup Surat (Ending)</legend>
                                        <input id="letter_ending_input" type="hidden" name="letter_ending"
                                            value="{{ old('letter_ending') }}">
                                        <trix-editor input="letter_ending_input"
                                            placeholder="Tuliskan penutup surat disini..."></trix-editor>
                                    </fieldset>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <fieldset class="p-3 border rounded-3 h-100">
                                        <legend class="float-none w-auto px-2 fs-6 fw-bold">Otorisasi</legend>
                                        <div class="mb-3">
                                            <label class="form-label">Penanda Tangan <span
                                                    class="text-danger">*</span></label>
                                            <select
                                                class="form-select select-ttd @error('signature_id') is-invalid @enderror"
                                                name="signature_id" required>
                                                <option value="" disabled selected>-- Pilih User --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->ID }}"
                                                        {{ old('signature_id') == $user->ID ? 'selected' : '' }}>
                                                        {{ $user->Nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('signature_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label
                                                class="form-label d-block fw-bold small text-uppercase text-muted">Metode
                                                Tanda Tangan</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="sig_method"
                                                    id="method_draw" value="draw" checked
                                                    onchange="toggleSignatureMethod()">
                                                <label class="form-check-label" for="method_draw">Gambar (Draw)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="sig_method"
                                                    id="method_upload" value="upload" onchange="toggleSignatureMethod()">
                                                <label class="form-check-label" for="method_upload">Upload File</label>
                                            </div>
                                        </div>

                                        <div id="container-draw">
                                            <div class="signature-wrapper">
                                                <canvas id="signature-canvas" class="signature-pad" width="300"
                                                    height="200"></canvas>
                                            </div>
                                            <button type="button" class="btn btn-outline-danger btn-sm mt-2 w-100"
                                                id="clear-signature">
                                                <i class="feather-trash-2 me-1"></i> Hapus / Ulangi
                                            </button>
                                        </div>

                                        <div id="container-upload" class="d-none">
                                            <label class="form-label">Upload Gambar Tanda Tangan</label>
                                            <input type="file" class="form-control" name="signature_file"
                                                id="signature_file" accept=".png, .jpg, .jpeg">
                                            <div class="form-text text-muted small mt-1">Format: JPG/PNG. Latar belakang
                                                transparan disarankan.</div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('quotation-letter.map.index') }}" class="btn btn-light">Batal</a>
                                <button type="button" class="btn btn-primary" onclick="confirmSubmit()">
                                    <i class="feather-save me-2"></i> Simpan Surat Penawaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= TEMPLATE ROW JS ================= --}}
    <template id="item-row-template">
        <tr class="quotation-row">
            <td>
                <select class="form-select item-select-ajax" required>
                    {{-- Option akan diload otomatis oleh Select2 --}}
                </select>

                {{-- Input Hidden untuk menyimpan Item ID Murni --}}
                <input type="hidden" class="real-item-id" name="items[INDEX][item_id]">

                {{-- Input Hidden untuk VALIDASI DUPLIKAT (Composite ID) --}}
                <input type="hidden" class="validation-id">
            </td>
            <td>
                <input type="text" class="form-control" name="items[INDEX][sku_number]" placeholder="SKU">
            </td>
            <td>
                <input type="text" class="form-control" name="items[INDEX][size_number]" placeholder="Size">
            </td>
            <td>
                <input type="text" class="form-control" name="items[INDEX][warranty_period]" placeholder="Warranty">
            </td>

            {{-- KOLOM BARU: INPUT MANUAL ITEM TYPE --}}
            <td>
                <input type="text" class="form-control" name="items[INDEX][item_type]" placeholder="Tipe">
            </td>

            <td>
                {{-- Harga Satuan (Otomatis dari Pricelist) --}}
                <input type="number" class="form-control unit-price text-end bg-light" name="items[INDEX][unit_price]"
                    readonly required>
            </td>
            <td>
                <input type="number" class="form-control discount-percentage text-center"
                    name="items[INDEX][discount_percentage]" value="0" min="0" max="100" required
                    oninput="calculateRow(this)">
            </td>
            <td>
                <input type="number" class="form-control total-price text-end bg-light" name="items[INDEX][total_price]"
                    readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                    <i class="feather-trash"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("trix-file-accept", function(event) {
            event.preventDefault();
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.select-ttd').select2({
                placeholder: 'Pilih Tanda Tangan'
            });
        });
    </script>

    <script>
        // ==========================================
        // CONFIGURATIONS
        // ==========================================
        const globalTaxRate = {{ $taxRate }};
        let itemIndex = 0;

        // ==========================================
        // 1. SETUP SIGNATURE PAD (CANVAS)
        // ==========================================
        const canvas = document.getElementById('signature-canvas');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)'
        });

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        document.getElementById('clear-signature').addEventListener('click', () => signaturePad.clear());

        function toggleSignatureMethod() {
            const isDraw = document.getElementById('method_draw').checked;
            const containerDraw = document.getElementById('container-draw');
            const containerUpload = document.getElementById('container-upload');

            if (isDraw) {
                containerDraw.classList.remove('d-none');
                containerUpload.classList.add('d-none');
            } else {
                containerDraw.classList.add('d-none');
                containerUpload.classList.remove('d-none');
            }
        }

        // ==========================================
        // 2. SETUP ITEMS (LOGIKA BARU)
        // ==========================================

        function initSelect2(element) {
            $(element).select2({
                theme: 'bootstrap-5',
                placeholder: '-- Cari Kode / Nama Barang --',
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route('api.map-items.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // --- EVENT SAAT ITEM DIPILIH ---
            $(element).on('select2:select', function(e) {
                const data = e.params.data;
                // data.id = "ITEM01|COUNTER" (Unik Composite)
                // data.real_item_id = "ITEM01"
                // data.price_category = "COUNTER"

                const row = $(this).closest('tr');

                // 1. CEK DUPLIKAT (Berdasarkan ID Composite: ItemID + PriceID)
                if (checkDuplicateItem(data.id, this)) {
                    // Jika kombinasi Item+Harga sudah ada, reset
                    $(this).val(null).trigger('change');
                    Swal.fire({
                        icon: 'error',
                        title: 'Item Duplikat',
                        text: `Item "${data.real_item_id}" dengan kategori harga "${data.price_category}" sudah dipilih. Silakan pilih kategori harga lain jika ingin menambahkan item yang sama.`,
                    });
                    return;
                }

                // 2. ISI DATA KE ROW
                row.find('.real-item-id').val(data.real_item_id); // Untuk Backend (ItemID murni)
                row.find('.validation-id').val(data.id); // Untuk Validasi JS (ItemID|PriceID)
                row.find('.unit-price').val(parseFloat(data.price) || 0);

                // 3. HITUNG ULANG
                calculateRow(row.find('.discount-percentage')[0]);
            });

            // --- EVENT SAAT ITEM DI-CLEAR ---
            $(element).on('select2:clear', function(e) {
                const row = $(this).closest('tr');
                row.find('.real-item-id').val('');
                row.find('.validation-id').val(''); // Clear validasi ID
                row.find('.unit-price').val(0);
                row.find('.total-price').val(0);
            });
        }

        // FUNGSI CEK DUPLIKAT (LOGIKA DIPERBAIKI)
        // Kita mengecek key unik (ItemID + PriceID)
        function checkDuplicateItem(newCompositeId, currentElement) {
            let isDuplicate = false;

            // Loop semua input hidden dengan class .validation-id
            $('.validation-id').each(function() {
                // Jangan bandingkan dengan baris diri sendiri
                if ($(this).closest('tr').is($(currentElement).closest('tr'))) return;

                const existingId = $(this).val();

                // Jika ID uniknya sama persis ("A01|COUNTER" == "A01|COUNTER"), maka duplikat
                if (existingId && existingId === newCompositeId) {
                    isDuplicate = true;
                }
            });

            return isDuplicate;
        }

        function addItemRow() {
            const template = document.getElementById('item-row-template');
            const clone = template.content.cloneNode(true);
            const container = document.getElementById('items-container');

            // Ganti placeholder INDEX
            const inputs = clone.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.name = input.name.replace('INDEX', itemIndex);
            });

            container.appendChild(clone);

            // Init Select2 pada baris baru
            const rows = container.querySelectorAll('tr');
            const lastRow = rows[rows.length - 1];
            const selectInLastRow = lastRow.querySelector('.item-select-ajax');

            initSelect2(selectInLastRow);
            itemIndex++;
        }

        function removeRow(btn) {
            const row = btn.closest('tr');
            const select = row.querySelector('.item-select-ajax');
            if ($(select).hasClass("select2-hidden-accessible")) {
                $(select).select2('destroy');
            }
            row.remove();
        }

        function calculateRow(element) {
            const row = element.closest('tr');
            const price = parseFloat(row.querySelector('.unit-price').value) || 0;
            const discountPercent = parseFloat(row.querySelector('.discount-percentage').value) || 0;

            const discountAmount = price * (discountPercent / 100);
            const priceAfterDisc = price - discountAmount;
            const taxAmount = priceAfterDisc * (globalTaxRate / 100);
            const total = priceAfterDisc + taxAmount;

            row.querySelector('.total-price').value = total.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', () => {
            addItemRow();
        });

        // ==========================================
        // 3. SUBMIT LOGIC
        // ==========================================
        function confirmSubmit() {
            const form = document.getElementById('create-quotation-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const isDraw = document.getElementById('method_draw').checked;
            if (isDraw) {
                if (signaturePad.isEmpty()) {
                    Swal.fire('Error', 'Silakan tanda tangan pada area canvas.', 'warning');
                    return;
                }
                document.getElementById('signature_base64_input').value = signaturePad.toDataURL('image/png');
                document.getElementById('signature_file').value = '';
            } else {
                if (document.getElementById('signature_file').files.length === 0) {
                    Swal.fire('Error', 'Silakan upload file tanda tangan.', 'warning');
                    return;
                }
            }

            Swal.fire({
                title: 'Simpan Data?',
                text: "Pastikan data sudah benar. Total harga akan otomatis memperhitungkan PPN " + globalTaxRate +
                    "%.",
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
                        didOpen: () => Swal.showLoading()
                    });
                }
            });
        }
    </script>
@endpush
