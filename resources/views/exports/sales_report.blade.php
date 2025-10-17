<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    <table style="border-collapse: collapse; width: 100%;">
        @php
            // Menghitung total kolom untuk colspan
            // 2 (Brand, Year) + jumlah bulan + 1 (Grand Total)
            $totalColumns = 3 + count($months);
        @endphp

        <!-- JUDUL LAPORAN -->
        <tr>
            <td colspan="{{ $totalColumns }}" rowspan="2"
                style="text-align: center; font-weight: bold; font-size: 12px; vertical-align:middle; border:1px solid #000;">
                {{ $description }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ $totalColumns }}"></td>
        </tr>

        <!-- INFORMASI SALES & PERIODE -->
        <tr>
            <td style="font-weight: bold; border:1px solid #000;">NAMA SALES</td>
            <td colspan="{{ $totalColumns - 1 }}" style="border:1px solid #000;">: {{ $salespersonName }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; border:1px solid #000;">PERIODE</td>
            @php
                $start = \Carbon\Carbon::parse($startDate);
                $end = \Carbon\Carbon::parse($endDate);

                if ($start->year == $end->year) {
                    if ($start->month == $end->month) {
                        // Bulan & tahun sama
                        $dateRange = $start->format('d') . ' - ' . $end->translatedFormat('d F Y');
                    } else {
                        // Bulan beda tapi tahun sama
                        $dateRange = $start->translatedFormat('d F') . ' - ' . $end->translatedFormat('d F Y');
                    }
                } else {
                    // Tahun berbeda
                    $dateRange = $start->translatedFormat('d F Y') . ' - ' . $end->translatedFormat('d F Y');
                }
            @endphp

            <td colspan="{{ $totalColumns - 1 }}" style="border:1px solid #000;">
                : {{ $dateRange }}
            </td>
        </tr>

        <thead>
            <!-- Baris Header Pertama -->
            <tr>
                <th rowspan="2"
                    style="background-color:#C0E6F5; font-weight:bold; text-align:center; vertical-align:middle; border:1px solid #000; padding:6px;">
                    BRAND</th>
                <th rowspan="2"
                    style="background-color:#C0E6F5; font-weight:bold; text-align:center; vertical-align:middle; border:1px solid #000; padding:6px;">
                    YEAR</th>
                <th colspan="{{ count($months) }}"
                    style="background-color:#C0E6F5; font-weight:bold; text-align:center; vertical-align:middle; border:1px solid #000; padding:6px;">
                    MONTH</th>
                <th rowspan="2"
                    style="background-color:#C0E6F5; font-weight:bold; text-align:center; vertical-align:middle; border:1px solid #000; padding:6px;">
                    Grand Total</th>
            </tr>

            <!-- Baris Header Kedua (hanya untuk nomor bulan) -->
            <tr>
                @foreach ($months as $month)
                    <th
                        style="background-color:#C0E6F5; font-weight:bold; text-align:center; border:1px solid #000; padding:6px;">
                        {{ $month }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @php
                $grandTotalByMonth = array_fill_keys($months, 0);
            @endphp

            @foreach ($pivotData as $brand => $years)
                @foreach ($years as $year => $monthlySales)
                    <tr>
                        <td style="border:1px solid #000; padding:6px; text-align:left; font-weight:bold">
                            {{ $brand }}</td>
                        <td style="border:1px solid #000; padding:6px; text-align:left;">{{ $year }}</td>

                        @php $yearTotal = 0; @endphp

                        @foreach ($months as $month)
                            @php
                                $sale = $monthlySales[$month] ?? 0;
                                $yearTotal += $sale;
                                $grandTotalByMonth[$month] += $sale;
                            @endphp
                            <td style="border:1px solid #000; padding:6px; text-align:right;">
                                {{ number_format($sale, 0, ',', '.') }}</td>
                        @endforeach

                        <td style="border:1px solid #000; padding:6px; text-align:right;">
                            {{ number_format($yearTotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <!-- Baris Total per Brand -->
                <tr style="background-color:#e7e6e6; font-weight:bold;">
                    <td colspan="2"
                        style="background-color:#FBE2D5; order:1px solid #000; padding:6px; text-align:center; font-weight:bold">
                        {{ $brand }}
                        Total</td>
                    @php $grandBrandTotal = 0; @endphp
                    @foreach ($months as $month)
                        @php
                            $totalForMonthByBrand = 0;
                            foreach ($years as $yearData) {
                                $totalForMonthByBrand += $yearData[$month] ?? 0;
                            }
                            $grandBrandTotal += $totalForMonthByBrand;
                        @endphp
                        <td
                            style="background-color:#FBE2D5; border:1px solid #000; padding:6px; text-align:right; font-weight:bold">
                            {{ number_format($totalForMonthByBrand, 0, ',', '.') }}</td>
                    @endforeach
                    <td
                        style="background-color:#FBE2D5; border:1px solid #000; padding:6px; text-align:right; font-weight:bold">
                        {{ number_format($grandBrandTotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <!-- Baris Grand Total di paling bawah -->
            <tr style="background-color:#C0E6F5; font-weight:bold;">
                <td colspan="2"
                    style="background-color:#C0E6F5; font-weight:bold; border:1px solid #000; padding:6px; text-align:center;">
                    Grand Total</td>
                @php $finalGrandTotal = 0; @endphp
                @foreach ($grandTotalByMonth as $monthlyTotal)
                    @php $finalGrandTotal += $monthlyTotal; @endphp
                    <td
                        style="background-color:#C0E6F5; font-weight:bold; border:1px solid #000; padding:6px; text-align:right;">
                        {{ number_format($monthlyTotal, 0, ',', '.') }}</td>
                @endforeach
                <td
                    style="background-color:#C0E6F5; font-weight:bold; border:1px solid #000; padding:6px; text-align:right;">
                    {{ number_format($finalGrandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
