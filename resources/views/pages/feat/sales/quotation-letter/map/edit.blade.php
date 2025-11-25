@extends('layouts.app')

@section('title', 'Edit Surat Penawaran: ' . $quotationLetter->quotation_letter_number)

@push('styles')
    {{-- 1. Trix Editor CSS --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

    <style>
        .signature-pad {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            width: 100%;
            height: 200px;
            background-color: #f8f9fa;
            cursor: crosshair;
        }

        trix-editor {
            min-height: 150px;
        }

        .d-none {
            display: none !important;
        }

        .select2-container {
            width: 100% !important;
        }

        /* Highlight signature box if existing */
        .current-signature-box {
            border: 2px dashed #ced4da;
            background: #f8f9fa;
            padding: 10px;
            display: inline-block;
            border-radius: 8px;
        }

        .quotation-row,
        .quotation-row input,
        .quotation-row select {
            font-size: 14px !important;
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
                        <h5 class="card-title"><i class="feather-edit me-2"></i>Formulir Edit Data Surat</h5>
                    </div>

                    <div class="card-body">
                        <form id="edit-quotation-form" action="{{ route('quotation-letter.update', $quotationLetter->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Input Hidden: Base64 Signature (Jika pilih Draw baru) --}}
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
                                            name="quotation_letter_number"
                                            value="{{ old('quotation_letter_number', $quotationLetter->quotation_letter_number) }}"
                                            required>
                                        @error('quotation_letter_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('letter_date') is-invalid @enderror"
                                            name="letter_date"
                                            value="{{ old('letter_date', $quotationLetter->letter_date->format('Y-m-d')) }}"
                                            required>
                                        @error('letter_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Perihal (Subject) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                            name="subject" value="{{ old('subject', $quotationLetter->subject) }}" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipe Surat</label>
                                        <input type="text" class="form-control" name="letter_type"
                                            value="{{ $quotationLetter->letter_type }}" readonly>
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
                                            name="recipient_company_name"
                                            value="{{ old('recipient_company_name', $quotationLetter->recipient_company_name) }}"
                                            required>
                                        @error('recipient_company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">UP (Attention To) <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('recipient_attention_to') is-invalid @enderror"
                                            name="recipient_attention_to"
                                            value="{{ old('recipient_attention_to', $quotationLetter->recipient_attention_to) }}"
                                            required>
                                        @error('recipient_attention_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Alamat Lengkap</label>
                                        <input type="text"
                                            class="form-control mb-2 @error('recipient_address_line1') is-invalid @enderror"
                                            name="recipient_address_line1"
                                            value="{{ old('recipient_address_line1', $quotationLetter->recipient_address_line1) }}"
                                            placeholder="Jalan No, RT/RW">

                                        <input type="text"
                                            class="form-control @error('recipient_address_line2') is-invalid @enderror"
                                            name="recipient_address_line2"
                                            value="{{ old('recipient_address_line2', $quotationLetter->recipient_address_line2) }}"
                                            placeholder="Kelurahan, Kecamatan">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Kota <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('recipient_city') is-invalid @enderror"
                                            name="recipient_city"
                                            value="{{ old('recipient_city', $quotationLetter->recipient_city) }}"
                                            required>
                                        @error('recipient_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('recipient_province') is-invalid @enderror"
                                            name="recipient_province"
                                            value="{{ old('recipient_province', $quotationLetter->recipient_province) }}"
                                            required>
                                        @error('recipient_province')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('recipient_postal_code') is-invalid @enderror"
                                            name="recipient_postal_code"
                                            value="{{ old('recipient_postal_code', $quotationLetter->recipient_postal_code) }}"
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
                                    value="{{ old('letter_opening', $quotationLetter->letter_opening) }}" required>
                                <trix-editor input="letter_opening_input"
                                    placeholder="Tuliskan pembuka surat disini..."></trix-editor>
                            </fieldset>

                            {{-- ================= SECTION 3: ITEMS ================= --}}
                            <fieldset class="mb-4 p-3 border rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 fw-bold">Detail Barang (MAP)</legend>

                                <div class="alert alert-info py-2 small d-flex justify-content-between align-items-center">
                                    <span><i class="feather-info me-1"></i> PPN Aktif:
                                        <strong>{{ $taxRate }}%</strong>. Harga otomatis terhitung ulang saat
                                        disimpan.</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="items-table">
                                        <thead class="bg-light">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th width="25%">Item</th>
                                                    <th width="12%">SKU Number</th>
                                                    <th width="8%">Size</th>
                                                    <th width="10%">Item Type</th>
                                                    <th width="15%">Harga (Excl. PPN)</th>
                                                    <th width="8%">Disc (%)</th>
                                                    <th width="15%">Total (Incl. PPN)</th>
                                                    <th width="5%" class="text-center">#</th>
                                                </tr>
                                            </thead>
                                        </thead>
                                        <tbody id="items-container">
                                            @foreach ($quotationLetter->details as $index => $detail)
                                                {{-- PREPARE DATA DISPLAY --}}
                                                @php
                                                    // 1. Ambil Nama Barang dari relasi (fallback jika null)
                                                    $itemName =
                                                        $detail->itemMap->MFIMA_Description ?? 'Item Tidak Ditemukan';

                                                    // 2. Ambil Kategori Harga (fallback jika null)
                                                    $priceCategory = $detail->pricelistMap->SOMPD_PriceID ?? '-';

                                                    // 3. Susun Text Tampilan: "A001 - Baju Koko (COUNTER)"
                                                    $displayText =
                                                        $detail->item_id .
                                                        ' - ' .
                                                        $itemName .
                                                        ' (' .
                                                        $priceCategory .
                                                        ')';

                                                    // 4. Susun Composite ID untuk Select2: "A001|COUNTER"
                                                    $compositeId = $detail->item_id . '|' . $priceCategory;
                                                @endphp

                                                <tr class="item-row quotation-row">
                                                    <td>
                                                        {{-- Select2 Option dibuat Manual agar langsung terseleksi --}}
                                                        <select class="form-select item-select-ajax" required>
                                                            <option value="{{ $compositeId }}" selected>
                                                                {{ $displayText }}</option>
                                                        </select>

                                                        {{-- Input Hidden Wajib (Backend) --}}
                                                        <input type="hidden" class="real-item-id"
                                                            name="items[{{ $index }}][item_id]"
                                                            value="{{ $detail->item_id }}">

                                                        {{-- Input Hidden Validasi (Frontend) --}}
                                                        <input type="hidden" class="validation-id"
                                                            value="{{ $compositeId }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="items[{{ $index }}][sku_number]"
                                                            value="{{ $detail->sku_number }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="items[{{ $index }}][size_number]"
                                                            value="{{ $detail->size_number }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="items[{{ $index }}][item_type]"
                                                            value="{{ old('items.' . $index . '.item_type', $detail->item_type) }}"
                                                            placeholder="Tipe" required>
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                            class="form-control unit-price text-end bg-light"
                                                            name="items[{{ $index }}][unit_price]"
                                                            value="{{ $detail->unit_price }}" readonly required>
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                            class="form-control discount-percentage text-center"
                                                            name="items[{{ $index }}][discount_percentage]"
                                                            value="{{ $detail->discount_percentage + 0 }}" min="0"
                                                            max="100" required oninput="calculateRow(this)">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                            class="form-control total-price text-end bg-light"
                                                            name="items[{{ $index }}][total_price]"
                                                            value="{{ $detail->total_price }}" readonly>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="removeRow(this)">
                                                            <i class="feather-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
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
                                {{-- Note --}}
                                <div class="col-md-8 mb-4">
                                    <fieldset class="p-3 border rounded-3 mb-3">
                                        <legend class="float-none w-auto px-2 fs-6 fw-bold">Catatan Surat (Note)</legend>
                                        <input id="letter_note_input" type="hidden" name="letter_note"
                                            value="{{ old('letter_note', $quotationLetter->letter_note) }}" required>
                                        <trix-editor input="letter_note_input"
                                            placeholder="Tuliskan catatan tambahan..."></trix-editor>
                                    </fieldset>

                                    <fieldset class="p-3 border rounded-3">
                                        <legend class="float-none w-auto px-2 fs-6 fw-bold">Penutup Surat (Ending)</legend>
                                        <input id="letter_ending_input" type="hidden" name="letter_ending"
                                            value="{{ old('letter_ending', $quotationLetter->letter_ending) }}">
                                        <trix-editor input="letter_ending_input"
                                            placeholder="Tuliskan penutup surat disini..."></trix-editor>
                                    </fieldset>
                                </div>

                                {{-- Otorisasi --}}
                                <div class="col-md-4 mb-4">
                                    <fieldset class="p-3 border rounded-3 h-100">
                                        <legend class="float-none w-auto px-2 fs-6 fw-bold">Otorisasi</legend>

                                        {{-- Tanda Tangan Saat Ini --}}
                                        <div class="mb-3 text-center">
                                            <p class="mb-1 small fw-bold">Tanda Tangan Saat Ini:</p>
                                            <div class="current-signature-box">
                                                @if ($quotationLetter->signature_path)
                                                    <img src="{{ Storage::url($quotationLetter->signature_path) }}"
                                                        alt="Signature" style="max-height: 80px;">
                                                @else
                                                    <span class="text-muted small fst-italic">- Belum ada tanda tangan
                                                        -</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Penanda Tangan <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('signature_id') is-invalid @enderror"
                                                name="signature_id" required>
                                                <option value="" disabled>-- Pilih User --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->ID }}"
                                                        {{ old('signature_id', $quotationLetter->signature_id) == $user->ID ? 'selected' : '' }}>
                                                        {{ $user->Nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('signature_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Toggle Ubah Tanda Tangan --}}
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="change_signature_check"
                                                onchange="toggleChangeSignature()">
                                            <label class="form-check-label fw-bold small" for="change_signature_check">
                                                Ubah Tanda Tangan?
                                            </label>
                                        </div>

                                        {{-- Wrapper untuk Input Tanda Tangan Baru (Hidden by default) --}}
                                        <div id="new_signature_wrapper" class="d-none border-top pt-3 mt-2">
                                            <div class="mb-2">
                                                <label class="form-label d-block small text-muted">Metode Baru:</label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="sig_method"
                                                        id="method_draw" value="draw" checked
                                                        onchange="toggleSignatureMethod()">
                                                    <label class="form-check-label small" for="method_draw">Gambar
                                                        (Draw)</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="sig_method"
                                                        id="method_upload" value="upload"
                                                        onchange="toggleSignatureMethod()">
                                                    <label class="form-check-label small" for="method_upload">Upload
                                                        File</label>
                                                </div>
                                            </div>

                                            <div id="container-draw">
                                                <div class="signature-wrapper">
                                                    <canvas id="signature-canvas" class="signature-pad" width="300"
                                                        height="150"></canvas>
                                                </div>
                                                <button type="button" class="btn btn-outline-danger btn-sm mt-1 w-100"
                                                    id="clear-signature">Hapus / Ulangi</button>
                                            </div>

                                            <div id="container-upload" class="d-none">
                                                <input type="file" class="form-control form-control-sm"
                                                    name="signature_file" id="signature_file" accept=".png, .jpg, .jpeg">
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('quotation-letter.map.index') }}" class="btn btn-light">Batal</a>
                                <button type="button" class="btn btn-primary" onclick="confirmSubmit()">
                                    <i class="feather-save me-2"></i> Perbarui Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TEMPLATE ROW JS (SAMA SEPERTI CREATE) --}}
    <template id="item-row-template">
        <tr>
            <td>
                <select class="form-select item-select-ajax" required></select>
                <input type="hidden" class="real-item-id" name="items[INDEX][item_id]">
                <input type="hidden" class="validation-id">
            </td>
            <td><input type="text" class="form-control" name="items[INDEX][sku_number]" required></td>
            <td><input type="text" class="form-control" name="items[INDEX][size_number]"></td>

            {{-- KOLOM BARU DI TEMPLATE --}}
            <td><input type="text" class="form-control" name="items[INDEX][item_type]" placeholder="Tipe" required>
            </td>
            {{-- --------------------- --}}

            <td><input type="number" class="form-control unit-price text-end bg-light" name="items[INDEX][unit_price]"
                    readonly required></td>
            <td><input type="number" class="form-control discount-percentage text-center"
                    name="items[INDEX][discount_percentage]" value="0" min="0" max="100" required
                    oninput="calculateRow(this)"></td>
            <td><input type="number" class="form-control total-price text-end bg-light" name="items[INDEX][total_price]"
                    readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i
                        class="feather-trash"></i></button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const globalTaxRate = {{ $taxRate ?? 11 }};

        // Kita hitung index mulai dari jumlah item yg ada + 1 agar ID unik
        let itemIndex = {{ $quotationLetter->details->count() }};

        // --- SIGNATURE LOGIC ---
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

        // Toggle tampilkan form ubah signature
        function toggleChangeSignature() {
            const isChecked = document.getElementById('change_signature_check').checked;
            const wrapper = document.getElementById('new_signature_wrapper');
            if (isChecked) {
                wrapper.classList.remove('d-none');
                // Resize canvas setelah element terlihat agar ukurannya benar
                setTimeout(resizeCanvas, 0);
            } else {
                wrapper.classList.add('d-none');
                signaturePad.clear(); // Reset jika dibatalkan
                document.getElementById('signature_file').value = '';
            }
        }

        function toggleSignatureMethod() {
            const isDraw = document.getElementById('method_draw').checked;
            const containerDraw = document.getElementById('container-draw');
            const containerUpload = document.getElementById('container-upload');

            if (isDraw) {
                containerDraw.classList.remove('d-none');
                containerUpload.classList.add('d-none');
                setTimeout(resizeCanvas, 0);
            } else {
                containerDraw.classList.add('d-none');
                containerUpload.classList.remove('d-none');
            }
        }

        // --- ITEMS LOGIC ---
        function initSelect2(element) {
            $(element).select2({
                theme: 'bootstrap-5',
                placeholder: '-- Cari Item --',
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route('api.map-items.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                }
            });

            $(element).on('select2:select', function(e) {
                const data = e.params.data;
                const row = $(this).closest('tr');

                if (checkDuplicateItem(data.id, this)) {
                    $(this).val(null).trigger('change');
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplikat',
                        text: 'Item dengan kategori harga ini sudah ada.'
                    });
                    return;
                }

                row.find('.real-item-id').val(data.real_item_id);
                row.find('.validation-id').val(data.id);
                row.find('.unit-price').val(parseFloat(data.price) || 0);
                calculateRow(row.find('.discount-percentage')[0]);
            });

            $(element).on('select2:clear', function(e) {
                const row = $(this).closest('tr');
                row.find('.real-item-id').val('');
                row.find('.validation-id').val('');
                row.find('.unit-price').val(0);
                row.find('.total-price').val(0);
            });
        }

        function checkDuplicateItem(newCompositeId, currentElement) {
            let isDuplicate = false;
            $('.validation-id').each(function() {
                if ($(this).closest('tr').is($(currentElement).closest('tr'))) return;
                if ($(this).val() === newCompositeId) isDuplicate = true;
            });
            return isDuplicate;
        }

        function addItemRow() {
            const template = document.getElementById('item-row-template');
            const clone = template.content.cloneNode(true);
            const container = document.getElementById('items-container');

            clone.querySelectorAll('input, select').forEach(input => {
                input.name = input.name.replace('INDEX', itemIndex);
            });

            container.appendChild(clone);
            const rows = container.querySelectorAll('tr');
            initSelect2(rows[rows.length - 1].querySelector('.item-select-ajax'));
            itemIndex++;
        }

        function removeRow(btn) {
            const row = btn.closest('tr');
            // Jangan biarkan hapus jika hanya sisa 1 baris (opsional, tergantung requirement)
            // if ($('#items-container tr').length <= 1) { Swal.fire('Info', 'Minimal harus ada 1 barang.', 'info'); return; }

            const select = row.querySelector('.item-select-ajax');
            if (select && $(select).hasClass("select2-hidden-accessible")) $(select).select2('destroy');
            row.remove();
        }

        function calculateRow(element) {
            const row = element.closest('tr');
            const price = parseFloat(row.querySelector('.unit-price').value) || 0;
            const discountPercent = parseFloat(row.querySelector('.discount-percentage').value) || 0;
            const total = (price - (price * (discountPercent / 100))) * (1 + (globalTaxRate / 100)); // Incl PPN
            row.querySelector('.total-price').value = total.toFixed(2);
        }

        // --- INITIALIZATION ON LOAD ---
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Inisialisasi Select2 pada item-item lama
            $('.item-select-ajax').each(function() {
                initSelect2(this);
            });
        });

        // --- SUBMIT LOGIC ---
        function confirmSubmit() {
            const form = document.getElementById('edit-quotation-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Validasi Signature hanya jika dicentang "Ubah"
            const changeSig = document.getElementById('change_signature_check').checked;
            if (changeSig) {
                const isDraw = document.getElementById('method_draw').checked;
                if (isDraw) {
                    if (signaturePad.isEmpty()) {
                        Swal.fire('Error', 'Tanda tangan baru belum digambar.', 'warning');
                        return;
                    }
                    document.getElementById('signature_base64_input').value = signaturePad.toDataURL('image/png');
                } else {
                    if (document.getElementById('signature_file').files.length === 0) {
                        Swal.fire('Error', 'File tanda tangan belum dipilih.', 'warning');
                        return;
                    }
                }
            }

            Swal.fire({
                title: 'Konfirmasi Perubahan Data?',
                text: "Anda akan memperbarui data Surat Penawaran ini. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Perbarui!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            }).then(r => {
                if (r.isConfirmed) {
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
