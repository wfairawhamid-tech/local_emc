<?php
/**
 * Time Slip View Template
 * Hospital Sultan Zainal Abidin
 * Supports English (BI) and Bahasa Malaysia (BM) versions.
 * Centralized translation presentation layer via DocumentTranslator.
 * Zero mixed-language output.
 */

use App\Libraries\DocumentTranslator;

// ── 1. Language Determination ──────────────────────────────
$requestedLang = strtoupper($_GET['lang'] ?? ($lang ?? 'BI'));
$isBM = ($requestedLang === 'BM');
$activeLang = $isBM ? 'BM' : 'BI';

// ── 2. Data Mapping & Preparation ──────────────────────────
$visitType    = $ts['visit_type'] ?? 'diri_sendiri';
$hasCompanion = ($visitType !== 'diri_sendiri') && !empty(trim($ts['companion_name'] ?? ''));

// Format Identification Number: XXXXXX-XX-XXXX (display only)
$formatIC = function(?string $ic): string {
    if (empty($ic)) return '—';
    $trimmed = trim($ic);
    if (strpos($trimmed, '-') !== false) {
        return $trimmed;
    }
    $digits = preg_replace('/[^0-9]/', '', $trimmed);
    if (strlen($digits) === 12) {
        return substr($digits, 0, 6) . '-' . substr($digits, 6, 2) . '-' . substr($digits, 8, 4);
    }
    return $trimmed;
};

// Determine Person Attending Name & IC
if ($hasCompanion) {
    $attendantName = trim($ts['companion_name']);
    $attendantIC   = !empty(trim($ts['companion_ic'] ?? '')) ? trim($ts['companion_ic']) : trim($ts['ic_number']);

    // Determine gender of attendant
    $attendantGender = null;
    if (!empty($ts['companion_ic'])) {
        $icDigits = preg_replace('/[^0-9]/', '', $ts['companion_ic']);
        if (!empty($icDigits)) {
            $lastDigit = (int) substr($icDigits, -1);
            $attendantGender = ($lastDigit % 2 !== 0) ? 'male' : 'female';
        }
    }
    if (!$attendantGender) {
        $cName = ' ' . strtolower(trim($ts['companion_name'])) . ' ';
        if (preg_match('/\b(bin|en|encik|mr|mr\.)\b/i', $cName)) {
            $attendantGender = 'male';
        } elseif (preg_match('/\b(binti|bt|bte|puan|pn|cik|mrs|ms|mrs\.|ms\.)\b/i', $cName)) {
            $attendantGender = 'female';
        }
    }
    if (!$attendantGender && $visitType === 'suami_isteri') {
        $attendantGender = ($ts['gender'] === 'Lelaki') ? 'female' : 'male';
    }
    if (!$attendantGender) {
        $attendantGender = ($ts['gender'] === 'Lelaki') ? 'male' : 'female';
    }
} else {
    $attendantName   = trim($ts['full_name']);
    $attendantIC     = trim($ts['ic_number']);
    $attendantGender = ($ts['gender'] === 'Lelaki') ? 'male' : 'female';
}

$patientIsMale = ($ts['gender'] === 'Lelaki');
$patientName   = trim($ts['full_name'] ?? '');

// ── Centralized Translations via DocumentTranslator ────────
$forRelationBM = DocumentTranslator::translateRelationship(
    $visitType,
    $ts['companion_relation'] ?? null,
    $attendantGender,
    $patientIsMale,
    $hasCompanion ? $patientName : null,
    'BM'
);

$forRelationBI = DocumentTranslator::translateRelationship(
    $visitType,
    $ts['companion_relation'] ?? null,
    $attendantGender,
    $patientIsMale,
    $hasCompanion ? $patientName : null,
    'BI'
);

// Department / Clinic display translation
$rawDept = trim($ts['department'] ?? '');
$clinicDisplayBM = DocumentTranslator::translateClinic($rawDept, 'BM');
$clinicDisplayBI = DocumentTranslator::translateClinic($rawDept, 'BI');

$deptDisplayBM = DocumentTranslator::translateDepartment($rawDept, 'BM');
$deptDisplayBI = DocumentTranslator::translateDepartment($rawDept, 'BI');

