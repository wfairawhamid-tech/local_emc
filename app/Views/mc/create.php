<?php // mc/create.php — Issue Medical Certificate form ?>

<?php
  $age = date_diff(date_create($patient['dob']), date_create())->y;
?>

<div class="section-header">
  <div>
    <h2 class="section-title">Issue <span>Medical Certificate (MC)</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">Fill in the MC details for this patient</p>
  </div>
  <a href="<?= base_url('mc') ?>" class="btn btn-outline">← Back</a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    ⚠️ <?php foreach ((array)$errors as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Patient Info -->
<div class="patient-info-box" style="margin-bottom:24px;">
  <?php
    $initials = '';
    foreach (array_slice(explode(' ', $patient['namapesakit']), 0, 2) as $part) {
      if (!empty($part)) $initials .= strtoupper($part[0]);
    }
  ?>
  <div class="avatar"><?= $initials ?></div>
  <div class="info">
    <h3><?= esc($patient['namapesakit']) ?></h3>
    <p><?= esc($patient['mykad_mykid']) ?> &bull; 
    <?= esc($patient['dob']) ?>
     <?php if (!empty($patient['RN'])): ?> &bull; RN: <strong><?= esc($patient['RN']) ?></strong><?php endif; ?></p>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title"><span class="icon">📋</span> Medical Certificate Details</h3>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= base_url('mc/store') ?>" id="form-issue-mc">
      <?= csrf_field() ?>
      <input type="hidden" name="patient_id" value="<?= $patient['idpesakit'] ?>">
      <input type="hidden" name="patient_rn" value="<?= $patient['RN'] ?>">
      <input type="hidden" name="patient_name" value="<?= $patient['namapesakit'] ?>">
      <input type="hidden" name="patient_dob" value="<?= $patient['dob'] ?>">
      <input type="hidden" name="patient_mykad_mykid" value="<?= $patient['mykad_mykid'] ?>">

      <div class="form-row">
        <!-- Doctor -->
        <div class="form-group">
          <label class="form-label" for="doctor_id">Doctor <span class="required">*</span></label>
          <select name="doctor_id" id="doctor_id" class="form-control" required>
            <option value="">-- Select Doctor --</option>
            <?php foreach ($doctors as $doc): ?>
              <option value="<?= $doc['id'] ?>" <?= ($doc['id'] == ($currentDoctorId ?? 0)) ? 'selected' : '' ?>>
                <?= esc($doc['name']) ?> (<?= esc($doc['mcr_number']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Date Issued -->
        <div class="form-group">
          <label class="form-label" for="date_issued">Date Issued <span class="required">*</span></label>
          <input type="date" id="date_issued" name="date_issued" class="form-control"
                 value="<?= date('Y-m-d') ?>" required>
        </div>
      </div>

      <!-- Perakuan Perubatan (Klausa a - d) -->
      <div style="margin: 20px 0;">
        <label class="form-label" style="font-size: 13px; font-weight: 700; color: #1e3a8a; margin-bottom: 8px; display: block;">
          📋 Medical Certification (Please check applicable options):
        </label>

        <!-- Klausa a: Kemasukan Wad -->
        <div class="clause-card" id="card_ward" style="border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 12px; background: #f8fafc; transition: all 0.2s;">
          <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin: 0; color: #1e293b;">
            <input type="checkbox" name="has_ward" id="has_ward" value="1" style="width: 18px; height: 18px; cursor: pointer;">
            <span><strong style="color: #2563eb;">a)</strong> Has been admitted to ward</span>
          </label>
          <div id="ward_fields" style="display: none; margin-top: 12px; padding-top: 12px; border-top: 1px dashed #cbd5e1;">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="ward_start_date">Ward Admission Date <span class="required">*</span></label>
                <input type="date" id="ward_start_date" name="ward_start_date" class="form-control" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="ward_num_days">Number of Ward Days <span class="required">*</span></label>
                <input type="number" id="ward_num_days" name="ward_num_days" class="form-control" min="1" max="365" value="1">
                <p class="form-hint">Until: <strong id="ward_end_date_display">—</strong></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Klausa b: Cuti Sakit Biasa (Default: Checked) -->
        <div class="clause-card" id="card_unfit" style="border: 1.5px solid #93c5fd; border-radius: 8px; padding: 12px 16px; margin-bottom: 12px; background: #eff6ff; transition: all 0.2s;">
          <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin: 0; color: #1e293b;">
            <input type="checkbox" name="has_unfit" id="has_unfit" value="1" checked style="width: 18px; height: 18px; cursor: pointer;">
            <span><strong style="color: #2563eb;">b)</strong> Unfit for duty or schooling <span style="font-size: 11px; color: #2563eb; font-weight: 500;">(Standard Medical Leave)</span></span>
          </label>
          <div id="unfit_fields" style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #bfdbfe;">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="start_date">Leave Start Date <span class="required">*</span></label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="num_days">Number of Days <span class="required">*</span></label>
                <input type="number" id="num_days" name="num_days" class="form-control" min="1" max="365" value="1">
                <p class="form-hint">Until: <strong id="end_date_display">—</strong></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Klausa c: Rawatan Susulan -->
        <div class="clause-card" id="card_followup" style="border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 12px; background: #f8fafc; transition: all 0.2s;">
          <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin: 0; color: #1e293b;">
            <input type="checkbox" name="has_followup" id="has_followup" value="1" style="width: 18px; height: 18px; cursor: pointer;">
            <span><strong style="color: #2563eb;">c)</strong> Requires follow-up treatment (TCA / Appointment)</span>
          </label>
          <div id="followup_fields" style="display: none; margin-top: 12px; padding-top: 12px; border-top: 1px dashed #cbd5e1;">
            <div class="form-group">
              <label class="form-label" for="followup_date">Follow-up Treatment Date <span class="required">*</span></label>
              <input type="date" id="followup_date" name="followup_date" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
            </div>
          </div>
        </div>

        <!-- Klausa d: Tugas Ringan -->
        <div class="clause-card" id="card_light_duty" style="border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 12px; background: #f8fafc; transition: all 0.2s;">
          <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; margin: 0; color: #1e293b;">
            <input type="checkbox" name="has_light_duty" id="has_light_duty" value="1" style="width: 18px; height: 18px; cursor: pointer;">
            <span><strong style="color: #2563eb;">d)</strong> Fit for light duties only</span>
          </label>
          <div id="light_duty_fields" style="display: none; margin-top: 12px; padding-top: 12px; border-top: 1px dashed #cbd5e1;">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="light_duty_start_date">Light Duty Start Date <span class="required">*</span></label>
                <input type="date" id="light_duty_start_date" name="light_duty_start_date" class="form-control" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="light_duty_num_days">Number of Days <span class="required">*</span></label>
                <input type="number" id="light_duty_num_days" name="light_duty_num_days" class="form-control" min="1" max="365" value="1">
                <p class="form-hint">Until: <strong id="light_duty_end_date_display">—</strong></p>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Diagnosis -->
      <div class="form-group">
        <label class="form-label" for="diagnosis">Diagnosis / Reason for Illness</label>
        <input type="text" id="diagnosis" name="diagnosis" class="form-control"
               placeholder="e.g. Fever, Headache, Gastroenteritis...">
      </div>

      <!-- Notes -->
      <div class="form-group">
        <label class="form-label" for="notes">Additional Notes</label>
        <textarea id="notes" name="notes" class="form-control" rows="2"
                  placeholder="Doctor's additional instructions or remarks..."></textarea>
      </div>

      <div class="divider"></div>

      <div style="display:flex; gap:12px; justify-content:flex-end;">
        <a href="<?= base_url('patients/select/' . $patient['idpesakit']) ?>" class="btn btn-outline">✕ Cancel</a>
        <button type="submit" class="btn btn-primary btn-lg" id="btn-issue-mc-submit">
          📋 Issue Medical Certificate
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  /* ── Calculation Helper ──────────────────────── */
  function calcEndDate(startVal, daysVal) {
    const numDays = parseInt(daysVal) || 0;
    if (startVal && numDays > 0) {
      const startDate = new Date(startVal);
      const endDate   = new Date(startDate);
      endDate.setDate(startDate.getDate() + numDays - 1);
      const opts = { day: '2-digit', month: 'long', year: 'numeric' };
      return endDate.toLocaleDateString('en-GB', opts);
    }
    return '—';
  }

  /* ── Toggle Clause Handlers ─────────────────── */
  function setupClause(checkId, fieldsId, cardId, startId, daysId, displayId) {
    const checkEl   = document.getElementById(checkId);
    const fieldsEl  = document.getElementById(fieldsId);
    const cardEl    = document.getElementById(cardId);
    const startEl   = startId ? document.getElementById(startId) : null;
    const daysEl    = daysId ? document.getElementById(daysId) : null;
    const displayEl = displayId ? document.getElementById(displayId) : null;

    function refresh() {
      const active = checkEl.checked;
      fieldsEl.style.display = active ? 'block' : 'none';
      if (active) {
        cardEl.style.background = '#eff6ff';
        cardEl.style.borderColor = '#93c5fd';
        if (startEl) startEl.setAttribute('required', 'required');
        if (daysEl)  daysEl.setAttribute('required', 'required');
        if (startEl && daysEl && displayEl) {
          displayEl.textContent = calcEndDate(startEl.value, daysEl.value);
        }
      } else {
        cardEl.style.background = '#f8fafc';
        cardEl.style.borderColor = '#e2e8f0';
        if (startEl) startEl.removeAttribute('required');
        if (daysEl)  daysEl.removeAttribute('required');
      }
    }

    checkEl.addEventListener('change', refresh);
    if (startEl) startEl.addEventListener('change', () => {
      if (displayEl) displayEl.textContent = calcEndDate(startEl.value, daysEl.value);
    });
    if (daysEl) daysEl.addEventListener('input', () => {
      if (displayEl) displayEl.textContent = calcEndDate(startEl.value, daysEl.value);
    });

    refresh(); // init on load
  }

  setupClause('has_ward', 'ward_fields', 'card_ward', 'ward_start_date', 'ward_num_days', 'ward_end_date_display');
  setupClause('has_unfit', 'unfit_fields', 'card_unfit', 'start_date', 'num_days', 'end_date_display');
  setupClause('has_followup', 'followup_fields', 'card_followup', 'followup_date', null, null);
  setupClause('has_light_duty', 'light_duty_fields', 'card_light_duty', 'light_duty_start_date', 'light_duty_num_days', 'light_duty_end_date_display');

  /* ── Form Validation: Ensure at least one clause is selected ── */
  document.getElementById('form-issue-mc').addEventListener('submit', function(e) {
    const hasWard      = document.getElementById('has_ward').checked;
    const hasUnfit     = document.getElementById('has_unfit').checked;
    const hasFollowup  = document.getElementById('has_followup').checked;
    const hasLightDuty = document.getElementById('has_light_duty').checked;

    if (!hasWard && !hasUnfit && !hasFollowup && !hasLightDuty) {
      e.preventDefault();
      alert('Please check at least one certification clause (a, b, c, or d).');
    }
  });
</script>
