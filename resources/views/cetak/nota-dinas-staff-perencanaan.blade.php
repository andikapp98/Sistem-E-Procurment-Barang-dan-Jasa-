<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Dinas - {{ $notaDinas->nomor }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            padding: 2cm 2.5cm;
            color: #000;
        }
        
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .kop-surat h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .kop-surat p {
            font-size: 11pt;
            margin: 2px 0;
        }
        
        .judul {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        
        .judul h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .nomor {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 30px;
        }
        
        .isi {
            margin: 20px 0;
        }
        
        .isi table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .isi table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .isi table td:first-child {
            width: 150px;
        }
        
        .isi table td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .anggaran-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .anggaran-table th,
        .anggaran-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .anggaran-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .anggaran-table .right {
            text-align: right;
        }
        
        .ttd {
            margin-top: 50px;
            text-align: right;
        }
        
        .ttd-content {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        
        .ttd-content p {
            margin: 5px 0;
        }
        
        .ttd-space {
            height: 80px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            @page {
                size: A4;
                margin: 2cm 2.5cm;
            }
        }
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <div class="kop-surat">
        <h1>RSUD KOTA YOGYAKARTA</h1>
        <p>Jl. Wirosaban No. 1, Yogyakarta 55162</p>
        <p>Telp: (0274) 512001 | Fax: (0274) 512002</p>
        <p>Email: rsud@jogjakota.go.id</p>
    </div>
    
    <!-- Judul -->
    <div class="judul">
        <h2>NOTA DINAS</h2>
    </div>
    
    <!-- Nomor -->
    <div class="nomor">
        Nomor: {{ $notaDinas->nomor ?? '-' }}
    </div>
    
    <!-- Isi -->
    <div class="isi">
        <table>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($notaDinas->tanggal_nota)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Dari</td>
                <td>:</td>
                <td>{{ $notaDinas->dari }}</td>
            </tr>
            <tr>
                <td>Kepada</td>
                <td>:</td>
                <td>{{ $notaDinas->kepada }}</td>
            </tr>
            <tr>
                <td>Sifat</td>
                <td>:</td>
                <td>{{ $notaDinas->sifat ?? '-' }}</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td><strong>{{ $notaDinas->perihal }}</strong></td>
            </tr>
        </table>
        
        <div style="margin: 30px 0 20px 0;">
            <p style="text-align: justify;">
                {!! nl2br(e($notaDinas->uraian ?? $permintaan->deskripsi)) !!}
            </p>
        </div>
        
        @if($notaDinas->pagu_anggaran)
        <h3 style="margin: 30px 0 15px 0;">Rincian Anggaran</h3>
        <table class="anggaran-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @if($notaDinas->kode_program)
                <tr>
                    <td>Kode Program</td>
                    <td>{{ $notaDinas->kode_program }}</td>
                </tr>
                @endif
                @if($notaDinas->kode_kegiatan)
                <tr>
                    <td>Kode Kegiatan</td>
                    <td>{{ $notaDinas->kode_kegiatan }}</td>
                </tr>
                @endif
                @if($notaDinas->kode_rekening)
                <tr>
                    <td>Kode Rekening</td>
                    <td>{{ $notaDinas->kode_rekening }}</td>
                </tr>
                @endif
                <tr>
                    <td><strong>Pagu Anggaran</strong></td>
                    <td class="right"><strong>{{ number_format($notaDinas->pagu_anggaran, 0, ',', '.') }}</strong></td>
                </tr>
                @if($notaDinas->ppn > 0)
                <tr>
                    <td>PPN</td>
                    <td class="right">{{ number_format($notaDinas->ppn, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($notaDinas->pph > 0)
                <tr>
                    <td>PPh</td>
                    <td class="right">{{ number_format($notaDinas->pph, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($notaDinas->pph_21 > 0)
                <tr>
                    <td>PPh 21</td>
                    <td class="right">{{ number_format($notaDinas->pph_21, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($notaDinas->pph_4_2 > 0)
                <tr>
                    <td>PPh 4(2)</td>
                    <td class="right">{{ number_format($notaDinas->pph_4_2, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($notaDinas->pph_22 > 0)
                <tr>
                    <td>PPh 22</td>
                    <td class="right">{{ number_format($notaDinas->pph_22, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif
        
        @if($notaDinas->unit_instalasi)
        <div style="margin: 20px 0;">
            <p><strong>Unit/Instalasi:</strong> {{ $notaDinas->unit_instalasi }}</p>
        </div>
        @endif
    </div>
    
    <!-- Tanda Tangan -->
    <div class="ttd">
        <div class="ttd-content">
            <p>Yogyakarta, {{ \Carbon\Carbon::parse($notaDinas->tanggal_nota)->translatedFormat('d F Y') }}</p>
            <p><strong>{{ $notaDinas->dari }}</strong></p>
            <div class="ttd-space"></div>
            <p>_____________________</p>
            <p><strong>Staff Perencanaan</strong></p>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