// Purpose translation
$purposeDisplayBM = DocumentTranslator::translatePurpose($ts['purpose'] ?? null, 'BM');
$purposeDisplayBI = DocumentTranslator::translatePurpose($ts['purpose'] ?? null, 'BI');

// ── 3. Date & Time Calculations ────────────────────────────
$dateGeneratedFormatted = (!empty($ts['created_at']) && $ts['created_at'] !== '0000-00-00 00:00:00')
    ? date('d/m/Y', strtotime($ts['created_at']))
    : (!empty($ts['date_issued']) ? date('d/m/Y', strtotime($ts['date_issued'])) : date('d/m/Y'));

$treatmentDateFormatted = !empty($ts['date_issued']) ? date('d/m/Y', strtotime($ts['date_issued'])) : $dateGeneratedFormatted;

$timeInFormatted  = !empty($ts['time_in']) ? date('h:i A', strtotime($ts['time_in'])) : '—';
$timeOutFormatted = !empty($ts['time_out']) ? date('h:i A', strtotime($ts['time_out'])) : '—';

$timeRangeBM = $timeInFormatted . ' hingga ' . $timeOutFormatted;
$timeRangeBI = $timeInFormatted . ' to ' . $timeOutFormatted;

// Sentence construction:
// English: "has attended for medical treatment at the General Medicine clinic for herself."
$biClinicSuffix = preg_match('/(clinic|department|unit|ward)$/i', $clinicDisplayBI) ? '' : ' clinic';
$certSentenceBI = 'has attended for medical treatment at the <strong>' . esc($clinicDisplayBI) . '</strong>' . $biClinicSuffix . ' for <strong>' . esc($forRelationBI) . '</strong>.<br>Date : <strong>' . esc($treatmentDateFormatted) . '</strong> &nbsp;&nbsp;&nbsp;&nbsp; Time : <strong>' . esc($timeRangeBI) . '</strong>';

// Malay: "telah hadir mendapatkan rawatan di klinik Perubatan Am untuk dirinya sendiri."
$bmClinicPrefix = preg_match('/^(klinik|jabatan|unit|wad)\b/i', $clinicDisplayBM) ? '' : 'klinik ';
$certSentenceBM = 'telah hadir mendapatkan rawatan di ' . $bmClinicPrefix . '<strong>' . esc($clinicDisplayBM) . '</strong> untuk <strong>' . esc($forRelationBM) . '</strong>.<br>Tarikh : <strong>' . esc($treatmentDateFormatted) . '</strong> &nbsp;&nbsp;&nbsp;&nbsp; Waktu : <strong>' . esc($timeRangeBM) . '</strong>';

