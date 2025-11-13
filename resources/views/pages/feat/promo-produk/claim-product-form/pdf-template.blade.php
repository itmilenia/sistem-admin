<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Form Klaim Produk Rusak - {{ $claim->id }}</title>
    <style>
        /* CSS Wajib untuk DOMPDF */
        @page {
            margin: 25px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        /* Struktur */
        .container {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h4,
        .header h5 {
            margin: 0;
            padding: 0;
        }

        .header h4 {
            font-size: 14px;
            margin-bottom: 3px;
        }

        .header h5 {
            font-size: 12px;
            font-weight: normal;
        }

        /* Info Header */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .info-table .label {
            width: 120px;
            /* Lebar label */
        }

        .info-table .separator {
            width: 10px;
            /* Lebar : */
        }

        .info-table .data {
            border-bottom: 1px solid #555;
            font-weight: bold;
        }

        /* Tabel Produk */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .product-table th,
        .product-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .product-table th {
            background-color: #f0f0f0;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
        }

        .product-table td {
            font-size: 10px;
        }

        .product-table .text-center {
            text-align: center;
        }

        .product-table .nowrap {
            white-space: nowrap;
        }

        /* Info Checker */
        .checker-info {
            margin-top: 20px;
        }

        .checker-info p {
            margin: 0 0 5px 0;
        }

        .checker-table {
            width: 100%;
            border-collapse: collapse;
        }

        .checker-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .checker-table .label {
            width: 120px;
        }

        .checker-table .separator {
            width: 10px;
        }

        .checker-table .data {
            font-weight: bold;
        }

        /* Tanda Tangan */
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }

        .signature-box {
            width: 33.33%;
            float: left;
            text-align: center;
            height: 120px;
            /* Tinggi untuk TTD */
        }

        .signature-box img {
            max-height: 80px;
            max-width: 150px;
        }

        .signature-box .signature-name {
            margin-top: 5px;
            font-weight: bold;
        }

        .footer {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h4>FORM KLAIM PRODUK RUSAK</h4>
            {{-- <h5>DIVISI AUTOCARE - SONAX</h5> --}}
        </div>

        <table class="info-table">
            <tbody>
                <tr>
                    <td class="label">Nama Sales</td>
                    <td class="separator">:</td>
                    <td class="data">{{ $claim->sales->Nama ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Toko</td>
                    <td class="separator">:</td>
                    <td class="data">{{ $claim->retail_name }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Klaim</td>
                    <td class="separator">:</td>
                    <td class="data">{{ \Carbon\Carbon::parse($claim->claim_date)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Daftar Produk</td>
                    <td class="separator">:</td>
                    <td class="data">
                        {{-- Mengambil nama produk pertama seperti di gambar --}}
                        @if ($claim->claimDetails->count())
                            {{ $claim->claimDetails->map(function ($detail) use ($products) {
                                    return $products[$detail->product_id]->MFIMA_Description ?? 'N/A';
                                })->implode(', ') }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="product-table">
            <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th class="text-center">Qty</th>
                    <th>No.Invoice</th>
                    <th>Tgl. Pembelian</th>
                    <th>Tgl. Pengiriman</th>
                    <th>Jenis Kerusakan atau Alasan Retur</th>
                </tr>
            </thead>
            <tbody>
                @php $minRows = 4; @endphp
                @forelse ($claim->claimDetails as $detail)
                    <tr>
                        <td class="nowrap">{{ $detail->product_id }}</td>
                        <td>{{ $products[$detail->product_id]->MFIMA_Description ?? 'N/A' }}</td>
                        <td class="text-center nowrap">{{ $detail->quantity }}
                            {{ $products[$detail->product_id]->MFIMA_InvUM }}</td>
                        <td class="nowrap text-center">{{ $detail->invoice_id }}</td>
                        <td class="nowrap">
                            {{ $detail->order_date ? \Carbon\Carbon::parse($detail->order_date)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="nowrap">
                            {{ $detail->delivery_date ? \Carbon\Carbon::parse($detail->delivery_date)->format('d-m-Y') : '-' }}
                        </td>
                        <td>{{ $detail->return_reason }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada detail produk.</td>
                    </tr>
                @endforelse

                {{-- Tambahkan baris kosong agar terlihat seperti form --}}
                @for ($i = $claim->claimDetails->count(); $i < $minRows; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="checker-info">
            <p>Dengan ini, produk sudah dicek dan dinyatakan rusak oleh:</p>
            <table class="checker-table">
                <tbody>
                    <tr>
                        <td class="label">Nama</td>
                        <td class="separator">:</td>
                        <td class="data">{{ $claim->checker->Nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Pengecekan</td>
                        <td class="separator">:</td>
                        <td class="data">
                            {{ $claim->verification_date ? \Carbon\Carbon::parse($claim->verification_date)->translatedFormat('d / F / Y') : '..... / ..... / .....' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Comm.</td>
                        <td class="separator">:</td>
                        <td class="data">{{ $claim->verification_result }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <table class="signature-section"
            style="width: 100%; margin-top: 30px; text-align: center; border-collapse: collapse;">
            <tr>
                <td>Ttd,<br><br>
                    @if ($checkerSignaturePath)
                        <img src="{{ $checkerSignaturePath }}" alt="TTD Checker"
                            style="width: 150px; height: 80px; object-fit: contain; margin-top: 5px;">
                    @else
                        <div style="width:150px; height:80px; margin:5px auto;"></div>
                    @endif
                    <div class="signature-name" style="margin-top: 5px;">
                        Checker,
                    </div>
                </td>

                <td>Ttd,<br><br>
                    @if ($salesSignaturePath)
                        <img src="{{ $salesSignaturePath }}" alt="TTD Sales"
                            style="width: 150px; height: 80px; object-fit: contain; margin-top: 5px;">
                    @else
                        <div style="width:150px; height:80px; margin:5px auto;"></div>
                    @endif
                    <div class="signature-name" style="margin-top: 5px;">
                        Sales,
                    </div>
                </td>

                <td>Ttd,<br><br>
                    @if ($salesHeadSignaturePath)
                        <img src="{{ $salesHeadSignaturePath }}" alt="TTD Sales Head"
                            style="width: 150px; height: 80px; object-fit: contain; margin-top: 5px;">
                    @else
                        <div style="width:150px; height:80px; margin:5px auto;"></div>
                    @endif
                    <div class="signature-name" style="margin-top: 5px;">
                        Head Sales,
                    </div>
                </td>
            </tr>
        </table>



        <div class="footer"></div>

    </div>
</body>

</html>
