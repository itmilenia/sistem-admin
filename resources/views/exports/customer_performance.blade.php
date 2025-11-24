<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Per Customer</title>
</head>

<body>

    {{-- HEADER LAPORAN --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="6" style="font-size: 16px; font-weight: bold; text-align: center; border: none;">
                LAPORAN PENJUALAN PER CUSTOMER
            </td>
        </tr>
        <tr>
            <td colspan="6" style="font-weight: bold; text-align: center; border: none;">
                {{ $companyType }}
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; border: none;">
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d
                {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="6" style="border: none;"></td>
        </tr>
    </table>

    {{-- TABEL DATA --}}
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th width="5"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    No</th>
                <th width="15"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    ID Customer</th>
                <th width="40"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Nama Customer</th>
                <th width="20"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Gross Sales (Rp)</th>
                <th width="20"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Return Amount (Rp)</th>
                <th width="20"
                    style="border: 1px solid #000000; padding: 5px; background-color: #ffffcc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Net Sales (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandSale = 0;
                $grandRetur = 0;
                $grandNet = 0;
            @endphp

            @forelse($customerData as $index => $row)
                <tr>
                    <td style="border: 1px solid #000000; padding: 5px; text-align: center;">{{ $index + 1 }}</td>

                    {{-- ID Customer: Pakai mso-number-format:'\@' agar format text (0 di depan tidak hilang) --}}
                    <td style="border: 1px solid #000000; padding: 5px; text-align: center; mso-number-format:'\@'">
                        {{ $row->MFCUS_CustomerID }}
                    </td>

                    {{-- Nama Customer --}}
                    <td style="border: 1px solid #000000; padding: 5px;">
                        {{ $row->customer_name ?? 'N/A' }}
                    </td>

                    {{-- Gross Sales --}}
                    <td style="border: 1px solid #000000; padding: 5px; text-align: right;">
                        {{ number_format($row->sale_amount, 2, '.', ',') }}
                    </td>

                    {{-- Return Amount (Warna Merah) --}}
                    <td style="border: 1px solid #000000; padding: 5px; text-align: right; color: #ff0000;">
                        {{ number_format($row->retur_amount, 2, '.', ',') }}
                    </td>

                    {{-- Net Sales (Bold & Background Kuning) --}}
                    <td
                        style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; background-color: #ffffcc;">
                        {{ number_format($row->net_amount, 2, '.', ',') }}
                    </td>
                </tr>

                @php
                    $grandSale += $row->sale_amount;
                    $grandRetur += $row->retur_amount;
                    $grandNet += $row->net_amount;
                @endphp
            @empty
                <tr>
                    <td colspan="6" style="border: 1px solid #000000; padding: 5px; text-align: center;">
                        Tidak ada data transaksi customer pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                {{-- Label Grand Total --}}
                <td colspan="3"
                    style="border: 1px solid #000000; padding: 5px; text-align: center; font-weight: bold; background-color: #e0e0e0;">
                    GRAND TOTAL
                </td>

                {{-- Total Sales --}}
                <td
                    style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; background-color: #e0e0e0;">
                    {{ number_format($grandSale, 2, '.', ',') }}
                </td>

                {{-- Total Return (Merah) --}}
                <td
                    style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; color: #ff0000; background-color: #e0e0e0;">
                    {{ number_format($grandRetur, 2, '.', ',') }}
                </td>

                {{-- Total Net (Kuning) --}}
                <td
                    style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; background-color: #ffffcc;">
                    {{ number_format($grandNet, 2, '.', ',') }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