// Purpose sentences
$purposeSentenceBI = 'He/She attended for the purpose of <strong>' . esc($purposeDisplayBI) . '</strong>.';
$purposeSentenceBM = 'Beliau hadir bagi tujuan <strong>' . esc($purposeDisplayBM) . '</strong>.';
?>
<!DOCTYPE html>
<html lang="<?= $isBM ? 'ms' : 'en' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title id="doc-window-title"><?= $isBM ? 'SLIP MASA' : 'TIME SLIP' ?> — <?= esc($ts['reference_no']) ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <style>
    /* ── Screen-only Action Bar ────────────────────────────── */
    .action-bar {
      position: fixed;
      top: 0; left: 0; right: 0;
      background: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      padding: 10px 24px;
      display: flex;
      align-items: center;
      gap: 12px;
      z-index: 999;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    .action-bar .spacer { flex: 1; }

    body.has-action-bar {
      padding-top: 64px;
      background: #f1f5f9;
    }

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
      transition: all var(--transition, 0.2s ease);
    }

    .btn-print-icon svg {
      display: block;
    }

    /* ── Document Container (Portrait B3 View) ────────────── */
    .doc-wrapper {
      max-width: 780px;
      margin: 32px auto;
      padding: 0 16px 40px;
    }

    .print-doc {
      background: #ffffff;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      padding: 46px 54px;
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
      font-family: 'Times New Roman', Times, 'Liberation Serif', serif;
      font-size: 12pt;
      line-height: 1.65;
      color: #111827;
      position: relative;
      overflow: hidden;
    }

    /* ── Header Logo ───────────────────────────────────────── */
    .header-logo-container {
      text-align: center;
      margin-bottom: 8px;
    }

    .header-logo {
      height: 60px;
      width: auto;
      max-width: 220px;
      object-fit: contain;
      display: inline-block;
    }

    /* ── Document Watermark ────────────────────────────────── */
    .doc-watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 300px;
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

    /* Ensure document contents sit above watermark */
    .hospital-letterhead,
    .doc-header-meta,
    .doc-title-container,
    .cert-intro,
    .info-block,
    .cert-statement,
    .purpose-statement,
    .signature-section,
    .doc-footer {
      position: relative;
      z-index: 1;
    }

    /* ── Hospital Letterhead (Matched with MC Header) ────── */
    .hospital-letterhead {
      text-align: center;
      margin-bottom: 8px;
    }

    .hospital-name {
      font-family: 'Poppins', sans-serif;
      font-size: 20pt;
      font-weight: 700;
      color: #1a73e8;
      letter-spacing: -0.5px;
      margin-bottom: 4px;
    }

    .hospital-address {
      font-family: 'Arial', 'Helvetica Neue', sans-serif;
      font-size: 9.5pt;
      color: #555555;
      line-height: 1.5;
    }

    .letterhead-divider {
      border-bottom: 2.5px solid #1a73e8;
      margin-top: 14px;
      margin-bottom: 18px;
    }

    /* ── Reference / Date row (Sama seperti MC) ───────────────── */
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

    /* ── Document Title ────────────────────────────────────── */
    .doc-title-container {
      text-align: center;
      margin: 18px 0 28px;
    }

    .doc-title {
      font-family: 'Times New Roman', Times, serif;
      font-size: 16pt;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #0f172a;
      margin: 0;
      padding-bottom: 4px;
      display: inline-block;
      border-bottom: 1.5px solid #0f172a;
    }

    /* ── Structured Information Table (Pixel-perfect Colons) ─ */
    .info-block {
      margin: 18px 0;
    }

    .info-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12pt;
    }

    .info-table tr td {
      padding: 5px 0;
      vertical-align: top;
      border: none;
    }

    .info-table .info-label {
      width: 190px;
      font-weight: 600;
      color: #111827;
      white-space: nowrap;
    }

    .info-table .info-colon {
      width: 24px;
      text-align: center;
      font-weight: 600;
      color: #111827;
    }

    .info-table .info-val {
      font-weight: 600;
      color: #0f172a;
    }

    .info-table .info-val.ic-mono {
      font-family: 'Courier New', monospace;
      letter-spacing: 0.5px;
      font-weight: 700;
    }

    /* ── Certification Statement ───────────────────────────── */
    .cert-intro {
      font-size: 12pt;
      color: #111827;
      margin: 18px 0 10px;
    }

    .cert-intro strong {
      font-weight: 700;
      color: #0f172a;
    }

    .cert-statement {
      margin: 16px 0 20px;
      font-size: 12pt;
      line-height: 1.85;
      text-align: justify;
      color: #111827;
    }

    .cert-statement p {
      margin: 0;
    }

    .purpose-statement {
      margin: 20px 0 28px;
      font-size: 12pt;
      line-height: 1.7;
      color: #111827;
    }

    .purpose-statement p {
      margin: 0;
    }

    /* ── Doctor's Signature Section (Blank for Manual Signing) ─ */
    .signature-section {
      margin-top: 50px;
      display: flex;
      justify-content: flex-end;
      page-break-inside: avoid;
    }

    .signature-block {
      text-align: center;
      min-width: 280px;
      max-width: 320px;
    }

    .sig-manual-space {
      height: 70px; /* Completely blank for manual signing by pen */
    }

    .sig-line-text {
      font-family: 'Times New Roman', Times, serif;
      letter-spacing: -0.5px;
      color: #111827;
      line-height: 1;
      margin-bottom: 6px;
      user-select: none;
    }

    .signature-block .doc-name {
      font-family: 'Times New Roman', Times, serif;
      font-size: 11pt;
      font-weight: 700;
      color: #0f172a;
      margin-top: 6px;
    }

    .signature-block .doc-qual {
      font-family: 'Times New Roman', Times, serif;
      font-size: 9.5pt;
      color: #374151;
      margin-top: 2px;
      line-height: 1.4;
    }

    .signature-block .doc-mcr {
      font-size: 9.5pt;
      color: #334155;
      font-family: 'Courier New', monospace;
      font-weight: 600;
      margin-top: 2px;
    }

    .signature-block .doc-dept {
      font-family: 'Times New Roman', Times, serif;
      font-size: 9.5pt;
      color: #374151;
      margin-top: 2px;
    }

    /* ── Document Footer ───────────────────────────────────── */
    .doc-footer {
      text-align: center;
      font-size: 8.5pt;
      color: #64748b;
      margin-top: 48px;
      padding-top: 14px;
      border-top: 1px solid #e2e8f0;
      font-style: italic;
    }

    /* ── PRINT Overrides (B3 Friendly, Clean, No Action Bar) ─ */
    @media print {
      .no-print, .action-bar {
        display: none !important;
      }

      body.has-action-bar {
        padding-top: 0 !important;
        background: #ffffff !important;
      }

      body {
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 12pt;
      }

      .doc-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
        box-shadow: none !important;
      }

      .print-doc {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
      }

      .doc-header-meta {
        display: flex !important;
        justify-content: space-between !important;
      }

      .doc-watermark {
        opacity: 0.07 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .signature-section {
        display: flex !important;
        justify-content: flex-end !important;
        page-break-inside: avoid !important;
      }

      @page {
        size: B3 portrait;
        margin: 20mm 20mm 20mm 20mm;
      }
    }
  </style>
