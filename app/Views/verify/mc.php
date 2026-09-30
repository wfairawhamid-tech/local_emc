<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Pengesahan Sijil Cuti Sakit') ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <style>
    body {
      background: #f1f5f9;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      margin: 0;
      padding: 20px 16px 40px;
      display: flex;
      justify-content: center;
      min-height: 100vh;
    }
    .verify-card {
      width: 100%;
      max-width: 520px;
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      overflow: hidden;
      border: 1px solid #e2e8f0;
    }
    .verify-header {
      background: #ffffff;
      padding: 24px 20px 18px;
      text-align: center;
      border-bottom: 1px solid #f1f5f9;
    }
    .verify-logo {
      height: 52px;
      max-width: 180px;
      object-fit: contain;
      margin-bottom: 8px;
    }
    .verify-hospital {
      font-size: 14px;
      font-weight: 700;
      color: #1e3a8a;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .verify-sub {
      font-size: 11.5px;
      color: #64748b;
      margin-top: 2px;
    }
    .status-banner {
      padding: 16px 20px;
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .status-valid {
      background: #ecfdf5;
      border-top: 3px solid #10b981;
      border-bottom: 1px solid #d1fae5;
    }
    .status-invalid {
      background: #fef2f2;
      border-top: 3px solid #ef4444;
      border-bottom: 1px solid #fee2e2;
    }
    .status-icon {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      flex-shrink: 0;
    }
    .status-valid .status-icon {
      background: #10b981;
      color: #ffffff;
    }
    .status-invalid .status-icon {
      background: #ef4444;
      color: #ffffff;
    }
    .status-text h2 {
      margin: 0 0 3px 0;
      font-size: 15px;
      font-weight: 700;
    }
    .status-valid .status-text h2 { color: #065f46; }
    .status-invalid .status-text h2 { color: #991b1b; }
    .status-text p {
      margin: 0;
      font-size: 12px;
    }
    .status-valid .status-text p { color: #047857; }
    .status-invalid .status-text p { color: #b91c1c; }

    .verify-body {
      padding: 24px 22px;
    }
    .details-group {
      margin-bottom: 20px;
    }
    .detail-row {
      display: flex;
      justify-content: space-between;
      padding: 10px 0;
      border-bottom: 1px dashed #e2e8f0;
      font-size: 13.5px;
    }
    .detail-row:last-child {
      border-bottom: none;
    }
    .detail-label {
      color: #64748b;
      font-weight: 500;
    }
    .detail-val {
      font-weight: 600;
      color: #0f172a;
      text-align: right;
    }
    .clause-highlight-box {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      border-radius: 10px;
      padding: 14px 16px;
      margin-top: 16px;
      font-size: 13px;
      color: #1e3a8a;
      line-height: 1.5;
    }
    .verify-footer {
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      padding: 16px 20px;
      font-size: 11.5px;
      color: #64748b;
      text-align: center;
      line-height: 1.5;
    }
  </style>
</head>
<body>

<div class="verify-card">
  <!-- Header Rasmi -->
  <div class="verify-header">
    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo HoSZA" class="verify-logo">
    <div class="verify-hospital">Hospital Sultan Zainal Abidin</div>
    <div class="verify-sub">Official Medical Certificate Verification Portal &bull; eMC Verification</div>
  </div>

  <?php if ($isValid && !empty($mc)): ?>
    <!-- Status VALID -->
    <div class="status-banner status-valid">
      <div class="status-icon">✔</div>
      <div class="status-text">
        <h2>THIS DOCUMENT IS AUTHENTIC &amp; VALID</h2>
        <p>Officially verified and issued by Hospital Sultan Zainal Abidin.</p>
      </div>
    </div>

    <div class="verify-body">
      <div class="details-group">
        <div class="detail-row">
          <span class="detail-label">Document Type:</span>
          <span class="detail-val">Medical Certificate (MC)</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Official Reference No.:</span>
          <span class="detail-val" style="color:#2563eb; font-weight:700;"><?= esc($mc['reference_no']) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Date Issued:</span>
          <span class="detail-val"><?= date('d/m/Y', strtotime($mc['date_issued'])) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Patient Name:</span>
          <span class="detail-val"><?= esc($mc['full_name']) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Identity Card No.:</span>
          <span class="detail-val">
            <?php
              $rawIc = preg_replace('/[^0-9]/', '', $mc['ic_number'] ?? '');
              if (strlen($rawIc) === 12) {
                echo substr($rawIc, 0, 6) . '-' . substr($rawIc, 6, 2) . '-****';
              } else {
                echo esc($mc['ic_number'] ?: '—');
              }
            ?>
          </span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Medical Officer:</span>
          <span class="detail-val"><?= esc($mc['doctor_name']) ?></span>
        </div>
        <?php if (!empty($mc['mcr_number'])): ?>
        <div class="detail-row">
          <span class="detail-label">Doctor MMC No.:</span>
          <span class="detail-val"><?= esc($mc['mcr_number']) ?></span>
        </div>
        <?php endif; ?>
      </div>

      <!-- Medical Recommendation Summary -->
      <div class="clause-highlight-box">
        <strong>📋 Medical Recommendation Summary:</strong><br>
        <?php if (!empty($mc['has_unfit']) && !empty($mc['num_days'])): ?>
          &bull; Granted medical leave for <strong><?= $mc['num_days'] ?> day<?= $mc['num_days'] > 1 ? 's' : '' ?></strong> (<?= date('d/m/Y', strtotime($mc['start_date'])) ?> to <?= date('d/m/Y', strtotime($mc['end_date'])) ?>).<br>
        <?php endif; ?>
        <?php if (!empty($mc['has_ward']) && !empty($mc['ward_num_days'])): ?>
          &bull; Admitted to ward for <strong><?= $mc['ward_num_days'] ?> day<?= $mc['ward_num_days'] > 1 ? 's' : '' ?></strong> (<?= date('d/m/Y', strtotime($mc['ward_start_date'])) ?> to <?= date('d/m/Y', strtotime($mc['ward_end_date'])) ?>).<br>
        <?php endif; ?>
        <?php if (!empty($mc['has_light_duty'])): ?>
          &bull; Fit for light duties only for <strong><?= $mc['light_duty_num_days'] ?> day<?= $mc['light_duty_num_days'] > 1 ? 's' : '' ?></strong>.<br>
        <?php endif; ?>
        <?php if (!empty($mc['has_followup'])): ?>
          &bull; Follow-up treatment on: <strong><?= date('d/m/Y', strtotime($mc['followup_date'])) ?></strong>.<br>
        <?php endif; ?>
      </div>
    </div>

  <?php else: ?>
    <!-- Status INVALID -->
    <div class="status-banner status-invalid">
      <div class="status-icon">✖</div>
      <div class="status-text">
        <h2>RECORD NOT FOUND / INVALID</h2>
        <p>Certificate with reference <strong><?= esc($ref) ?></strong> was not found in official records.</p>
      </div>
    </div>
    <div class="verify-body" style="text-align:center; padding:32px 20px;">
      <p style="font-size:13.5px; color:#64748b; line-height:1.5;">
        Please ensure that the scanned QR code originates from an authentic document issued by Hospital Sultan Zainal Abidin. If you have any inquiries, please contact the hospital medical records department.
      </p>
    </div>
  <?php endif; ?>

  <div class="verify-footer">
    Hospital Sultan Zainal Abidin (HoSZA)<br>
    Jalan Gong Badak, 21300 Kuala Nerus, Terengganu &bull; Tel: 09-665 5555<br>
    <em>This verification is electronically generated and authentic.</em>
  </div>
</div>

</body>
</html>
