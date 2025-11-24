<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penjualan</title>
</head>

<body>

    {{-- Judul Laporan --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="6" style="font-size: 16px; font-weight: bold; text-align: center; border: none;">
                LAPORAN PENJUALAN PER SALES
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; border: none;">
                Jenis Perusahaan: {{ $companyType }}
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
        </tr> {{-- Spasi kosong --}}
    </table>

    {{-- Tabel Data --}}
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                {{-- Header Style: Background Abu-abu (#cccccc), Bold, Center, Border Hitam --}}
                <th width="5"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    No</th>
                <th width="15"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    ID Sales</th>
                <th width="30"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Nama Salesman</th>
                <th width="20"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Sale Amount</th>
                <th width="20"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Return Amount</th>
                <th width="20"
                    style="border: 1px solid #000000; padding: 5px; background-color: #ffffcc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandSale = 0;
                $grandRet = 0;
                $grandNet = 0;
            @endphp

            @foreach ($salesData as $index => $row)
                <tr>
                    <td style="border: 1px solid #000000; padding: 5px; text-align: center;">{{ $index + 1 }}</td>

                    {{-- mso-number-format:\@ memaksa excel membaca sebagai text --}}
                    <td style="border: 1px solid #000000; padding: 5px; mso-number-format:'\@';">
                        {{ $row->SOIVD_SalesmanID }}</td>

                    <td style="border: 1px solid #000000; padding: 5px;">{{ $row->salesman_name }}</td>

                    <td style="border: 1px solid #000000; padding: 5px; text-align: right;">
                        {{ number_format($row->total_amount, 2, '.', ',') }}</td>

                    {{-- Return Amount Merah --}}
                    <td style="border: 1px solid #000000; padding: 5px; text-align: right; color: #ff0000;">
                        {{ number_format($row->total_return_amount, 2, '.', ',') }}</td>

                    {{-- Net Amount Bold & Background Kuning --}}
                    <td
                        style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; background-color: #ffffcc;">
                        {{ number_format($row->net_amount, 2, '.', ',') }}</td>
                </tr>

                @php
                    $grandSale += $row->total_amount;
                    $grandRet += $row->total_return_amount;
                    $grandNet += $row->net_amount;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                {{-- Grand Total Label --}}
                <td colspan="3"
                    style="border: 1px solid #000000; padding: 5px; text-align: center; font-weight: bold; background-color: #e0e0e0;">
                    GRAND TOTAL</td>

                {{-- Total Sales --}}
                <td
                    style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; background-color: #e0e0e0;">
                    {{ number_format($grandSale, 2, '.', ',') }}
                </td>

                {{-- Total Return (Merah) --}}
                <td
                    style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; color: #ff0000; background-color: #e0e0e0;">
                    {{ number_format($grandRet, 2, '.', ',') }}
                </td>

                {{-- Total Net (Kuning Lebih Gelap dikit #ffff99 sesuai kode asli) --}}
                <td
                    style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; background-color: #ffff99;">
                    {{ number_format($grandNet, 2, '.', ',') }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