</head>
<body class="has-action-bar">

<!-- ── Action Bar (Screen only) ────────────────────────────── -->
<div class="action-bar no-print">
  <a href="<?= base_url('timeslip') ?>" class="btn btn-outline btn-sm">← Time Slip Records</a>
  <a href="<?= base_url('patients/select/' . $ts['patient_id']) ?>" class="btn btn-outline btn-sm">👤 Patient</a>

  <div class="spacer"></div>

  <!-- [ BM ] [ BI ] [ 🖨 Print Time Slip ] -->
  <div class="action-btn-group">
    <div class="lang-switch-pills" role="group" aria-label="Language selection">
      <a href="?lang=BM" class="lang-pill <?= $isBM ? 'active' : '' ?>" id="btn-lang-bm" title="Bahasa Malaysia">BM</a>
      <a href="?lang=BI" class="lang-pill <?= !$isBM ? 'active' : '' ?>" id="btn-lang-bi" title="English">BI</a>
    </div>

    <button onclick="window.print()" class="btn btn-primary btn-print-icon" id="btn-print-slip" title="<?= $isBM ? 'Cetak Slip Masa' : 'Print Time Slip' ?>" aria-label="Print">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="6 9 6 2 18 2 18 9"></polyline>
        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
        <rect x="6" y="14" width="12" height="8"></rect>
      </svg>
    </button>
  </div>
</div>

