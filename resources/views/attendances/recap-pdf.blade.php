<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi {{ $periodLabel }}</title>
    <style>
        @page { margin: 16mm 14mm; }

        body {
            font-family: "Times New Roman", times, serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
        }

        h1 {
            font-size: 15px;
            text-align: center;
            margin: 0 0 2px;
            letter-spacing: 0.5px;
        }

        .subtitle {
            text-align: center;
            font-size: 11px;
            color: #4b5563;
            margin: 0 0 2px;
        }

        .meta {
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            margin: 0 0 12px;
        }

        .office {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin: 12px 0 4px;
            page-break-after: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #374151;
            padding: 5px 6px;
            vertical-align: middle;
        }

        thead th {
            background-color: #7ac143;
            color: #1f2937;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }

        thead th.name {
            text-align: left;
        }

        td.center { text-align: center; }
        td.name { font-weight: bold; }
        td.note { font-size: 9px; }

        .footer {
            margin-top: 10px;
            font-size: 8.5px;
            color: #6b7280;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>REKAP ABSENSI BULANAN</h1>
    <p class="subtitle">{{ $periodLabel }}{{ $cutoffLabel ? ' ('.$cutoffLabel.')' : '' }}</p>

    @if ($locationId)
        <p class="meta">{{ $officeLabel }}</p>
    @endif

    @foreach ($groups as $group)
        <p class="office">{{ $group['label'] }}</p>

        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 7%;">No.</th>
                    <th rowspan="2" class="name" style="width: 28%;">Nama Karyawan</th>
                    <th colspan="2">Masuk</th>
                    <th rowspan="2" style="width: 12%;">Tidak Masuk</th>
                    <th rowspan="2" class="name" style="width: 35%;">Keterangan</th>
                </tr>
                <tr>
                    <th style="width: 9%;">Tepat Waktu</th>
                    <th style="width: 9%;">Terlambat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($group['rows'] as $row)
                    <tr>
                        <td class="center">{{ $row['no'] }}</td>
                        <td class="name">{{ $row['name'] }}</td>
                        <td class="center">{{ $row['present'] }}</td>
                        <td class="center">{{ $row['late'] }}</td>
                        <td class="center">{{ $row['absent'] }}</td>
                        <td class="note">{{ $row['note'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    @if ($groups === [])
        <p class="meta">Belum ada karyawan aktif.</p>
    @endif

    <div class="footer">Dicetak {{ $generatedAt }} WIB · BMT Cinde Barokah</div>
</body>
</html>
