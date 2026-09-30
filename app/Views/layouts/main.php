<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'eMC') ?> — Electronic Medical Certificate System</title>
  <meta name="description" content="Electronic Medical Certificate & Time Slip Management System">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
</head>
<body>
<div class="app-wrapper">

  <!-- ── Sidebar ──────────────────────────────────────── -->
  <aside class="sidebar" role="navigation" aria-label="Main Menu">
    <div class="sidebar-brand">
      <div class="brand-icon">🏥</div>
      <div class="brand-text">
        <h1>eMC</h1>
        <span>Medical Certificate System</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <p class="nav-section-title">Main</p>

      <a href="<?= base_url('/') ?>"
         class="nav-item <?= (current_url() === base_url('/') || current_url() === base_url()) ? 'active' : '' ?>">
        <span class="nav-icon">📊</span>
        Dashboard
      </a>

      <p class="nav-section-title">Documents</p>

      <?php
        $isDocsActive = str_contains(current_url(), '/documents')
                     || str_contains(current_url(), '/mc')
                     || str_contains(current_url(), '/timeslip');
      ?>
      <a href="<?= base_url('documents') ?>"
         class="nav-item <?= $isDocsActive ? 'active' : '' ?>"
         id="nav-medical-documents">
        <span class="nav-icon">📄</span>
        Medical Documents
      </a>

      <a href="<?= base_url('patient-history') ?>"
         class="nav-item <?= str_contains(current_url(), '/patient-history') ? 'active' : '' ?>"
         id="nav-patient-history">
        <span class="nav-icon">🔍</span>
        Patient History
      </a>

      <?php if (session()->get('role') === 'admin'): ?>
        <p class="nav-section-title">IT Administrator</p>

        <a href="<?= base_url('admin/doctors') ?>"
           class="nav-item <?= str_contains(current_url(), '/admin/doctors') ? 'active' : '' ?>">
          <span class="nav-icon">👨‍⚕️</span>
          Manage Doctors
        </a>

        <a href="<?= base_url('admin/api-status') ?>"
           class="nav-item <?= str_contains(current_url(), '/admin/api-status') ? 'active' : '' ?>">
          <span class="nav-icon">📡</span>
          iPesakit API Status
        </a>

        <a href="<?= base_url('admin/audit-log') ?>"
           class="nav-item <?= str_contains(current_url(), '/admin/audit-log') ? 'active' : '' ?>">
          <span class="nav-icon">📋</span>
          Document Audit Log
        </a>
      <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
      <?php if (session()->get('login_source') === 'ipesakit' || session()->get('login_source') === 'ipesakit_fallback'): ?>
        <div style="margin-bottom:10px; background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.25); border-radius:8px; padding:8px 12px; display:flex; align-items:center; gap:8px;">
          <span style="font-size:14px;">🔗</span>
          <div style="line-height:1.3;">
            <strong style="font-size:11px; color:#86efac; display:block;">Sesi iPesakit Aktif</strong>
            <span style="font-size:10.5px; color:#4ade80;">Data pesakit dari iPesakit</span>
          </div>
        </div>
      <?php endif; ?>
      <div style="margin-bottom: 8px;">
        <a href="<?= base_url('login') ?>" style="font-size: 12px; color: #2563eb; text-decoration: none; display: flex; align-items: center; gap: 4px;">
          <span>🔄</span> Switch Role / Login
        </a>
      </div>
      <p>eMC v1.0 &copy; <?= date('Y') ?></p>
    </div>
  </aside>

  <!-- ── Main Content ──────────────────────────────────── -->
  <main class="main-content">

    <!-- Topbar -->
    <header class="topbar" role="banner">
      <div class="topbar-left">
        <h2><?= esc($title ?? 'Dashboard') ?></h2>
        <p class="breadcrumb">Electronic Medical Certificate &amp; Time Slip Management System</p>
      </div>
      <div class="topbar-right" style="display: flex; align-items: center; gap: 14px;">
        <span class="topbar-date">📅 <?= date('d M Y, H:i') ?></span>

        <?php if (session()->get('logged_in')): ?>
          <div style="display: flex; align-items: center; gap: 10px; background: #f1f5f9; padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
            <span style="font-size: 16px;"><?= (session()->get('role') === 'admin') ? '🛠️' : '👨‍⚕️' ?></span>
            <div style="line-height: 1.2;">
              <strong style="font-size: 12.5px; color: #0f172a; display: block; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <?= esc(session()->get('user_name') ?: 'User') ?>
              </strong>
              <span style="font-size: 11px; color: #64748b;">
                <?= (session()->get('role') === 'admin') ? 'IT Admin' : (session()->get('mcr_number') ? esc(session()->get('mcr_number')) : 'Doctor') ?>
              </span>
            </div>
          </div>
          <a href="<?= base_url('logout') ?>" class="btn btn-outline btn-sm" title="Log Out" style="color: #ef4444; border-color: #fca5a5;">
            Log Out ➔
          </a>
        <?php else: ?>
          <a href="<?= base_url('login') ?>" class="btn btn-primary btn-sm">
            🔑 Log In
          </a>
        <?php endif; ?>
      </div>
    </header>

    <!-- Flash Messages -->
    <?php if (session()->has('error')): ?>
      <div style="padding: 0 28px; padding-top: 16px;">
        <div class="alert alert-danger">⚠️ <?= session('error') ?></div>
      </div>
    <?php endif; ?>
    <?php if (session()->has('success')): ?>
      <div style="padding: 0 28px; padding-top: 16px;">
        <div class="alert alert-success">✅ <?= session('success') ?></div>
      </div>
    <?php endif; ?>
    <?php if (session()->has('info')): ?>
      <div style="padding: 0 28px; padding-top: 16px;">
        <div class="alert alert-info">ℹ️ <?= session('info') ?></div>
      </div>
    <?php endif; ?>

    <!-- Page Content -->
    <div class="page-body">
      <?= view($content_view ?? 'home/index') ?>
    </div>

  </main>
</div>

<script>
  // Active nav highlight helper
  document.querySelectorAll('.nav-item').forEach(el => {
    el.addEventListener('click', function() {
      document.querySelectorAll('.nav-item').forEach(e => e.classList.remove('active'));
      this.classList.add('active');
    });
  });
</script>
</body>
</html>
