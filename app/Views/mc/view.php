<?php
$requestedLang = strtoupper($_GET['lang'] ?? ($lang ?? 'EN'));
$isBM = ($requestedLang === 'BM');
$activeLang = $isBM ? 'BM' : 'BI';
?>
<!DOCTYPE html>
<html lang="<?= $isBM ? 'ms' : 'en' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isBM ? 'Sijil Cuti Sakit' : 'Medical Certificate' ?> — <?= esc($mc['reference_no']) ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <style>
    /* ── Screen-only action bar ───────────────── */
    .action-bar {
      position: fixed;
      top: 0; left: 0; right: 0;
      background: #fff;
      border-bottom: 1px solid #e0e0e0;
      padding: 12px 24px;
      display: flex;
      align-items: center;
      gap: 12px;
      z-index: 999;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .action-bar .spacer { flex: 1; }

    /* Action buttons grouping */
    .action-btn-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* Language Toggle Pills */
    .lang-switch-pills {
      display: inline-flex;
      align-items: center;
      background: #f1f5f9;
      border: 1.5px solid #cbd5e1;
      border-radius: 8px;
      padding: 3px;
      gap: 2px;
    }

    .lang-pill {
      padding: 5px 14px;
      font-size: 13px;
      font-weight: 700;
      color: #475569;
      border-radius: 6px;
      text-decoration: none;
      transition: all 0.15s ease;
      line-height: 1.2;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .lang-pill:hover {
      color: #0f172a;
      background: #e2e8f0;
    }

    .lang-pill.active {
      background: #1a73e8;
      color: #ffffff;
      box-shadow: 0 1px 3px rgba(26, 115, 232, 0.3);
    }

    .btn-print-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 36px;
      padding: 0;
      border-radius: 8px;
      transition: all 0.2s ease;
    }

    .btn-print-icon svg {
      display: block;
    }

    body.has-action-bar { padding-top: 64px; }

    /* ── Document Wrapper ─────────────────────── */
    .doc-wrapper {
      max-width: 600px;
      margin: 30px auto;
      padding: 24px;
    }

    /* ── The actual document (print target) ──── */
    .print-doc {
      background: #fff;
      border: 1px solid #dde3ec;
      border-radius: 12px;
      padding: 36px 40px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      position: relative;
      overflow: hidden;
      font-family: 'Times New Roman', Times, serif;
      font-size: 12pt;
      color: #111;
    }

    /* ── Header Logo ──────────────────────────── */
    .header-logo-container {
      text-align: center;
      margin-bottom: 8px;
    }

    .header-logo {
      height: 52px;
      width: auto;
      max-width: 190px;
      object-fit: contain;
      display: inline-block;
    }

    /* ── Center Watermark ─────────────────────── */
    .doc-watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 250px;
      max-width: 65%;
      opacity: 0.06;
      pointer-events: none;
      user-select: none;
      z-index: 0;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .doc-watermark img {
      width: 100%;
      height: auto;
      display: block;
    }

    /* Ensure document elements sit above watermark */
    .doc-header,
    .doc-ref-row,
    .cert-body,
    .details-table,
    .diagnosis-box,
    .notes-box,
    .signature-section,
    .doc-footer {
      position: relative;
      z-index: 1;
    }

    /* ── Header ───────────────────────────────── */
    .doc-header {
      text-align: center;
      border-bottom: 2.5px solid #1a73e8;
      padding-bottom: 18px;
      margin-bottom: 22px;
    }

    .doc-header .clinic-name {
      font-family: 'Poppins', sans-serif;
      font-size: 20pt;
      font-weight: 700;
      color: #1a73e8;
      letter-spacing: -0.5px;
    }

    .doc-header .clinic-address {
      font-size: 9.5pt;
      color: #555;
      margin-top: 4px;
      line-height: 1.5;
    }

    .doc-header .doc-type {
      display: inline-block;
      background: #1a73e8;
      color: #fff;
      font-size: 13pt;
      font-weight: 700;
      font-family: 'Arial', sans-serif;
      letter-spacing: 2px;
      padding: 6px 32px;
      border-radius: 99px;
      margin-top: 14px;
    }

    /* ── Reference / Date row ─────────────────── */
    .doc-ref-row {
      display: flex;
      justify-content: space-between;
      font-size: 10pt;
      color: #444;
      background: #f5f7fb;
      border: 1px solid #e0e8f4;
      border-radius: 8px;
      padding: 10px 16px;
      margin-bottom: 22px;
    }

    .doc-ref-row .ref-no {
      font-weight: 700;
      font-family: 'Courier New', monospace;
      font-size: 11pt;
      color: #111;
    }

    /* ── Certificate Body ─────────────────────── */
    .cert-body {
      line-height: 2.0;
      font-size: 12pt;
      text-align: justify;
      color: #111;
    }

    .cert-body .patient-name {
      font-weight: 700;
      text-decoration: underline;
      font-size: 13pt;
    }

    .cert-body .ic-number {
      font-family: 'Courier New', monospace;
      font-weight: 700;
    }

    .cert-body .highlight {
      background: #e8f0fe;
      padding: 2px 6px;
      border-radius: 4px;
      font-weight: 700;
    }

    /* ── Details Table ────────────────────────── */
    .details-table {
      width: 100%;
      margin: 18px 0;
      border-collapse: collapse;
      font-size: 11pt;
    }

    .details-table td {
      padding: 7px 10px;
      vertical-align: top;
      border: none;
    }

    .details-table td:first-child {
      width: 42%;
      color: #555;
      font-weight: 600;
    }

    .details-table td:last-child {
      color: #111;
      font-weight: 700;
    }

    .details-table tr:nth-child(even) td { background: #f9fafb; }

    /* ── Diagnosis Box ─────────────────────────── */
    .diagnosis-box {
      border: 1.5px solid #c5d8f8;
      background: #f0f7ff;
      border-radius: 8px;
      padding: 12px 16px;
      margin: 16px 0;
      font-size: 11pt;
    }

    .diagnosis-box .label {
      font-size: 9.5pt;
      color: #555;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }

    .diagnosis-box .value { font-weight: 700; color: #1a1a1a; }

    /* ── Findings List (Standard Medical Certificate) ── */
    .findings-list {
      margin: 8px 0 12px 0;
      display: flex;
      flex-direction: column;
      gap: 5px;
      font-size: 10.5pt;
      line-height: 1.45;
    }

    .finding-item {
      display: flex;
      align-items: flex-start;
      gap: 8px;
    }

    .finding-code {
      font-weight: 700;
      color: #1e293b;
      min-width: 20px;
    }

    .finding-text {
      flex: 1;
      color: #334155;
    }

    .finding-item.active-finding .finding-text {
      color: #0f172a;
    }

    .blank-line {
      color: #64748b;
      letter-spacing: -0.5px;
    }

    /* ── Signatures & Stamp Section ────────────── */
    .signature-section {
      margin-top: 28px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
    }

    /* Hospital Official Stamp Block (Left) */
    .hospital-stamp-block {
      flex: 1;
      max-width: 240px;
      text-align: center;
    }

    .hospital-stamp-space {
      height: 75px; /* Ruang kosong untuk cop basah fizikal */
    }

    .hospital-stamp-line,
    .doctor-sig-line {
      color: #333;
      letter-spacing: 1px;
      font-size: 10pt;
      line-height: 1;
      user-select: none;
      margin: 0 0 6px 0;
      white-space: nowrap;
      overflow: hidden;
    }

    .hospital-stamp-subtext {
      font-size: 9.5pt;
      color: #111;
      line-height: 1.4;
      font-weight: 600;
      text-align: center;
    }

    /* Doctor Signature Block (Right) */
    .signature-block {
      flex: 1;
      max-width: 250px;
      text-align: center;
    }

    .sig-manual-space {
      height: 75px; /* Sama tinggi ruang cop (75px) = 100% selari! */
    }

    .sig-details .doc-name { font-weight: 700; font-size: 10.5pt; color: #0f172a; line-height: 1.35; }
    .sig-details .doc-qual { font-size: 9pt; color: #475569; margin-top: 2px; line-height: 1.3; }
    .sig-details .doc-mcr  { font-size: 9pt; color: #334155; font-family: monospace; font-weight: 600; margin-top: 2px; }
    .sig-details .doc-dept { font-size: 9pt; color: #1a73e8; margin-top: 2px; }


    /* ── Footer ───────────────────────────────── */
    .doc-footer {
      text-align: center;
      font-size: 8.5pt;
      color: #aaa;
      margin-top: 28px;
      padding-top: 14px;
      border-top: 1px solid #e0e0e0;
      font-style: italic;
    }

    /* ── Notes box ────────────────────────────── */
    .notes-box {
      background: #fffbeb;
      border: 1px solid #f5d76e;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 10.5pt;
      margin-top: 14px;
      color: #5c4a00;
    }

    /* ── PRINT overrides ──────────────────────── */
    @media print {
      .action-bar, body.has-action-bar { padding-top: 0 !important; }
      .action-bar { display: none !important; }
      .doc-wrapper { margin: 0; padding: 0; max-width: 100%; }
      .print-doc {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0;
        padding: 10mm 12mm;
        page-break-inside: avoid;
      }
      .doc-watermark {
        opacity: 0.07 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      @page { size: A5 portrait; margin: 8mm; }
    }
  </style>
</head>
<body class="has-action-bar">

<!-- ── Action Bar (screen only) ──── -->
<div class="action-bar no-print">
  <a href="<?= base_url('mc') ?>" class="btn btn-outline btn-sm">← MC Records</a>
  <a href="<?= base_url('patients/select/' . $mc['patient_id']) ?>" class="btn btn-outline btn-sm">👤 Patient</a>

  <div class="spacer"></div>

  <!-- [ BM ] [ BI ] [ 🖨 ] -->
  <div class="action-btn-group">
    <div class="lang-switch-pills" role="group" aria-label="Language selection">
      <a href="?lang=BM" class="lang-pill <?= $isBM ? 'active' : '' ?>" id="btn-lang-bm" title="Bahasa Malaysia">BM</a>
      <a href="?lang=BI" class="lang-pill <?= !$isBM ? 'active' : '' ?>" id="btn-lang-bi" title="Bahasa Inggeris">BI</a>
    </div>

    <button onclick="window.print()" class="btn btn-primary btn-print-icon" id="btn-print-mc" title="<?= $isBM ? 'Cetak Sijil Cuti Sakit' : 'Print Medical Certificate' ?>" aria-label="Print">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="6 9 6 2 18 2 18 9"></polyline>
        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
        <rect x="6" y="14" width="12" height="8"></rect>
      </svg>
    </button>
  </div>
</div>

<!-- ── Document ──────────────────── -->
<div class="doc-wrapper">
<div class="print-doc" id="mc-document">

  <!-- Center Watermark -->
  <div class="doc-watermark" aria-hidden="true">
    <img src="<?= base_url('assets/images/watermark.png') ?>" alt="Watermark">
  </div>

  <!-- Header -->
  <div class="doc-header">
    <div class="header-logo-container">
      <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo UniSZA" class="header-logo">
    </div>
    <div class="clinic-name">HOSPITAL SULTAN ZAINAL ABIDIN</div>
    <div class="clinic-address">
      Jalan Gong Badak, Kampung Gong Badak, 21300 Kuala Nerus, Terengganu<br>
      Tel: 09-665 5555 &nbsp;|&nbsp; Email: hosza@unisza.edu.my
    </div>
    <div class="doc-type">
      <?= $isBM ? 'SIJIL CUTI SAKIT' : 'MEDICAL CERTIFICATE' ?>
    </div>
  </div>

  <!-- Ref Row with QR Code Verification -->
  <div class="doc-ref-row" style="align-items: center;">
    <div>
      <div>
        <span style="color:#888; font-size:9.5pt;"><?= $isBM ? 'No. Rujukan' : 'Reference No.' ?>: </span>
        <span class="ref-no"><?= esc($mc['reference_no']) ?></span>
      </div>
      <div style="margin-top: 4px;">
        <span style="color:#888; font-size:9.5pt;"><?= $isBM ? 'Tarikh Keluar' : 'Date Issued' ?>: </span>
        <strong><?= date('d/m/Y', strtotime($mc['date_issued'])) ?></strong>
      </div>
    </div>
    <div style="display: flex; align-items: center; gap: 8px;">
      <div style="text-align: right; line-height: 1.2;">
        <span style="font-size: 7.5pt; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block;">
          <?= $isBM ? 'Imbas Pengesahan' : 'Scan to Verify' ?>
        </span>
        <span style="font-size: 7pt; color: #94a3b8;">
          hosza.unisza.edu.my
        </span>
      </div>
      <div class="qr-box" style="width: 52px; height: 52px; background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 2px; display: flex; align-items: center; justify-content: center;">
        <?= \App\Libraries\QRCodeGenerator::svg(base_url('verify/mc/' . rawurlencode($mc['reference_no'])), 2) ?>
      </div>
    </div>
  </div>

  <?php
    // Helper format No. Kad Pengenalan XXXXXX-XX-XXXX
    $formatIC = function(?string $ic): string {
        if (empty($ic)) return '—';
        $digits = preg_replace('/[^0-9]/', '', $ic);
        if (strlen($digits) === 12) {
            return substr($digits, 0, 6) . '-' . substr($digits, 6, 2) . '-' . substr($digits, 8, 4);
        }
        return $ic;
    };
  ?>

  <?php if ($isBM): ?>
  <!-- ── BM Version ──────────────────────────────────── -->

  <div class="cert-body">
    <p>
      Dengan ini saya mengesahkan bahawa saya telah memeriksa:
    </p>
  </div>

  <!-- Patient Details Table -->
  <table class="details-table">
    <tr>
      <td>Nama Pesakit</td>
      <td class="patient-name"><?= esc($mc['full_name']) ?></td>
    </tr>
    <tr>
      <td>No. Kad Pengenalan</td>
      <td class="ic-number"><?= esc($formatIC($mc['ic_number'] ?? '')) ?></td>
    </tr>
    <tr>
      <td>Kementerian / Jabatan</td>
      <td>Yang berkenaan</td>
    </tr>
  </table>

  <div class="cert-body">
    <p style="margin-bottom: 6px;">dan mendapati beliau :</p>
    <div class="findings-list">
      <!-- a) Masuk Wad -->
      <div class="finding-item <?= !empty($mc['has_ward']) ? 'active-finding' : '' ?>">
        <span class="finding-code">a)</span>
        <span class="finding-text">
          <?php if (!empty($mc['has_ward']) && !empty($mc['ward_num_days']) && !empty($mc['ward_start_date'])): ?>
            Telah dimasukkan ke wad selama <strong class="highlight"><?= $mc['ward_num_days'] ?> hari</strong> dari <strong><?= date('d/m/Y', strtotime($mc['ward_start_date'])) ?></strong> hingga <strong><?= date('d/m/Y', strtotime($mc['ward_end_date'])) ?></strong>
          <?php else: ?>
            Telah dimasukkan ke wad selama <span class="blank-line">_____________</span> hari dari <span class="blank-line">___________</span> hingga <span class="blank-line">__________</span>
          <?php endif; ?>
        </span>
      </div>

      <!-- b) Cuti Sakit Biasa -->
      <div class="finding-item <?= !empty($mc['has_unfit']) ? 'active-finding' : '' ?>">
        <span class="finding-code">b)</span>
        <span class="finding-text">
          <?php if (!empty($mc['has_unfit']) && !empty($mc['num_days']) && !empty($mc['start_date'])): ?>
            Tidak sihat untuk menjalankan tugas atau persekolahan selama <strong class="highlight"><?= $mc['num_days'] ?> hari</strong> dari <strong><?= date('d/m/Y', strtotime($mc['start_date'])) ?></strong> hingga <strong><?= date('d/m/Y', strtotime($mc['end_date'])) ?></strong>
          <?php else: ?>
            Tidak sihat untuk menjalankan tugas atau persekolahan selama <span class="blank-line">_________</span> hari dari <span class="blank-line">____________</span> hingga <span class="blank-line">__________</span>
          <?php endif; ?>
        </span>
      </div>

      <!-- c) Rawatan Susulan -->
      <div class="finding-item <?= !empty($mc['has_followup']) ? 'active-finding' : '' ?>">
        <span class="finding-code">c)</span>
        <span class="finding-text">
          <?php if (!empty($mc['has_followup']) && !empty($mc['followup_date'])): ?>
            Perlu membuat rawatan susulan pada <strong><?= date('d/m/Y', strtotime($mc['followup_date'])) ?></strong>
          <?php else: ?>
            Perlu membuat rawatan susulan pada <span class="blank-line">________________</span>
          <?php endif; ?>
        </span>
      </div>

      <!-- d) Tugas-Tugas Ringan -->
      <div class="finding-item <?= !empty($mc['has_light_duty']) ? 'active-finding' : '' ?>">
        <span class="finding-code">d)</span>
        <span class="finding-text">
          <?php if (!empty($mc['has_light_duty']) && !empty($mc['light_duty_num_days']) && !empty($mc['light_duty_start_date'])): ?>
            Sihat tetapi hanya boleh menjalankan tugas-tugas ringan selama <strong class="highlight"><?= $mc['light_duty_num_days'] ?> hari</strong> dari <strong><?= date('d/m/Y', strtotime($mc['light_duty_start_date'])) ?></strong> hingga <strong><?= date('d/m/Y', strtotime($mc['light_duty_end_date'])) ?></strong>
          <?php else: ?>
            Sihat tetapi hanya boleh menjalankan tugas-tugas ringan selama <span class="blank-line">________</span> hari dari <span class="blank-line">__________</span> hingga <span class="blank-line">_________</span>
          <?php endif; ?>
        </span>
      </div>
    </div>
  </div>

  <?php else: ?>
  <!-- ── EN Version ──────────────────────────────────── -->

  <div class="cert-body">
    <p>
      I hereby certify that I have examined:
    </p>
  </div>

  <table class="details-table">
    <tr>
      <td>Patient Name</td>
      <td class="patient-name"><?= esc($mc['full_name']) ?></td>
    </tr>
    <tr>
      <td>Identity Card No.</td>
      <td class="ic-number"><?= esc($formatIC($mc['ic_number'] ?? '')) ?></td>
    </tr>
    <tr>
      <td>Ministry / Department</td>
      <td>To whom it may concern</td>
    </tr>
  </table>

  <div class="cert-body">
    <p style="margin-bottom: 6px;">and found that he/she :</p>
    <div class="findings-list">
      <!-- a) Admission to Ward -->
      <div class="finding-item <?= !empty($mc['has_ward']) ? 'active-finding' : '' ?>">
        <span class="finding-code">a)</span>
        <span class="finding-text">
          <?php if (!empty($mc['has_ward']) && !empty($mc['ward_num_days']) && !empty($mc['ward_start_date'])): ?>
            Has been admitted to ward for <strong class="highlight"><?= $mc['ward_num_days'] ?> day<?= $mc['ward_num_days'] > 1 ? 's' : '' ?></strong> from <strong><?= date('d/m/Y', strtotime($mc['ward_start_date'])) ?></strong> to <strong><?= date('d/m/Y', strtotime($mc['ward_end_date'])) ?></strong>
          <?php else: ?>
            Has been admitted to ward for <span class="blank-line">_____________</span> day(s) from <span class="blank-line">___________</span> to <span class="blank-line">__________</span>
          <?php endif; ?>
        </span>
      </div>

      <!-- b) Unfit for Duty -->
      <div class="finding-item <?= !empty($mc['has_unfit']) ? 'active-finding' : '' ?>">
        <span class="finding-code">b)</span>
        <span class="finding-text">
          <?php if (!empty($mc['has_unfit']) && !empty($mc['num_days']) && !empty($mc['start_date'])): ?>
            Is unfit to perform duties or attend school for <strong class="highlight"><?= $mc['num_days'] ?> day<?= $mc['num_days'] > 1 ? 's' : '' ?></strong> from <strong><?= date('d/m/Y', strtotime($mc['start_date'])) ?></strong> to <strong><?= date('d/m/Y', strtotime($mc['end_date'])) ?></strong>
          <?php else: ?>
            Is unfit to perform duties or attend school for <span class="blank-line">_________</span> day(s) from <span class="blank-line">____________</span> to <span class="blank-line">__________</span>
          <?php endif; ?>
        </span>
      </div>

      <!-- c) Follow-up Treatment -->
      <div class="finding-item <?= !empty($mc['has_followup']) ? 'active-finding' : '' ?>">
        <span class="finding-code">c)</span>
        <span class="finding-text">
          <?php if (!empty($mc['has_followup']) && !empty($mc['followup_date'])): ?>
            Requires follow-up treatment on <strong><?= date('d/m/Y', strtotime($mc['followup_date'])) ?></strong>
          <?php else: ?>
            Requires follow-up treatment on <span class="blank-line">________________</span>
          <?php endif; ?>
        </span>
      </div>

      <!-- d) Light Duties -->
      <div class="finding-item <?= !empty($mc['has_light_duty']) ? 'active-finding' : '' ?>">
        <span class="finding-code">d)</span>
        <span class="finding-text">
          <?php if (!empty($mc['has_light_duty']) && !empty($mc['light_duty_num_days']) && !empty($mc['light_duty_start_date'])): ?>
            Is fit but only able to perform light duties for <strong class="highlight"><?= $mc['light_duty_num_days'] ?> day<?= $mc['light_duty_num_days'] > 1 ? 's' : '' ?></strong> from <strong><?= date('d/m/Y', strtotime($mc['light_duty_start_date'])) ?></strong> to <strong><?= date('d/m/Y', strtotime($mc['light_duty_end_date'])) ?></strong>
          <?php else: ?>
            Is fit but only able to perform light duties for <span class="blank-line">________</span> day(s) from <span class="blank-line">__________</span> to <span class="blank-line">_________</span>
          <?php endif; ?>
        </span>
      </div>
    </div>
  </div>

  <?php endif; ?>

  <?php if (!empty($mc['notes'])): ?>
  <div class="notes-box">
    <?= $isBM ? '📝 Catatan:' : '📝 Notes:' ?>
    <?= esc($mc['notes']) ?>
  </div>
  <?php endif; ?>

  <!-- Signature & Official Stamp Section -->
  <div class="signature-section">
    <!-- Ruang Cop Rasmi Hospital (Cop Basah Fizikal) -->
    <div class="hospital-stamp-block">
      <div class="hospital-stamp-space"></div>
      <div class="hospital-stamp-line">.......................................................</div>
      <div class="hospital-stamp-subtext">
        <?= $isBM ? 'Klinik / Hospital<br>(Cop Rasmi)' : 'Clinic / Hospital<br>(Official Stamp)' ?>
      </div>
    </div>

    <!-- Ruang Tandatangan & Maklumat Lengkap Pegawai Perubatan -->
    <div class="signature-block">
      <div class="sig-manual-space"></div>
      <div class="doctor-sig-line">.......................................................</div>
      <div class="sig-details">
        <div class="doc-name"><?= esc($mc['doctor_name']) ?></div>
        <?php if (!empty($mc['qualification'])): ?>
        <div class="doc-qual"><?= esc($mc['qualification']) ?></div>
        <?php endif; ?>
        <?php if (!empty($mc['mcr_number'])): ?>
        <?php
          $mcrVal = trim($mc['mcr_number']);
          $mcrFormatted = preg_match('/^mmc\s*no/i', $mcrVal) ? $mcrVal : 'MMC No : ' . $mcrVal;
        ?>
        <div class="doc-mcr"><?= esc($mcrFormatted) ?></div>
        <?php endif; ?>
        <?php if (!empty($mc['department'])): ?>
        <div class="doc-dept"><?= esc(\App\Libraries\DocumentTranslator::translateDepartment($mc['department'], $activeLang)) ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="doc-footer">
    <?= $isBM
      ? 'Dokumen ini dijana secara komputer.'
      : 'This document is computer-generated.' ?>
    &nbsp;|&nbsp; <?= date('d/m/Y H:i') ?>
  </div>

</div><!-- /.print-doc -->
</div><!-- /.doc-wrapper -->

</body>
</html>
