<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f0f4f1;
            margin: 0;
            padding: 20px;
            color: #1e2d24;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,.1);
        }
        .body {
            padding: 32px;
        }
        .body p {
            font-size: 14px;
            line-height: 1.7;
            margin: 0 0 14px;
            color: #3a4a3f;
        }
        .username-box {
            background: #eaf7e6;
            border: 1px solid #c8e6c3;
            border-radius: 5px;
            padding: 8px 16px;
            font-size: 15px;
            font-weight: 600;
            color: #1a5c32;
            margin: 0 0 20px;
            display: inline-block;
        }
        .btn {
            display: block;
            background-color: #2d7a4f;
            color: #ffffff !important;
            text-decoration: none;
            text-align: center;
            padding: 13px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            margin: 20px 0;
        }
        .note {
            background: #fff8e1;
            border-left: 4px solid #f39c12;
            border-radius: 0 5px 5px 0;
            padding: 10px 14px;
            font-size: 13px;
            color: #7a5c00;
            margin: 14px 0;
        }
        .url-box {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 12px;
            color: #555;
            word-break: break-all;
            margin: 8px 0 14px;
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="body">
            <p>Halo, <strong>{{ $username }}</strong></p>
            <p>Kami menerima permintaan reset kata sandi untuk akun berikut:</p>

            <div class="username-box">{{ $username }}</div>

            <p>Klik tombol di bawah untuk membuat kata sandi baru:</p>

            <a href="{{ $resetUrl }}" class="btn">Reset Kata Sandi Sekarang</a>

            <div class="note">
                ⏱ Link ini hanya berlaku selama <strong>15 menit</strong> sejak email ini dikirim.
            </div>

            <p>Jika tombol tidak berfungsi, salin dan tempel link berikut di browser Anda:</p>
            <div class="url-box">{{ $resetUrl }}</div>

            <strong>Jika Anda tidak merasa meminta reset kata sandi, abaikan email ini. Kata sandi Anda tidak akan berubah.</strong>
        </div>

    </div>
</body>
</html>