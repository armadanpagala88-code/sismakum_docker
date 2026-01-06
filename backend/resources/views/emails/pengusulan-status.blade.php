<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Status Pengusulan PERBUB</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .status-diajukan { background-color: #dbeafe; color: #1e40af; }
        .status-diterima { background-color: #d1fae5; color: #065f46; }
        .status-ditolak { background-color: #fee2e2; color: #991b1b; }
        .status-revisi { background-color: #fef3c7; color: #92400e; }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SISMAKUM</h1>
        <p>Sistem Informasi Pengusulan PERBUB</p>
    </div>
    
    <div class="content">
        <h2>Notifikasi Status Pengusulan PERBUB</h2>
        
        <p>Yth. {{ $pengusulan->dinas->name }},</p>
        
        <p>Pengusulan PERBUB Anda telah mengalami perubahan status:</p>
        
        <div class="info-box">
            <strong>Judul PERBUB:</strong> {{ $pengusulan->judul_perbub }}<br>
            <strong>Nomor Surat:</strong> {{ $pengusulan->nomor_surat }}<br>
            <strong>Tanggal Surat:</strong> {{ $pengusulan->tanggal_surat->format('d F Y') }}<br>
            <strong>Status:</strong> 
            <span class="status-badge status-{{ $pengusulan->status }}">
                {{ $statusLabel }}
            </span>
        </div>
        
        @if($catatan)
        <div class="info-box">
            <strong>Catatan:</strong><br>
            <p style="white-space: pre-wrap;">{{ $catatan }}</p>
        </div>
        @endif
        
        @if($pengusulan->status === 'revisi')
        <p><strong>Silakan lengkapi usulan Anda sesuai dengan catatan yang diberikan.</strong></p>
        @endif
        
        <p>
            <a href="{{ env('APP_URL', 'http://localhost:5173') }}/pengusulan/{{ $pengusulan->id }}" class="button">
                Lihat Detail Pengusulan
            </a>
        </p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis oleh sistem SISMAKUM.</p>
        <p>Jangan membalas email ini.</p>
    </div>
</body>
</html>

