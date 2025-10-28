<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DPP - {{ $dpp->nama_paket }}</title>
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
            margin-bottom: 10px;
        }
        
        .header h2 {
            font-size: 14pt;
            margin-bottom: 5px;
        }
        
        .section {
            margin: 25px 0;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px 10px;
            margin-bottom: 15px;
            border-left: 4px solid #028174;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 6px 8px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 220px;
            font-weight: 600;
        }
        
        .info-table td:nth-child(2) {
            width: 20px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #028174;
            color: white;
            font-weight: bold;
        }
        
        .right {
            text-align: right;
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
                size: A4;
                margin: 2cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DOKUMEN PERSIAPAN PENGADAAN (DPP)</h1>
        <h2>RSUD KOTA YOGYAKARTA</h2>
        <p>Tahun Anggaran {{ date('Y') }}</p>
    </div>
    
    <!-- Identifikasi Paket -->
    <div class="section">
        <div class="section-title">I. IDENTIFIKASI PAKET</div>
        <table class="info-table">
            <tr>
                <td>Nama Paket</td>
                <td>:</td>
                <td>{{ $dpp->nama_paket }}</td>
            </tr>
            <tr>
                <td>PPK yang Ditunjuk</td>
                <td>:</td>
                <td>{{ $dpp->ppk_ditunjuk }}</td>
            </tr>
            <tr>
                <td>Lokasi</td>
                <td>:</td>
                <td>{{ $dpp->lokasi }}</td>
            </tr>
            <tr>
                <td>Nama Kegiatan</td>
                <td>:</td>
                <td>{{ $dpp->nama_kegiatan }}</td>
            </tr>
            <tr>
                <td>Jenis Pengadaan</td>
                <td>:</td>
                <td>{{ $dpp->jenis_pengadaan }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Program dan Kegiatan -->
    <div class="section">
        <div class="section-title">II. PROGRAM DAN KEGIATAN</div>
        <table class="info-table">
            <tr>
                <td>Uraian Program</td>
                <td>:</td>
                <td>{{ $dpp->uraian_program }}</td>
            </tr>
            <tr>
                <td>Uraian Kegiatan</td>
                <td>:</td>
                <td>{{ $dpp->uraian_kegiatan }}</td>
            </tr>
            @if($dpp->sub_kegiatan)
            <tr>
                <td>Sub Kegiatan</td>
                <td>:</td>
                <td>{{ $dpp->sub_kegiatan }}</td>
            </tr>
            @endif
            @if($dpp->sub_sub_kegiatan)
            <tr>
                <td>Sub-Sub Kegiatan</td>
                <td>:</td>
                <td>{{ $dpp->sub_sub_kegiatan }}</td>
            </tr>
            @endif
            <tr>
                <td>Kode Rekening</td>
                <td>:</td>
                <td>{{ $dpp->kode_rekening }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Anggaran dan HPS -->
    <div class="section">
        <div class="section-title">III. ANGGARAN DAN HPS</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="right">Nilai (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sumber Dana</td>
                    <td>{{ $dpp->sumber_dana }}</td>
                </tr>
                <tr>
                    <td>Pagu Paket</td>
                    <td class="right">{{ number_format($dpp->pagu_paket, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Nilai HPS</strong></td>
                    <td class="right"><strong>{{ number_format($dpp->nilai_hps, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td colspan="2">Sumber Data/Survei HPS: {{ $dpp->sumber_data_survei_hps }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Kontrak dan Pelaksanaan -->
    <div class="section">
        <div class="section-title">IV. KONTRAK DAN PELAKSANAAN</div>
        <table class="info-table">
            <tr>
                <td>Jenis Kontrak</td>
                <td>:</td>
                <td>{{ $dpp->jenis_kontrak }}</td>
            </tr>
            <tr>
                <td>Kualifikasi</td>
                <td>:</td>
                <td>{{ $dpp->kualifikasi }}</td>
            </tr>
            <tr>
                <td>Jangka Waktu Pelaksanaan</td>
                <td>:</td>
                <td>{{ $dpp->jangka_waktu_pelaksanaan }} hari kalender</td>
            </tr>
        </table>
    </div>
    
    <!-- Referensi Permintaan -->
    <div class="section">
        <div class="section-title">V. REFERENSI</div>
        <table class="info-table">
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
            <tr>
                <td>Tanggal Permintaan</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->translatedFormat('d F Y') }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Tanda Tangan -->
    <div class="footer">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p><strong>Kepala Bagian</strong></p>
            <div class="signature-space"></div>
            <p>_____________________</p>
        </div>
        <div class="signature-box">
            <p>Yogyakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p><strong>PPK</strong></p>
            <div class="signature-space"></div>
            <p>_____________________</p>
            <p>{{ $dpp->ppk_ditunjuk }}</p>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