<!-- ── Main Document Wrapper ───────────────────────────────── -->
<div class="doc-wrapper">
  <div class="print-doc" id="ts-document">

    <!-- Center Watermark -->
    <div class="doc-watermark" aria-hidden="true">
      <img src="<?= base_url('assets/images/watermark.png') ?>" alt="Watermark">
    </div>

    <!-- Hospital Letterhead -->
    <div class="hospital-letterhead">
      <div class="header-logo-container">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo UniSZA" class="header-logo">
      </div>
      <div class="hospital-name">Hospital Sultan Zainal Abidin</div>
      <div class="hospital-address">
        Universiti Sultan Zainal Abidin, Kampus Gong Badak, 21300 Kuala Nerus, Terengganu<br>
        Tel: 09-665 5555 &nbsp;|&nbsp; Email: hosza@unisza.edu.my
      </div>
      <div class="letterhead-divider"></div>
    </div>

    <!-- Ref Row with QR Code Verification (Sama seperti MC) -->
    <div class="doc-ref-row" style="align-items: center;">
      <div>
        <div>
          <span id="label-ref-no" style="color:#888; font-size:9.5pt;"><?= $isBM ? 'No. Rujukan' : 'Reference No.' ?>: </span>
          <span class="ref-no"><?= esc($ts['reference_no']) ?></span>
        </div>
        <div style="margin-top: 4px;">
          <span id="label-date-issued" style="color:#888; font-size:9.5pt;"><?= $isBM ? 'Tarikh Keluar' : 'Date Issued' ?>: </span>
          <strong><?= date('d/m/Y', strtotime($ts['date_issued'])) ?></strong>
        </div>
      </div>
      <div style="display: flex; align-items: center; gap: 8px;">
        <div style="text-align: right; line-height: 1.2;">
          <span id="label-qr-verify" style="font-size: 7.5pt; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block;">
            <?= $isBM ? 'Imbas Pengesahan' : 'Scan to Verify' ?>
          </span>
          <span style="font-size: 7pt; color: #94a3b8;">
            hosza.unisza.edu.my
          </span>
        </div>
        <div class="qr-box" style="width: 52px; height: 52px; background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 2px; display: flex; align-items: center; justify-content: center;">
          <?= \App\Libraries\QRCodeGenerator::svg(base_url('verify/timeslip/' . rawurlencode($ts['reference_no'])), 2) ?>
        </div>
      </div>
    </div>

    <!-- Title: TIME SLIP (BI) / SLIP MASA (BM) -->
    <div class="doc-title-container">
      <h1 class="doc-title" id="doc-title-text"><?= $isBM ? 'SLIP MASA' : 'TIME SLIP' ?></h1>
    </div>

    <!-- Certification Intro -->
    <div class="cert-intro">
      <strong id="cert-intro-text"><?= $isBM ? 'Dengan ini disahkan bahawa :' : 'This is to certify that :' ?></strong>
    </div>

    <!-- Name and Identification No. -->
    <div class="info-block">
      <table class="info-table">
        <tr>
          <td class="info-label" id="label-name"><?= $isBM ? 'Nama' : 'Name' ?></td>
          <td class="info-colon">:</td>
          <td class="info-val"><?= esc($attendantName) ?></td>
        </tr>
        <tr>
          <td class="info-label" id="label-ic"><?= $isBM ? 'No. K/P' : 'Identification No.' ?></td>
          <td class="info-colon">:</td>
          <td class="info-val ic-mono"><?= esc($formatIC($attendantIC)) ?></td>
        </tr>
      </table>
    </div>

    <!-- Certification Statement -->
    <div class="cert-statement" id="cert-statement-text">
      <p><?= $isBM ? $certSentenceBM : $certSentenceBI ?></p>
    </div>

    <!-- Purpose Statement -->
    <div class="purpose-statement" id="purpose-statement-text">
      <p><?= $isBM ? $purposeSentenceBM : $purposeSentenceBI ?></p>
    </div>

    <!-- 1. Doctor's Signature Section (Blank for Manual Signing + Doctor Details) -->
    <div class="signature-section">
      <div class="signature-block">
        <div class="sig-manual-space"></div>
        <div class="sig-line-text">____________________________________</div>
        <div class="doc-name"><?= esc($ts['doctor_name']) ?></div>
        <?php if (!empty($ts['qualification'])): ?>
          <div class="doc-qual"><?= esc($ts['qualification']) ?></div>
        <?php endif; ?>
        <?php if (!empty($ts['mcr_number'])): ?>
          <?php
            $mcrVal = trim($ts['mcr_number']);
            $mcrFormatted = preg_match('/^mmc\s*no/i', $mcrVal) ? $mcrVal : 'MMC No: ' . $mcrVal;
          ?>
          <div class="doc-mcr"><?= esc($mcrFormatted) ?></div>
        <?php endif; ?>
        <?php if (!empty($ts['department'])): ?>
          <div class="doc-dept" id="doc-dept-text"><?= esc($isBM ? $deptDisplayBM : $deptDisplayBI) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Document Footer -->
    <div class="doc-footer" id="doc-footer-text">
      <?= $isBM
        ? 'Surat ini dijana secara komputer dan tidak sah untuk kegunaan rasmi. | Hospital Sultan Zainal Abidin (HoSZA)'
        : 'This letter is computer-generated and not valid for official use. | Hospital Sultan Zainal Abidin (HoSZA)' ?>
    </div>

  </div><!-- /.print-doc -->
</div><!-- /.doc-wrapper -->

