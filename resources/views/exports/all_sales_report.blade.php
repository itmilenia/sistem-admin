<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    <table style="border-collapse: collapse; width: 100%;">
        @php
            $totalColumns = 3 + count($allBrands);
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);

            if ($start->year == $end->year) {
                if ($start->month == $end->month) {
                    $dateRange = $start->format('d') . ' - ' . $end->translatedFormat('d F Y');
                } else {
                    $dateRange = $start->translatedFormat('d F') . ' - ' . $end->translatedFormat('d F Y');
                }
            } else {
                $dateRange = $start->translatedFormat('d F Y') . ' - ' . $end->translatedFormat('d F Y');
            }
        @endphp

        <!-- HEADER LAPORAN -->
        <tr>
            <td colspan="{{ $totalColumns }}" rowspan="2"
                style="text-align: center; font-weight: bold; font-size: 12px; vertical-align:middle; border:1px solid #000;">
                {{ $description }}
            </td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td style="font-weight: bold; border:1px solid #000;">PERIODE</td>
            <td colspan="{{ $totalColumns - 1 }}" style="border:1px solid #000;">: {{ $dateRange }}</td>
        </tr>

        <!-- HEADER TABEL -->
        <thead>
            <tr>
                <th rowspan="2"
                    style="background-color:#C0E6F5; font-weight:bold; text-align:center; vertical-align:middle; border:1px solid #000; padding:6px;">
                    SALES</th>
                <th rowspan="2"
                    style="background-color:#C0E6F5; font-weight:bold; text-align:center; vertical-align:middle; border:1px solid #000; padding:6px;">
                    YEAR</th>
                <th colspan="{{ count($allBrands) }}"
                    style="background-color:#C0E6F5; font-weight:bold; text-align:center; vertical-align:middle; border:1px solid #000; padding:6px;">
                    BRAND</th>
                <th rowspan="2"
                    style="background-color:#C0E6F5; font-weight:bold; text-align:center; vertical-align:middle; border:1px solid #000; padding:6px;">
                    Grand Total</th>
            </tr>
            <tr>
                @foreach ($allBrands as $brand)
                    <th
                        style="background-color:#C0E6F5; font-weight:bold; text-align:center; border:1px solid #000; padding:6px;">
                        {{ $brand }}</th>
                @endforeach
            </tr>
        </thead>

        <!-- ISI TABEL -->
        <tbody>
            @if (empty($pivotData))
                <tr>
                    <td colspan="{{ $totalColumns }}" style="text-align:center; border:1px solid #000; padding:6px;">
                        Tidak ada data penjualan pada periode yang dipilih.</td>
                </tr>
            @else
                @php
                    $grandTotalByBrand = array_fill_keys($allBrands, 0);
                    $finalGrandTotal = 0;
                @endphp
                @foreach ($pivotData as $salesName => $years)
                    @php
                        $salesTotal = 0;
                        $firstYear = true;
                    @endphp
                    @foreach ($years as $year => $brandsData)
                        <tr>
                            {{-- Tampilkan nama sales hanya di baris tahun pertama --}}
                            <td style="border:1px solid #000; padding:6px; text-align:left;">
                                {{ $firstYear ? $salesName : '' }}</td>
                            <td style="border:1px solid #000; padding:6px; text-align:center;">{{ $year }}</td>
                            @php $yearTotal = 0; @endphp
                            {{-- Loop per brand untuk mengisi nilai penjualan --}}
                            @foreach ($allBrands as $brand)
                                @php
                                    $sale = $brandsData[$brand] ?? 0;
                                    $yearTotal += $sale;
                                    $grandTotalByBrand[$brand] += $sale;
                                @endphp
                                <td style="border:1px solid #000; padding:6px; text-align:right;">
                                    {{ $sale > 0 ? number_format($sale, 0, ',', '.') : '-' }}</td>
                            @endforeach
                            <td style="border:1px solid #000; padding:6px; text-align:right; font-weight:bold;">
                                {{ number_format($yearTotal, 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $salesTotal += $yearTotal;
                            $firstYear = false;
                        @endphp
                    @endforeach
                    <!-- Baris Subtotal per Sales -->
                    <tr>
                        <td colspan="2"
                            style="background-color:#FBE2D5; border:1px solid #000; padding:6px; text-align:center; font-weight:bold">
                            {{ $salesName }} Total</td>
                        @foreach ($allBrands as $brand)
                            @php
                                $totalBrandForSales = 0;
                                foreach ($years as $yearData) {
                                    $totalBrandForSales += $yearData[$brand] ?? 0;
                                }
                            @endphp
                            <td
                                style="background-color:#FBE2D5; border:1px solid #000; padding:6px; text-align:right; font-weight:bold">
                                {{ $totalBrandForSales > 0 ? number_format($totalBrandForSales, 0, ',', '.') : '-' }}
                            </td>
                        @endforeach
                        <td
                            style="background-color:#FBE2D5; border:1px solid #000; padding:6px; text-align:right; font-weight:bold">
                            {{ number_format($salesTotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <!-- Baris Grand Total -->
                <tr>
                    <td colspan="2"
                        style="background-color:#C0E6F5; font-weight:bold; border:1px solid #000; padding:6px; text-align:center;">
                        Grand Total</td>
                    @foreach ($allBrands as $brand)
                        @php $finalGrandTotal += $grandTotalByBrand[$brand]; @endphp
                        <td
                            style="background-color:#C0E6F5; font-weight:bold; border:1px solid #000; padding:6px; text-align:right;">
                            {{ number_format($grandTotalByBrand[$brand], 0, ',', '.') }}</td>
                    @endforeach
                    <td
                        style="background-color:#C0E6F5; font-weight:bold; border:1px solid #000; padding:6px; text-align:right;">
                        {{ number_format($finalGrandTotal, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>

</html>
