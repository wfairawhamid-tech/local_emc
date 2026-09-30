<?php // errors/ipesakit_only.php — Halaman akses ditolak (bukan melalui iPesakit) ?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Akses Ditolak — eMC System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .card {
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 20px;
      padding: 52px 48px;
      max-width: 520px;
      width: 100%;
      text-align: center;
      box-shadow: 0 25px 60px rgba(0,0,0,0.4);
      animation: fadeUp 0.5s ease;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .icon-wrap {
      width: 88px;
      height: 88px;
      background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(239,68,68,0.05));
      border: 2px solid rgba(239,68,68,0.3);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      margin: 0 auto 28px;
      animation: pulse 2.5s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.2); }
      50%       { box-shadow: 0 0 0 12px rgba(239,68,68,0); }
    }

    h1 {
      font-size: 22px;
      font-weight: 700;
      color: #f1f5f9;
      margin-bottom: 12px;
      line-height: 1.3;
    }

    .subtitle {
      font-size: 14px;
      color: #94a3b8;
      line-height: 1.7;
      margin-bottom: 32px;
    }

    .info-box {
      background: rgba(59,130,246,0.1);
      border: 1px solid rgba(59,130,246,0.25);
      border-radius: 12px;
      padding: 18px 20px;
      margin-bottom: 32px;
      text-align: left;
    }

    .info-box p {
      font-size: 13px;
      color: #93c5fd;
      line-height: 1.7;
    }

    .info-box strong {
      color: #bfdbfe;
    }

    .step-list {
      list-style: none;
      margin-top: 10px;
    }

    .step-list li {
      font-size: 13px;
      color: #93c5fd;
      padding: 4px 0;
      display: flex;
      align-items: flex-start;
      gap: 8px;
    }

    .step-list li .num {
      background: rgba(59,130,246,0.3);
      color: #93c5fd;
      font-size: 11px;
      font-weight: 700;
      min-width: 20px;
      height: 20px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 1px;
    }

    .divider {
      border: none;
      border-top: 1px solid rgba(255,255,255,0.08);
      margin: 28px 0;
    }

    .badge-source {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(234,179,8,0.12);
      border: 1px solid rgba(234,179,8,0.25);
      color: #fde68a;
      font-size: 11.5px;
      font-weight: 600;
      padding: 5px 12px;
      border-radius: 20px;
      margin-bottom: 28px;
    }

    footer {
      margin-top: 32px;
      font-size: 11.5px;
      color: #475569;
    }
  </style>
</head>
<body>

  <div class="card">
    <div class="icon-wrap">🔒</div>

    <div class="badge-source">⚠️ Akses Ditolak</div>

    <h1>Sistem Ini Hanya Boleh Diakses Melalui iPesakit</h1>

    <p class="subtitle">
      Sistem eMC &amp; Time Slip adalah aplikasi bersepadu yang direka untuk dilancarkan terus dari sistem <strong>iPesakit</strong>. Akses secara terus tidak dibenarkan.
    </p>

    <div class="info-box">
      <p><strong>Cara menggunakan sistem ini:</strong></p>
      <ul class="step-list">
        <li>
          <span class="num">1</span>
          <span>Log masuk ke sistem <strong>iPesakit</strong> menggunakan akaun doktor anda.</span>
        </li>
        <li>
          <span class="num">2</span>
          <span>Pilih pesakit yang ingin dirawat dari senarai pesakit dalam iPesakit.</span>
        </li>
        <li>
          <span class="num">3</span>
          <span>Klik butang <strong>"Keluarkan MC"</strong> atau <strong>"Keluarkan Time Slip"</strong> dalam iPesakit.</span>
        </li>
        <li>
          <span class="num">4</span>
          <span>Sistem eMC akan dilancarkan secara automatik dengan maklumat pesakit yang dipilih.</span>
        </li>
      </ul>
    </div>

    <hr class="divider">

    <footer>
      eMC v1.0 &copy; <?= date('Y') ?> &mdash; Electronic Medical Certificate &amp; Time Slip System
    </footer>
  </div>

</body>
</html>