<!-- ── Instant Client-Side Language Switcher ──────────────── -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  var docData = {
    BM: {
      windowTitle: <?= json_encode('SLIP MASA — ' . $ts['reference_no']) ?>,
      title: 'SLIP MASA',
      intro: 'Dengan ini disahkan bahawa :',
      refLabel: 'No. Rujukan: ',
      dateIssuedLabel: 'Tarikh Keluar: ',
      qrLabel: 'Imbas Pengesahan',
      nameLabel: 'Nama',
      icLabel: 'No. K/P',
      certSentence: <?= json_encode($certSentenceBM) ?>,
      purposeSentence: <?= json_encode($purposeSentenceBM) ?>,
      docDept: <?= json_encode($deptDisplayBM) ?>,
      footer: 'Surat ini dijana secara komputer dan tidak sah untuk kegunaan rasmi. | Hospital Sultan Zainal Abidin (HoSZA)',
      printTitle: 'Cetak Slip Masa'
    },
    BI: {
      windowTitle: <?= json_encode('TIME SLIP — ' . $ts['reference_no']) ?>,
      title: 'TIME SLIP',
      intro: 'This is to certify that :',
      refLabel: 'Reference No.: ',
      dateIssuedLabel: 'Date Issued: ',
      qrLabel: 'Scan to Verify',
      nameLabel: 'Name',
      icLabel: 'Identification No.',
      certSentence: <?= json_encode($certSentenceBI) ?>,
      purposeSentence: <?= json_encode($purposeSentenceBI) ?>,
      docDept: <?= json_encode($deptDisplayBI) ?>,
      footer: 'This letter is computer-generated and not valid for official use. | Hospital Sultan Zainal Abidin (HoSZA)',
      printTitle: 'Print Time Slip'
    }
  };

  function switchLanguage(lang) {
    if (!docData[lang]) return;
    var data = docData[lang];

    var winTitle = document.getElementById('doc-window-title');
    if (winTitle) winTitle.textContent = data.windowTitle;
    document.title = data.windowTitle;
    document.documentElement.lang = (lang === 'BM') ? 'ms' : 'en';

    var el;
    if (el = document.getElementById('doc-title-text')) el.textContent = data.title;
    if (el = document.getElementById('cert-intro-text')) el.textContent = data.intro;
    if (el = document.getElementById('label-ref-no')) el.textContent = data.refLabel;
    if (el = document.getElementById('label-date-issued')) el.textContent = data.dateIssuedLabel;
    if (el = document.getElementById('label-qr-verify')) el.textContent = data.qrLabel;
    if (el = document.getElementById('label-name')) el.textContent = data.nameLabel;
    if (el = document.getElementById('label-ic')) el.textContent = data.icLabel;
    if (el = document.getElementById('cert-statement-text')) el.innerHTML = '<p>' + data.certSentence + '</p>';
    if (el = document.getElementById('purpose-statement-text')) el.innerHTML = '<p>' + data.purposeSentence + '</p>';
    if (el = document.getElementById('doc-dept-text')) el.textContent = data.docDept;
    if (el = document.getElementById('doc-footer-text')) el.textContent = data.footer;
    if (el = document.getElementById('btn-print-slip')) el.setAttribute('title', data.printTitle);

    var bmBtn = document.getElementById('btn-lang-bm');
    var biBtn = document.getElementById('btn-lang-bi');
    if (bmBtn) bmBtn.classList.toggle('active', lang === 'BM');
    if (biBtn) biBtn.classList.toggle('active', lang === 'BI');

    if (window.history && window.history.pushState) {
      var newUrl = new URL(window.location.href);
      newUrl.searchParams.set('lang', lang);
      window.history.pushState({ lang: lang }, '', newUrl.toString());
    }
  }

  var bmBtn = document.getElementById('btn-lang-bm');
  var biBtn = document.getElementById('btn-lang-bi');

  if (bmBtn) {
    bmBtn.addEventListener('click', function(e) {
      e.preventDefault();
      switchLanguage('BM');
    });
  }

  if (biBtn) {
    biBtn.addEventListener('click', function(e) {
      e.preventDefault();
      switchLanguage('BI');
    });
  }

  window.addEventListener('popstate', function() {
    var params = new URLSearchParams(window.location.search);
    var lang = params.get('lang') || 'BI';
    switchLanguage(lang.toUpperCase() === 'BM' ? 'BM' : 'BI');
  });
});
</script>

</body>
</html>
