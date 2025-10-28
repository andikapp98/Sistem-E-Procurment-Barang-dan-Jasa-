<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HPS - Permintaan #{{ $permintaan->permintaan_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            padding: 2cm;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14pt;
            margin-bottom: 10px;
        }
        
        .info-section {
            margin: 20px 0;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 5px 8px;
        }
        
        .info-table td:first-child {
            width: 180px;
            font-weight: 600;
        }
        
        .info-table td:nth-child(2) {
            width: 20px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #028174;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table td.center {
            text-align: center;
        }
        
        .items-table td.right {
            text-align: right;
        }
        
        .items-table tfoot {
            font-weight: bold;
        }
        
        .items-table tfoot td {
            background-color: #f0f0f0;
        }
        
        .grand-total {
            font-size: 13pt;
            background-color: #028174 !important;
            color: white !important;
        }
        
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .signature-space {
            height: 80px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            @page {
                size: A4 landscape;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HARGA PERKIRAAN SATUAN (HPS)</h1>
        <h2>RSUD KOTA YOGYAKARTA</h2>
        <p>Tahun Anggaran {{ date('Y') }}</p>
    </div>
    
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>PPK</td>
                <td>:</td>
                <td>{{ $hps->ppk }}</td>
            </tr>
            <tr>
                <td>Surat Penawaran Harga</td>
                <td>:</td>
                <td>{{ $hps->surat_penawaran_harga }}</td>
            </tr>
            <tr>
                <td>ID Permintaan</td>
                <td>:</td>
                <td>#{{ $permintaan->permintaan_id }}</td>
            </tr>
            <tr>
                <td>Unit Pemohon</td>
                <td>:</td>
                <td>{{ $permintaan->bidang }}</td>
            </tr>
        </table>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Item</th>
                <th style="width: 120px;">Type</th>
                <th style="width: 120px;">Merk</th>
                <th style="width: 80px;">Volume</th>
                <th style="width: 80px;">Satuan</th>
                <th style="width: 140px;">Harga Satuan (Rp)</th>
                <th style="width: 160px;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hps->items as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_item }}</td>
                <td class="center">{{ $item->type ?? '-' }}</td>
                <td>{{ $item->merk ?? '-' }}</td>
                <td class="center">{{ number_format($item->volume, 0, ',', '.') }}</td>
                <td class="center">{{ $item->satuan }}</td>
                <td class="right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="7" class="right">GRAND TOTAL:</td>
                <td class="right">{{ number_format($hps->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="8" style="padding: 10px; text-align: center; font-style: italic;">
                    Terbilang: {{ ucwords(terbilang($hps->grand_total)) }} Rupiah
                </td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p><strong>Kepala Bagian Perencanaan</strong></p>
            <div class="signature-space"></div>
            <p>_____________________</p>
        </div>
        <div class="signature-box">
            <p>Yogyakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p><strong>PPK</strong></p>
            <div class="signature-space"></div>
            <p>_____________________</p>
            <p>{{ $hps->ppk }}</p>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

@php
function terbilang($angka) {
    $angka = abs($angka);
    $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
    $temp = "";
    
    if ($angka < 12) {
        $temp = " " . $huruf[$angka];
    } else if ($angka < 20) {
        $temp = terbilang($angka - 10) . " Belas";
    } else if ($angka < 100) {
        $temp = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
    } else if ($angka < 200) {
        $temp = " Seratus" . terbilang($angka - 100);
    } else if ($angka < 1000) {
        $temp = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
    } else if ($angka < 2000) {
        $temp = " Seribu" . terbilang($angka - 1000);
    } else if ($angka < 1000000) {
        $temp = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
    } else if ($angka < 1000000000) {
        $temp = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
    } else if ($angka < 1000000000000) {
        $temp = terbilang($angka / 1000000000) . " Miliar" . terbilang(fmod($angka, 1000000000));
    } else if ($angka < 1000000000000000) {
        $temp = terbilang($angka / 1000000000000) . " Triliun" . terbilang(fmod($angka, 1000000000000));
    }
    
    return $temp;
}
@endphp
