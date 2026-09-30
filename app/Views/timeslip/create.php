<?php // timeslip/create.php — Issue Time Slip form ?>

<?php
  $dob       = $patient['dob'] ?? $patient['date_of_birth'] ?? null;
  $age       = $dob ? date_diff(date_create($dob), date_create())->y : '—';
  $fullName  = $patient['namapesakit'] ?? $patient['full_name'] ?? '';
  $icNumber  = $patient['mykad_mykid'] ?? $patient['ic_number'] ?? '';
  $rnNumber  = $patient['RN'] ?? $patient['rn'] ?? '';
  $patientId = $patient['idpesakit'] ?? $patient['id'] ?? '';

  $genderRaw = $patient['gender'] ?? '';
  if (empty($genderRaw) && !empty($icNumber)) {
    $lastDigit = (int) substr(preg_replace('/\D/', '', $icNumber), -1);
    $gender = ($lastDigit % 2 !== 0) ? 'Male' : 'Female';
  } else {
    $gender = ($genderRaw === 'Lelaki' || $genderRaw === 'Male') ? 'Male' : 'Female';
  }
?>

<div class="section-header">
  <div>
    <h2 class="section-title">Issue <span>Time Slip</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">Record the patient's arrival and departure time</p>
  </div>
  <a href="<?= base_url('timeslip') ?>" class="btn btn-outline">← Back</a>
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
    foreach (array_slice(explode(' ', trim($fullName)), 0, 2) as $part) {
      if (!empty($part)) $initials .= strtoupper($part[0]);
    }
  ?>
  <div class="avatar"><?= $initials ?></div>
  <div class="info">
    <h3><?= esc($fullName) ?></h3>
    <p>
      <?= esc($icNumber) ?> &bull;
      <?= $gender ?> &bull;
      <?= $age ?> years old<?php if (!empty($rnNumber)): ?> &bull; RN: <strong><?= esc($rnNumber) ?></strong><?php endif; ?>
    </p>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title"><span class="icon">🕐</span> Time Slip Details</h3>
  </div>
  <div class="card-body">
    <style>
      .companion-box {
        background: linear-gradient(135deg, #f0f7ff 0%, #e8f4fd 100%);
        border: 1.5px solid #b3d9f5;
        border-radius: 10px;
        padding: 18px 20px;
        margin-top: 4px;
        animation: fadeSlideIn 0.25s ease;
      }
      .companion-box h4 {
        font-size: 14px; font-weight: 700; color: #1a6fa8;
        margin: 0 0 14px; display: flex; align-items: center; gap: 8px;
      }
      @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
      }
    </style>
    <form method="POST" action="<?= base_url('timeslip/store') ?>" id="form-issue-slip">
      <?= csrf_field() ?>
      <input type="hidden" name="patient_id" value="<?= esc($patientId) ?>">
      <input type="hidden" name="patient_rn" value="<?= esc($rnNumber) ?>">
      <input type="hidden" name="patient_name" value="<?= esc($fullName) ?>">
      <input type="hidden" name="patient_dob" value="<?= esc($dob) ?>">
      <input type="hidden" name="patient_mykad_mykid" value="<?= esc($icNumber) ?>">
      <input type="hidden" name="patient_gender" value="<?= esc($gender === 'Male' ? 'Lelaki' : 'Perempuan') ?>">

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
          <label class="form-label" for="date_issued">Treatment Date <span class="required">*</span></label>
          <input type="date" id="date_issued" name="date_issued" class="form-control"
                 value="<?= date('Y-m-d') ?>" required>
        </div>
      </div>

      <div class="form-row">
        <!-- Time In -->
        <div class="form-group">
          <label class="form-label" for="time_in">⏩ Arrival Time <span class="required">*</span></label>
          <input type="time" id="time_in" name="time_in" class="form-control"
                 value="<?= date('H:i') ?>" required>
          <p class="form-hint">Time patient arrived at the clinic</p>
        </div>

        <!-- Time Out -->
        <div class="form-group">
          <label class="form-label" for="time_out">⏪ Departure Time <span class="required">*</span></label>
          <input type="time" id="time_out" name="time_out" class="form-control"
                 value="<?= date('H:i', time() + 3600) ?>" required>
          <p class="form-hint">Duration: <strong id="duration_display">—</strong></p>
        </div>
      </div>

      <!-- Visit Type -->
      <div class="form-group">
        <label class="form-label" for="visit_type">Visit Type <span class="required">*</span></label>
        <select name="visit_type" id="visit_type" class="form-control" required>
          <option value="diri_sendiri">🧍 Self Treatment</option>
          <option value="anak">👶 Accompanying Child</option>
          <option value="suami_isteri">💑 Accompanying Spouse</option>
          <option value="ibu_bapa">👴 Accompanying Parent</option>
          <option value="lain">👥 Other</option>
        </select>
        <p class="form-hint">Select "Self Treatment" if the patient is attending for themselves.</p>
      </div>

      <!-- Companion Panel (conditional) -->
      <div id="companion-panel" style="display:none;">
        <div class="companion-box">
          <h4>👤 Companion / Next-of-Kin Information</h4>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="companion_name">Companion Name <span class="required">*</span></label>
              <input type="text" id="companion_name" name="companion_name"
                     class="form-control" placeholder="Full name of companion / next-of-kin"
                     autocomplete="off">
              <p class="form-hint">Name of the person accompanying the patient to the clinic.</p>
            </div>
            <div class="form-group">
              <label class="form-label" for="companion_ic">Companion IC No.</label>
              <input type="text" id="companion_ic" name="companion_ic"
                     class="form-control" placeholder="e.g. 850312-10-5671 (optional)"
                     autocomplete="off">
            </div>
          </div>
          <div class="form-group" id="relation-group" style="display:none;">
            <label class="form-label" for="companion_relation">Relationship to Patient <span class="required">*</span></label>
            <input type="text" id="companion_relation" name="companion_relation"
                   class="form-control" placeholder="e.g. Sibling, Colleague, Neighbour..."
                   autocomplete="off">
          </div>
        </div>
      </div>

      <!-- Purpose -->
      <div class="form-group">
        <label class="form-label" for="purpose">Purpose / Reason for Visit <span class="required">*</span></label>
        <input type="text" id="purpose" name="purpose" class="form-control"
               placeholder="e.g. Medical treatment, Fever, Headache..." required>
        <p class="form-hint">State the reason or purpose of the treatment obtained.</p>
      </div>

      <!-- Notes -->
      <div class="form-group">
        <label class="form-label" for="notes">Additional Notes</label>
        <textarea id="notes" name="notes" class="form-control" rows="2"
                  placeholder="Additional remarks (if any)..."></textarea>
      </div>

      <div class="divider"></div>

      <div style="display:flex; gap:12px; justify-content:flex-end;">
        <a href="<?= base_url('timeslip') ?>" class="btn btn-outline">✕ Cancel</a>
        <button type="submit" class="btn btn-success btn-lg" id="btn-issue-slip-submit">
          🕐 Issue Time Slip
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  /* ── Duration Calculator ─────────────────────── */
  function updateDuration() {
    const timeIn  = document.getElementById('time_in').value;
    const timeOut = document.getElementById('time_out').value;
    if (timeIn && timeOut) {
      const [h1, m1] = timeIn.split(':').map(Number);
      const [h2, m2] = timeOut.split(':').map(Number);
      const mins = (h2 * 60 + m2) - (h1 * 60 + m1);
      if (mins > 0) {
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        document.getElementById('duration_display').textContent =
          (h > 0 ? h + ' hr ' : '') + (m > 0 ? m + ' min' : '');
      } else {
        document.getElementById('duration_display').textContent = '—';
      }
    }
  }

  document.getElementById('time_in').addEventListener('change', updateDuration);
  document.getElementById('time_out').addEventListener('change', updateDuration);
  updateDuration();

  /* ── Companion Panel Toggle ──────────────────── */
  const visitTypeEl     = document.getElementById('visit_type');
  const companionPanel  = document.getElementById('companion-panel');
  const relationGroup   = document.getElementById('relation-group');
  const companionNameEl = document.getElementById('companion_name');
  const companionRelEl  = document.getElementById('companion_relation');
  const companionIcEl   = document.getElementById('companion_ic');

  function toggleCompanionPanel() {
    const val          = visitTypeEl.value;
    const hasCompanion = val !== 'diri_sendiri';
    const isOther      = val === 'lain';

    // Show / hide companion panel
    companionPanel.style.display = hasCompanion ? 'block' : 'none';

    if (hasCompanion) {
      companionNameEl.setAttribute('required', 'required');
    } else {
      companionNameEl.removeAttribute('required');
      companionNameEl.value = '';
      companionIcEl.value   = '';
      if (companionRelEl) companionRelEl.value = '';
    }

    // Show relationship field only for 'Other'
    relationGroup.style.display = isOther ? 'block' : 'none';
    if (companionRelEl) {
      if (isOther) {
        companionRelEl.setAttribute('required', 'required');
      } else {
        companionRelEl.removeAttribute('required');
        companionRelEl.value = '';
      }
    }
  }

  visitTypeEl.addEventListener('change', toggleCompanionPanel);
  toggleCompanionPanel(); // init on page load
</script>
