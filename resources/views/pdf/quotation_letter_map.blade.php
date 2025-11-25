<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Penawaran {{ $quotationLetter->quotation_letter_number }}</title>
    <style>
        @page {
            margin: 120px 60px 80px 60px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0px;
            right: 0px;
            height: 90px;
            text-align: center;
            border-bottom: 5px solid #1A3378;
        }

        header img {
            height: 60px;
            width: auto;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 60px;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #e0e0e0;
            text-align: center;
            font-weight: bold;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mb-1 {
            margin-bottom: 5px;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        /* LIST STYLING */
        ul {
            margin: 0;
            padding-left: 15px;
        }

        li {
            margin-bottom: 2px;
        }

        /* SIGNATURE */
        .signature-img {
            height: 60px;
            display: block;
            margin: 5px 0;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    <header>
        <img src="{{ public_path('assets/images/logo/logo-map-quotation.png') }}" alt="Logo Milenia">
    </header>

    <footer>
        <table style="border: none; width: 100%; border-top: 1px solid #000;">
            <tr>
                <td style="border: none; width: 70%; vertical-align: bottom; color: #17365D">
                    <strong style="font-size: 10px;">Corporate Office</strong><br>
                    Jl. Pecenongan Raya No. 57 Gambir Jakarta Pusat 10130<br>
                    Telp. : (62-21) 6310770 / 6310769
                </td>
            </tr>
        </table>
    </footer>

    <main>
        {{-- Tanggal --}}
        <div class="mb-2">
            Jakarta, {{ \Carbon\Carbon::parse($quotationLetter->letter_date)->translatedFormat('d F Y') }}
        </div>

        {{-- Info Nomor --}}
        <table style="width: 100%; border: none; margin-bottom: 15px;">
            <tr>
                <td style="border: none; width: 60px;">Nomor</td>
                <td style="border: none;">: {{ $quotationLetter->quotation_letter_number }}</td>
            </tr>
            <tr>
                <td style="border: none;">Lamp</td>
                <td style="border: none;">: 1 Halaman</td>
            </tr>
            <tr>
                <td style="border: none;">Perihal</td>
                <td style="border: none;">: {{ $quotationLetter->subject }}</td>
            </tr>
        </table>

        {{-- Penerima --}}
        <div class="mb-2">
            Kepada Yth,<br>
            {{ $quotationLetter->recipient_company_name }}<br>
            {{ $quotationLetter->recipient_address_line1 }}<br>
            @if ($quotationLetter->recipient_address_line2)
                {{ $quotationLetter->recipient_address_line2 }}<br>
            @endif
            {{ $quotationLetter->recipient_city }} {{ $quotationLetter->recipient_postal_code }}<br>
            <strong>Up : {{ $quotationLetter->recipient_attention_to }}</strong>
        </div>

        <div class="mb-2">
            @if ($quotationLetter->letter_opening)
                <p class="mb-2">Dengan Hormat,</p>
                {!! $quotationLetter->letter_opening !!}
            @else
                <p class="mb-2">Dengan Hormat,</p>
                <p class="mb-2">Bersama dengan ini, kami dari PT. MILENIA MEGA MANDIRI bermaksud menyampaikan
                    penawaran harga sebagai berikut :</p>
            @endif
        </div>

        {{-- TABEL DATA --}}
        <table class="data-table mb-2">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="15%">Sku</th>
                    <th width="30%">Nama Barang</th>
                    <th width="8%">Size</th>
                    <th width="10%">Type</th>
                    <th width="15%">Harga</th>
                    <th width="5%">Disc</th>
                    <th width="12%">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach ($quotationLetter->details as $index => $detail)
                    @php $grandTotal += $detail->total_price; @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $detail->sku_number }}</td>
                        <td>{{ $detail->itemMilenia->MFIMA_Description ?? ($detail->itemMap->MFIMA_Description ?? $detail->item_id) }}
                        </td>
                        <td class="text-center">{{ $detail->size_number ?? '-' }}</td>
                        <td class="text-center">{{ $detail->item_type }}</td>
                        <td class="text-right">{{ number_format($detail->unit_price, 0, '.', ',') }}</td>
                        <td class="text-center">
                            {{ $detail->discount_percentage > 0 ? $detail->discount_percentage + 0 . '%' : '' }}
                        </td>
                        <td class="text-right">{{ number_format($detail->total_price, 0, '.', ',') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="text-center fw-bold">Grand Total</td>
                    <td class="text-right fw-bold">{{ number_format($grandTotal, 0, '.', ',') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- NOTES / NB --}}
        <div style="margin-bottom: 20px; page-break-inside: avoid;">
            @if ($quotationLetter->letter_note)
                {!! $quotationLetter->letter_note !!}
            @endif
        </div>

        <div class="mb-2">
            @if ($quotationLetter->letter_ending)
                {!! $quotationLetter->letter_ending !!}
            @else
                <p class="mb-2">Demikian surat penawaran kerjasama dari kami. Atas perhatian Bapak, kami ucapkan
                    terima kasih.</p>
            @endif
        </div>

        {{-- TANDA TANGAN --}}
        <div style="margin-top: 20px; page-break-inside: avoid;">
            <p>Hormat Kami,</p>

            <div style="height: 70px; width: 150px;">
                @if ($quotationLetter->signature_path)
                    <img src="{{ public_path('storage/' . $quotationLetter->signature_path) }}" class="signature-img"
                        alt="Tanda Tangan">
                @endif
            </div>

            <p style="font-weight: bold; text-decoration: underline; margin-bottom: 0;">
                {{ $quotationLetter->signer->Nama ?? 'Nama Sales' }}
            </p>
            <span style="font-size: 10px;">Operational Manager</span>
        </div>
    </main>

</body>

</html>
