<?php // patients/choose_doc.php — Pilih jenis dokumen untuk pesakit dari iPesakit ?>

<?php
  // Sokong kedua-dua format data: terus dari API (IntegrationController) atau dari PatientController::select()
  $patientId   = $patient['idpesakit'] ?? $patient['id']        ?? '';
  $patientName = $patient['name']      ?? $patient['full_name'] ?? '';
  $patientIc   = $patient['mykad_mykid'] ?? $patient['ic_number'] ?? '';
  $patientRn   = $patient['rn']        ?? $patient['RN']        ?? '';
  $patientDob  = $patient['dob']       ?? $patient['date_of_birth'] ?? null;

  // Kira umur
  $age = $patientDob ? date_diff(date_create($patientDob), date_create())->y : '—';

  // Inisial avatar
  $initials = '';
  foreach (array_slice(explode(' ', trim($patientName)), 0, 2) as $part) {
    if (!empty($part)) $initials .= strtoupper($part[0]);
  }

  // Jantina dari IC jika tiada dari API
  $gender = $patient['gender'] ?? '';
  if (empty($gender) && !empty($patientIc)) {
    $lastDigit = (int) substr(preg_replace('/\D/', '', $patientIc), -1);
    $gender = ($lastDigit % 2 !== 0) ? 'Lelaki' : 'Perempuan';
  }
  $genderDisplay = ($gender === 'Lelaki' || $gender === 'Male') ? 'Male' : 'Female';
?>

<div style="max-width: 680px; margin: 0 auto;">

  <!-- ── Maklumat Pesakit ──────────────────────────────────────── -->
  <div class="patient-info-box" style="margin-bottom: 28px;">
    <div class="avatar"><?= $initials ?></div>
    <div class="info">
      <h3><?= esc($patientName) ?></h3>
      <p>
        <?php if (!empty($patientIc)): ?>
          <?= esc($patientIc) ?> &bull;
        <?php endif; ?>
        <?= $genderDisplay ?> &bull;
        <?= $age ?> years old
        <?php if ($patientDob): ?>
          &bull; <?= date('d/m/Y', strtotime($patientDob)) ?>
        <?php endif; ?>
        <?php if (!empty($patientRn)): ?>
          &bull; RN: <strong><?= esc($patientRn) ?></strong>
        <?php endif; ?>
      </p>
    </div>
  </div>

  <!-- ── Tajuk Bahagian ────────────────────────────────────────── -->
  <div class="section-header" style="margin-bottom: 8px;">
    <h2 class="section-title">Select <span>Document Type</span></h2>
    <!-- Tiada butang "Change Patient" — pesakit ditentukan oleh iPesakit -->
    <div style="display:flex; align-items:center; gap:8px; background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.2); border-radius:8px; padding:6px 12px;">
      <span style="font-size:13px;">🔗</span>
      <span style="font-size:11.5px; color:#4ade80;">Sesi iPesakit Aktif</span>
    </div>
  </div>

  <p style="font-size:13px; color:var(--text-secondary); margin-bottom:28px;">
    Please select the document type to issue for this patient.
  </p>

  <!-- ── Pilihan Dokumen ───────────────────────────────────────── -->
  <div class="doc-choice-grid">

    <!-- Medical Certificate -->
    <a href="<?= base_url('mc/create/' . $patientId) ?>" class="doc-choice-card mc" id="btn-issue-mc">
      <div class="doc-choice-icon">📋</div>
      <h3>Medical Certificate (MC)</h3>
      <p>Official medical leave certificate with reference number and doctor's signature</p>
      <span class="btn btn-primary" style="margin-top:8px; pointer-events:none;">Issue MC →</span>
    </a>

    <!-- Time Slip -->
    <a href="<?= base_url('timeslip/create/' . $patientId) ?>" class="doc-choice-card ts" id="btn-issue-slip">
      <div class="doc-choice-icon">🕐</div>
      <h3>Time Slip</h3>
      <p>Arrival and departure time slip for clinic treatment attendance</p>
      <span class="btn btn-success" style="margin-top:8px; pointer-events:none;">Issue Time Slip →</span>
    </a>

  </div>

</div>
