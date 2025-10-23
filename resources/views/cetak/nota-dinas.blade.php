<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Dinas - {{ $notaDinas->no_nota }}</title>
    <style>
        @page { 
            size: A4; 
            margin: 2cm 2.5cm;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
            .detail-section {
                background: white !important;
                border: 1px solid #000 !important;
            }
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
            max-width: 21cm;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .container {
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        @media print {
            .container {
                box-shadow: none;
                padding: 0;
            }
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px double #000;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14pt;
            font-weight: bold;
        }
        
        .header .subtitle {
            font-size: 9pt;
            margin-top: 8px;
            line-height: 1.4;
            color: #333;
        }
        
        .title {
            text-align: center;
            margin: 25px 0 20px 0;
        }
        
        .title h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .meta-info {
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #000;
            padding: 15px;
        }
        
        .meta-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .meta-info td {
            padding: 4px 0;
            vertical-align: top;
        }
        
        .meta-info .label {
            width: 130px;
            font-weight: bold;
        }
        
        .meta-info .separator {
            width: 15px;
            text-align: center;
        }
        
        .meta-info .value {
            font-weight: normal;
        }
        
        .content {
            margin: 20px 0;
            text-align: justify;
            line-height: 1.8;
        }
        
        .content p {
            margin: 12px 0;
            text-indent: 0;
        }
        
        .content .greeting {
            margin-bottom: 15px;
        }
        
        .detail-section {
            margin: 15px 0;
            padding: 12px;
            border: 1px solid #000;
        }
        
        .detail-section h4 {
            margin: 0 0 8px 0;
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .detail-section .text {
            white-space: pre-line;
            line-height: 1.7;
            text-align: justify;
        }
        
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-row {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        
        .signature-box .title {
            margin-bottom: 70px;
            font-weight: normal;
            font-size: 10pt;
            line-height: 1.4;
        }
        
        .signature-box .name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-bottom: 2px;
        }
        
        .signature-box .position {
            font-weight: normal;
            margin-top: 3px;
            font-size: 10pt;
        }
        
        .footer-info {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            color: #666;
            page-break-inside: avoid;
        }
        
        @media print {
            .footer-info {
                border-top: 1px solid #ccc;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #2563eb;
        }
        
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 12px 24px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        
        .back-button:hover {
            background: #4b5563;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100pt;
            color: rgba(0, 0, 0, 0.05);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 9pt;
            font-weight: bold;
            margin-left: 8px;
            border: 1px solid #000;
        }
        
        @media print {
            .badge-segera {
                background: #ddd;
                color: #000;
                border: 1px solid #000;
            }
            
            .badge-sangat-segera {
                background: #999;
                color: #000;
                border: 2px solid #000;
            }
            
            .badge-rahasia {
                background: #666;
                color: #fff;
                border: 2px solid #000;
            }
            
            .badge-biasa {
                background: #fff;
                color: #000;
                border: 1px solid #000;
            }
        }
        
        @media screen {
            .badge-segera {
                background: #fee2e2;
                color: #991b1b;
                border: 1px solid #fca5a5;
            }
            
            .badge-sangat-segera {
                background: #dc2626;
                color: white;
            }
            
            .badge-rahasia {
                background: #374151;
                color: white;
            }
            
            .badge-biasa {
                background: #e5e7eb;
                color: #374151;
            }
        }
    </style>
</head>
<body>
    <!-- Tombol Aksi -->
    <a href="{{ route('permintaan.show', $permintaan->permintaan_id) }}" class="back-button no-print">
        ← Kembali
    </a>
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Cetak
    </button>

    <!-- Container Utama -->
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PEMERINTAH KABUPATEN GRESIK</h1>
            <h1>RUMAH SAKIT UMUM DAERAH IBNU SINA</h1>
            <div class="subtitle">
                Jl. Dr. Wahidin Sudirohusodo No. 243A, Kebomas, Gresik 61124<br>
                Telp: (031) 3952439 | Fax: (031) 3952983 | Email: rsud.ibnusina@gresikkab.go.id<br>
                Website: www.rsud-ibnusina.gresikkab.go.id
            </div>
        </div>

        <!-- Judul -->
        <div class="title">
            <h3>Nota Dinas</h3>
        </div>

        <!-- Meta Informasi -->
        <div class="meta-info">
            <table>
                <tr>
                    <td class="label">Kepada</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $notaDinas->kepada }}</td>
                </tr>
                <tr>
                    <td class="label">Dari</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $notaDinas->dari }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="separator">:</td>
                    <td class="value">{{ \Carbon\Carbon::parse($notaDinas->tanggal_nota)->isoFormat('D MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <td class="label">Nomor</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $notaDinas->no_nota }}</td>
                </tr>
                @if($notaDinas->sifat)
                <tr>
                    <td class="label">Sifat</td>
                    <td class="separator">:</td>
                    <td class="value">
                        {{ $notaDinas->sifat }}
                        <span class="badge badge-{{ strtolower(str_replace(' ', '-', $notaDinas->sifat)) }}">
                            {{ strtoupper($notaDinas->sifat) }}
                        </span>
                    </td>
                </tr>
                @endif
                @if($notaDinas->lampiran)
                <tr>
                    <td class="label">Lampiran</td>
                    <td class="separator">:</td>
                    <td class="value">1 (satu) berkas</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Perihal</td>
                    <td class="separator">:</td>
                    <td class="value"><strong>{{ $notaDinas->perihal }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Konten/Isi -->
        <div class="content">
            <p class="greeting">Dengan hormat,</p>
            
            @if($notaDinas->detail)
            <p>{{ $notaDinas->detail }}</p>
            @else
            <p>
                Bersama ini kami sampaikan permohonan terkait <strong>{{ $notaDinas->perihal }}</strong>.
                Sesuai dengan permintaan dari <strong>{{ $permintaan->bidang ?? 'Unit terkait' }}</strong> 
                tertanggal {{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->isoFormat('D MMMM YYYY') }}, 
                kami memohon perhatian dan tindak lanjut dari Bapak/Ibu.
            </p>
            @endif

            @if($permintaan->deskripsi)
            <div class="detail-section">
                <h4>Deskripsi Permintaan</h4>
                <div class="text">{{ $permintaan->deskripsi }}</div>
            </div>
            @endif

            @if($permintaan->disposisi_tujuan)
            <div class="detail-section">
                <h4>Informasi Disposisi</h4>
                <div class="text">
                    <strong>Tujuan Disposisi:</strong> {{ $permintaan->disposisi_tujuan }}
                    @if($permintaan->wadir_tujuan)
                    <br><strong>Wakil Direktur:</strong> {{ $permintaan->wadir_tujuan }}
                    @endif
                    @if($permintaan->catatan_disposisi)
                    <br><strong>Catatan:</strong> {{ $permintaan->catatan_disposisi }}
                    @endif
                </div>
            </div>
            @endif

            <p>
                Demikian nota dinas ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
            </p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-row">
                @if($notaDinas->mengetahui)
                <div class="signature-box">
                    <div class="title">
                        Mengetahui,<br>
                        Kepala Instalasi
                    </div>
                    <div class="name">{{ $notaDinas->mengetahui }}</div>
                    <div class="position">NIP. ___________________</div>
                </div>
                @else
                <div class="signature-box">
                    <!-- Empty space -->
                </div>
                @endif
                
                <div class="signature-box">
                    <div class="title">
                        {{ \Carbon\Carbon::parse($notaDinas->tanggal_nota)->isoFormat('D MMMM YYYY') }}<br>
                        {{ $notaDinas->dari }}
                    </div>
                    <div class="name">{{ $permintaan->pic_pimpinan ?? '___________________' }}</div>
                    <div class="position">NIP. ___________________</div>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="footer-info no-print">
            <strong>Informasi Dokumen:</strong><br>
            ID Permintaan: #{{ $permintaan->permintaan_id }} | 
            Dicetak: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB
        </div>
    </div>

    <script>
        // Auto focus untuk print
        window.onload = function() {
            // Optional: Auto print saat halaman dibuka
            // window.print();
        }

        // Keyboard shortcut
        document.addEventListener('keydown', function(e) {
            // Ctrl+P untuk print
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            // ESC untuk kembali
            if (e.key === 'Escape') {
                window.location.href = '{{ route("permintaan.show", $permintaan->permintaan_id) }}';
            }
        });
    </script>
</body>
</html>
