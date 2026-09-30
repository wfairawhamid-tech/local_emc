<?php // timeslip/edit.php — Edit Time Slip ?>
<?php
  $patientName = $ts['full_name']     ?? '';
  $patientIc   = $ts['ic_number']     ?? '';
  $patientRn   = $ts['rn']            ?? '';
  $patientDob  = $ts['date_of_birth'] ?? '';

  $initials = '';
  foreach (array_slice(explode(' ', trim($patientName)), 0, 2) as $part) {
    if (!empty($part)) $initials .= strtoupper($part[0]);
  }

  $visitType = $ts['visit_type'] ?? 'diri_sendiri';
?>

<div class="section-header">
  <div>
    <h2 class="section-title">Edit <span>Time Slip</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">
      Ref: <strong><?= esc($ts['reference_no']) ?></strong>
    </p>
  </div>
  <a href="<?= base_url('timeslip/view/' . $ts['id']) ?>" class="btn btn-outline">← Back to View</a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    ⚠️ <?php foreach ((array)$errors as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Patient Info — read only -->
<div class="patient-info-box" style="margin-bottom:24px; position:relative;">
  <div class="avatar"><?= $initials ?></div>
  <div class="info">
    <h3><?= esc($patientName) ?></h3>
    <p>
      <?= esc($patientIc) ?>
      <?php if (!empty($patientDob)): ?> &bull; <?= date('d/m/Y', strtotime($patientDob)) ?><?php endif; ?>
      <?php if (!empty($patientRn)): ?> &bull; RN: <strong><?= esc($patientRn) ?></strong><?php endif; ?>
    </p>
  </div>
  <span style="position:absolute;top:12px;right:14px;background:rgba(234,179,8,.12);border:1px solid rgba(234,179,8,.3);color:#92400e;font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;letter-spacing:.3px;">
    ✏️ EDIT MODE
  </span>
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title"><span class="icon">🕐</span> Time Slip Details</h3>
  </div>
  <div class="card-body">
    <style>
      .companion-box {
        background: linear-gradient(135deg,#f0f7ff 0%,#e8f4fd 100%);
        border: 1.5px solid #b3d9f5; border-radius: 10px;
        padding: 18px 20px; margin-top: 4px;
      }
      .companion-box h4 { font-size:14px; font-weight:700; color:#1a6fa8; margin:0 0 14px; display:flex; align-items:center; gap:8px; }
    </style>

    <form method="POST" action="<?= base_url('timeslip/update/' . $ts['id']) ?>" id="form-edit-slip">
      <?= csrf_field() ?>

      <div class="form-row">
        <!-- Doctor -->
        <div class="form-group">
          <label class="form-label" for="doctor_id">Doctor <span class="required">*</span></label>
          <select name="doctor_id" id="doctor_id" class="form-control" required>
            <option value="">-- Select Doctor --</option>
            <?php foreach ($doctors as $doc): ?>
              <option value="<?= $doc['id'] ?>" <?= ($doc['id'] == $ts['doctor_id']) ? 'selected' : '' ?>>
                <?= esc($doc['name']) ?> (<?= esc($doc['mcr_number']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Treatment Date -->
        <div class="form-group">
          <label class="form-label" for="date_issued">Treatment Date <span class="required">*</span></label>
          <input type="date" id="date_issued" name="date_issued" class="form-control"
                 value="<?= esc($ts['date_issued']) ?>" required>
        </div>
      </div>

      <div class="form-row">
        <!-- Time In -->
        <div class="form-group">
          <label class="form-label" for="time_in">⏩ Arrival Time <span class="required">*</span></label>
          <input type="time" id="time_in" name="time_in" class="form-control"
                 value="<?= esc(substr($ts['time_in'], 0, 5)) ?>" required>
          <p class="form-hint">Time patient arrived at the clinic</p>
        </div>

        <!-- Time Out -->
        <div class="form-group">
          <label class="form-label" for="time_out">⏪ Departure Time <span class="required">*</span></label>
          <input type="time" id="time_out" name="time_out" class="form-control"
                 value="<?= esc(substr($ts['time_out'], 0, 5)) ?>" required>
          <p class="form-hint">Duration: <strong id="duration_display">—</strong></p>
        </div>
      </div>

      <!-- Visit Type -->
      <div class="form-group">
        <label class="form-label" for="visit_type">Visit Type <span class="required">*</span></label>
        <select name="visit_type" id="visit_type" class="form-control" required>
          <option value="diri_sendiri" <?= $visitType === 'diri_sendiri' ? 'selected' : '' ?>>🧍 Self Treatment</option>
          <option value="anak"         <?= $visitType === 'anak'         ? 'selected' : '' ?>>👶 Accompanying Child</option>
          <option value="suami_isteri" <?= $visitType === 'suami_isteri' ? 'selected' : '' ?>>💑 Accompanying Spouse</option>
          <option value="ibu_bapa"     <?= $visitType === 'ibu_bapa'     ? 'selected' : '' ?>>👴 Accompanying Parent</option>
          <option value="lain"         <?= $visitType === 'lain'         ? 'selected' : '' ?>>👥 Other</option>
        </select>
        <p class="form-hint">Select "Self Treatment" if the patient is attending for themselves.</p>
      </div>

      <!-- Companion Panel (conditional) -->
      <div id="companion-panel" style="<?= $visitType !== 'diri_sendiri' ? '' : 'display:none;' ?>">
        <div class="companion-box">
          <h4>👤 Companion / Next-of-Kin Information</h4>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="companion_name">Companion Name <span class="required">*</span></label>
              <input type="text" id="companion_name" name="companion_name" class="form-control"
                     placeholder="Full name of companion / next-of-kin"
                     value="<?= esc($ts['companion_name'] ?? '') ?>" autocomplete="off">
            </div>
            <div class="form-group">
              <label class="form-label" for="companion_ic">Companion IC No.</label>
              <input type="text" id="companion_ic" name="companion_ic" class="form-control"
                     placeholder="e.g. 850312-10-5671 (optional)"
                     value="<?= esc($ts['companion_ic'] ?? '') ?>" autocomplete="off">
            </div>
          </div>
          <div class="form-group" id="relation-group" style="<?= $visitType === 'lain' ? '' : 'display:none;' ?>">
            <label class="form-label" for="companion_relation">Relationship to Patient <span class="required">*</span></label>
            <input type="text" id="companion_relation" name="companion_relation" class="form-control"
                   placeholder="e.g. Sibling, Colleague, Neighbour..."
                   value="<?= esc($ts['companion_relation'] ?? '') ?>" autocomplete="off">
          </div>
        </div>
      </div>

      <!-- Purpose -->
      <div class="form-group">
        <label class="form-label" for="purpose">Purpose / Reason for Visit <span class="required">*</span></label>
        <input type="text" id="purpose" name="purpose" class="form-control"
               placeholder="e.g. Medical treatment, Fever, Headache..."
               value="<?= esc($ts['purpose'] ?? '') ?>" required>
      </div>

      <!-- Notes -->
      <div class="form-group">
        <label class="form-label" for="notes">Additional Notes</label>
        <textarea id="notes" name="notes" class="form-control" rows="2"
                  placeholder="Additional remarks (if any)..."><?= esc($ts['notes'] ?? '') ?></textarea>
      </div>

      <div class="divider"></div>

      <div style="display:flex; gap:12px; justify-content:flex-end;">
        <a href="<?= base_url('timeslip/view/' . $ts['id']) ?>" class="btn btn-outline">✕ Cancel</a>
        <button type="submit" class="btn btn-success btn-lg" id="btn-save-slip-edit">
          💾 Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<script>
/* ── Duration Calculator ─────────────────────── */
function updateDuration() {
  const ti = document.getElementById('time_in').value;
  const to = document.getElementById('time_out').value;
  if (ti && to) {
    const [h1,m1] = ti.split(':').map(Number);
    const [h2,m2] = to.split(':').map(Number);
    const mins = (h2*60+m2) - (h1*60+m1);
    const el = document.getElementById('duration_display');
    if (mins > 0) {
      const h = Math.floor(mins/60), m = mins%60;
      el.textContent = (h>0?h+' hr ':'') + (m>0?m+' min':'');
    } else { el.textContent = '—'; }
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
  const val = visitTypeEl.value;
  const hasCompanion = val !== 'diri_sendiri';
  const isOther      = val === 'lain';

  companionPanel.style.display = hasCompanion ? 'block' : 'none';
  if (hasCompanion) {
    companionNameEl.setAttribute('required','required');
  } else {
    companionNameEl.removeAttribute('required');
    companionNameEl.value = ''; companionIcEl.value = '';
    if (companionRelEl) companionRelEl.value = '';
  }
  relationGroup.style.display = isOther ? 'block' : 'none';
  if (companionRelEl) {
    if (isOther) { companionRelEl.setAttribute('required','required'); }
    else { companionRelEl.removeAttribute('required'); companionRelEl.value = ''; }
  }
}
visitTypeEl.addEventListener('change', toggleCompanionPanel);
toggleCompanionPanel();
</script>
