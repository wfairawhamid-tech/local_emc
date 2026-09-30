<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Log Masuk') ?> — Sistem eMC HoSZA</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <style>
    body {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f2e59 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      margin: 0;
    }

    .login-container {
      width: 100%;
      max-width: 520px;
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
      overflow: hidden;
    }

    .login-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
      color: #ffffff;
      padding: 32px 28px;
      text-align: center;
      position: relative;
    }

    .login-logo {
      height: 60px;
      margin-bottom: 12px;
      object-fit: contain;
      background: #ffffff;
      padding: 6px 16px;
      border-radius: 8px;
    }

    .login-header h1 {
      font-size: 22px;
      font-weight: 700;
      margin: 0 0 6px 0;
      letter-spacing: -0.3px;
    }

    .login-header p {
      font-size: 13px;
      color: #bfdbfe;
      margin: 0;
      line-height: 1.4;
    }

    .login-body {
      padding: 28px;
    }

    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 13.5px;
      line-height: 1.4;
    }
    .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-info { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }

    .quick-section-title {
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #64748b;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .quick-section-title::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e2e8f0;
    }

    .role-grid {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 24px;
    }

    .role-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 16px;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      text-decoration: none;
      color: #1e293b;
      background: #f8fafc;
      transition: all 0.2s ease;
    }
    .role-card:hover {
      border-color: #2563eb;
      background: #eff6ff;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
    }

    .role-card.admin-card {
      background: #faf5ff;
      border-color: #e9d5ff;
    }
    .role-card.admin-card:hover {
      border-color: #9333ea;
      background: #f3e8ff;
      box-shadow: 0 4px 12px rgba(147, 51, 234, 0.12);
    }

    .role-card-left {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .role-avatar {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      background: #dbeafe;
      color: #1d4ed8;
      flex-shrink: 0;
    }
    .admin-card .role-avatar {
      background: #f3e8ff;
      color: #7e22ce;
    }

    .role-info h4 {
      margin: 0 0 3px 0;
      font-size: 14.5px;
      font-weight: 600;
      color: #0f172a;
    }
    .role-info p {
      margin: 0;
      font-size: 12.5px;
      color: #64748b;
    }

    .role-btn-badge {
      font-size: 12px;
      font-weight: 600;
      padding: 6px 12px;
      border-radius: 6px;
      background: #2563eb;
      color: #ffffff;
      white-space: nowrap;
    }
    .admin-card .role-btn-badge {
      background: #7e22ce;
    }

    .login-footer {
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      padding: 16px 28px;
      text-align: center;
      font-size: 12px;
      color: #64748b;
    }
  </style>
</head>
<body>

<div class="login-container">
  <div class="login-header">
    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo HoSZA" class="login-logo">
    <h1>eMC &amp; Time Slip System</h1>
    <p>Hospital Sultan Zainal Abidin (HoSZA)<br>Access &amp; Quick Login Portal</p>
  </div>

  <div class="login-body">
    <?php if (session()->has('error')): ?>
      <div class="alert alert-danger">⚠️ <?= session('error') ?></div>
    <?php endif; ?>
    <?php if (session()->has('success')): ?>
      <div class="alert alert-success">✅ <?= session('success') ?></div>
    <?php endif; ?>
    <?php if (session()->has('info')): ?>
      <div class="alert alert-info">ℹ️ <?= session('info') ?></div>
    <?php endif; ?>

    <!-- 1-Click Login: IT Department Administrator -->
    <div class="quick-section-title">System Administrator Access (IT Department)</div>
    <div class="role-grid">
      <a href="<?= base_url('auth/dummy/admin') ?>" class="role-card admin-card">
        <div class="role-card-left">
          <div class="role-avatar">🛠️</div>
          <div class="role-info">
            <h4>IT Administrator</h4>
            <p>Doctor management, API diagnostics &amp; audit logs</p>
          </div>
        </div>
        <span class="role-btn-badge">Log In →</span>
      </a>
    </div>

    <!-- 1-Click Login: Medical Officers (Doctors) -->
    <div class="quick-section-title">Medical Officer Access (Doctors)</div>
    <div class="role-grid">
      <?php if (!empty($doctors)): ?>
        <?php foreach ($doctors as $doc): ?>
          <a href="<?= base_url('auth/dummy/doctor/' . $doc['id']) ?>" class="role-card">
            <div class="role-card-left">
              <div class="role-avatar">👨‍⚕️</div>
              <div class="role-info">
                <h4><?= esc($doc['name']) ?></h4>
                <p><?= esc($doc['mcr_number'] ?? 'MMC') ?> &bull; <?= esc($doc['department'] ?? 'Clinical') ?></p>
              </div>
            </div>
            <span class="role-btn-badge">Log In as Doctor →</span>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="font-size:13px; color:#64748b; text-align:center;">No active doctor records found.</p>
      <?php endif; ?>
    </div>

    <div style="background: #f1f5f9; border-radius: 8px; padding: 12px 14px; font-size: 12px; color: #475569; line-height: 1.5;">
      💡 <strong>iPesakit Integration Note:</strong> When launched from within the <em>iPesakit</em> system via the link <code>/launch?doctor_id=...&amp;rn=...</code>, the system will automatically authenticate the doctor and retrieve patient details without requiring manual login.
    </div>
  </div>

  <div class="login-footer">
    Hospital Sultan Zainal Abidin (HoSZA) &copy; <?= date('Y') ?> &bull; All Rights Reserved
  </div>
</div>

</body>
</html>
