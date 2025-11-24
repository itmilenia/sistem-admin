<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Brand Performance</title>
</head>

<body>

    {{-- Tabel Header / Judul --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="6" style="font-size: 16px; font-weight: bold; text-align: center; border: none;">
                LAPORAN BRAND PERFORMANCE
            </td>
        </tr>
        <tr>
            <td colspan="6" style="font-weight: bold; text-align: center; border: none;">
                {{ $companyName }}
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

    {{-- Tabel Data --}}
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th width="5"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    No</th>
                <th width="15"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    ID Brand</th>
                <th width="35"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Nama Brand</th>
                <th width="20"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Sale Amount (Rp)</th>
                <th width="20"
                    style="border: 1px solid #000000; padding: 5px; background-color: #cccccc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Return Amount (Rp)</th>
                <th width="20"
                    style="border: 1px solid #000000; padding: 5px; background-color: #ffffcc; font-weight: bold; text-align: center; vertical-align: middle;">
                    Net Amount (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandSale = 0;
                $grandRetur = 0;
                $grandNet = 0;
            @endphp

            @forelse($brandData as $index => $row)
                <tr>
                    <td style="border: 1px solid #000000; padding: 5px; text-align: center;">{{ $index + 1 }}</td>

                    {{-- mso-number-format memaksa excel membaca sebagai text --}}
                    <td style="border: 1px solid #000000; padding: 5px; text-align: center; mso-number-format:'\@'">
                        {{ $row->MFIB_BrandID }}</td>

                    <td style="border: 1px solid #000000; padding: 5px;">{{ $row->brand_name ?? 'N/A' }}</td>

                    {{-- Kolom Angka --}}
                    <td style="border: 1px solid #000000; padding: 5px; text-align: right;">
                        {{ number_format($row->sale_amount, 2, '.', ',') }}</td>

                    {{-- Kolom Return (Merah) --}}
                    <td style="border: 1px solid #000000; padding: 5px; text-align: right; color: #ff0000;">
                        {{ number_format($row->retur_amount, 2, '.', ',') }}</td>

                    {{-- Kolom Net (Bold & Kuning) --}}
                    <td
                        style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; background-color: #ffffcc;">
                        {{ number_format($row->net_amount, 2, '.', ',') }}</td>
                </tr>

                @php
                    $grandSale += $row->sale_amount;
                    $grandRetur += $row->retur_amount;
                    $grandNet += $row->net_amount;
                @endphp
            @empty
                <tr>
                    <td colspan="6" style="border: 1px solid #000000; padding: 5px; text-align: center;">Tidak ada
                        data brand pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                {{-- Grand Total Label (Background Header Abu-abu) --}}
                <td colspan="3"
                    style="border: 1px solid #000000; padding: 5px; text-align: center; font-weight: bold; background-color: #e0e0e0;">
                    GRAND TOTAL</td>

                {{-- Grand Total Sales (Background Header Abu-abu) --}}
                <td
                    style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; background-color: #e0e0e0;">
                    {{ number_format($grandSale, 2, '.', ',') }}</td>

                {{-- Grand Total Return (Background Header Abu-abu & Merah) --}}
                <td
                    style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; color: #ff0000; background-color: #e0e0e0;">
                    {{ number_format($grandRetur, 2, '.', ',') }}</td>

                {{-- Grand Total Net (Background Kuning) --}}
                <td
                    style="border: 1px solid #000000; padding: 5px; text-align: right; font-weight: bold; background-color: #ffffcc;">
                    {{ number_format($grandNet, 2, '.', ',') }}</td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
