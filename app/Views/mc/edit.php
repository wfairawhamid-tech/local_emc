<?php // mc/edit.php — Edit Medical Certificate ?>
<?php
  $patientName = $mc['full_name']      ?? '';
  $patientIc   = $mc['ic_number']      ?? '';
  $patientRn   = $mc['rn']             ?? '';
  $patientDob  = $mc['date_of_birth']  ?? '';

  $initials = '';
  foreach (array_slice(explode(' ', trim($patientName)), 0, 2) as $part) {
    if (!empty($part)) $initials .= strtoupper($part[0]);
  }
?>

<div class="section-header">
  <div>
    <h2 class="section-title">Edit <span>Medical Certificate</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">
      Ref: <strong><?= esc($mc['reference_no']) ?></strong>
    </p>
  </div>
  <a href="<?= base_url('mc/view/' . $mc['id']) ?>" class="btn btn-outline">← Back to View</a>
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
    <h3 class="card-title"><span class="icon">📋</span> Medical Certificate Details</h3>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= base_url('mc/update/' . $mc['id']) ?>" id="form-edit-mc">
      <?= csrf_field() ?>

      <div class="form-row">
        <!-- Doctor -->
        <div class="form-group">
          <label class="form-label" for="doctor_id">Doctor <span class="required">*</span></label>
          <select name="doctor_id" id="doctor_id" class="form-control" required>
            <option value="">-- Select Doctor --</option>
            <?php foreach ($doctors as $doc): ?>
              <option value="<?= $doc['id'] ?>" <?= ($doc['id'] == $mc['doctor_id']) ? 'selected' : '' ?>>
                <?= esc($doc['name']) ?> (<?= esc($doc['mcr_number']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Date Issued -->
        <div class="form-group">
          <label class="form-label" for="date_issued">Date Issued <span class="required">*</span></label>
          <input type="date" id="date_issued" name="date_issued" class="form-control"
                 value="<?= esc($mc['date_issued']) ?>" required>
        </div>
      </div>

      <!-- Clauses a–d -->
      <div style="margin:20px 0;">
        <label class="form-label" style="font-size:13px;font-weight:700;color:#1e3a8a;margin-bottom:8px;display:block;">
          📋 Medical Certification (Please check applicable options):
        </label>

        <!-- a) Ward -->
        <div class="clause-card" id="card_ward" style="border:1.5px solid #e2e8f0;border-radius:8px;padding:12px 16px;margin-bottom:12px;background:#f8fafc;transition:all .2s;">
          <label style="display:flex;align-items:center;gap:10px;font-weight:600;cursor:pointer;margin:0;color:#1e293b;">
            <input type="checkbox" name="has_ward" id="has_ward" value="1"
                   <?= $mc['has_ward'] ? 'checked' : '' ?> style="width:18px;height:18px;cursor:pointer;">
            <span><strong style="color:#2563eb;">a)</strong> Has been admitted to ward</span>
          </label>
          <div id="ward_fields" style="<?= $mc['has_ward'] ? '' : 'display:none;' ?>margin-top:12px;padding-top:12px;border-top:1px dashed #cbd5e1;">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="ward_start_date">Ward Admission Date <span class="required">*</span></label>
                <input type="date" id="ward_start_date" name="ward_start_date" class="form-control"
                       value="<?= esc($mc['ward_start_date'] ?? date('Y-m-d')) ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="ward_num_days">Number of Ward Days <span class="required">*</span></label>
                <input type="number" id="ward_num_days" name="ward_num_days" class="form-control"
                       min="1" max="365" value="<?= (int)($mc['ward_num_days'] ?? 1) ?>">
                <p class="form-hint">Until: <strong id="ward_end_date_display">—</strong></p>
              </div>
            </div>
          </div>
        </div>

        <!-- b) Unfit -->
        <div class="clause-card" id="card_unfit" style="border:1.5px solid #93c5fd;border-radius:8px;padding:12px 16px;margin-bottom:12px;background:#eff6ff;transition:all .2s;">
          <label style="display:flex;align-items:center;gap:10px;font-weight:600;cursor:pointer;margin:0;color:#1e293b;">
            <input type="checkbox" name="has_unfit" id="has_unfit" value="1"
                   <?= $mc['has_unfit'] ? 'checked' : '' ?> style="width:18px;height:18px;cursor:pointer;">
            <span><strong style="color:#2563eb;">b)</strong> Unfit for duty or schooling <span style="font-size:11px;color:#2563eb;font-weight:500;">(Standard Medical Leave)</span></span>
          </label>
          <div id="unfit_fields" style="<?= $mc['has_unfit'] ? '' : 'display:none;' ?>margin-top:12px;padding-top:12px;border-top:1px dashed #bfdbfe;">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="start_date">Leave Start Date <span class="required">*</span></label>
                <input type="date" id="start_date" name="start_date" class="form-control"
                       value="<?= esc($mc['start_date'] ?? date('Y-m-d')) ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="num_days">Number of Days <span class="required">*</span></label>
                <input type="number" id="num_days" name="num_days" class="form-control"
                       min="1" max="365" value="<?= (int)($mc['num_days'] ?? 1) ?>">
                <p class="form-hint">Until: <strong id="end_date_display">—</strong></p>
              </div>
            </div>
          </div>
        </div>

        <!-- c) Follow-up -->
        <div class="clause-card" id="card_followup" style="border:1.5px solid #e2e8f0;border-radius:8px;padding:12px 16px;margin-bottom:12px;background:#f8fafc;transition:all .2s;">
          <label style="display:flex;align-items:center;gap:10px;font-weight:600;cursor:pointer;margin:0;color:#1e293b;">
            <input type="checkbox" name="has_followup" id="has_followup" value="1"
                   <?= $mc['has_followup'] ? 'checked' : '' ?> style="width:18px;height:18px;cursor:pointer;">
            <span><strong style="color:#2563eb;">c)</strong> Requires follow-up treatment (TCA / Appointment)</span>
          </label>
          <div id="followup_fields" style="<?= $mc['has_followup'] ? '' : 'display:none;' ?>margin-top:12px;padding-top:12px;border-top:1px dashed #cbd5e1;">
            <div class="form-group">
              <label class="form-label" for="followup_date">Follow-up Treatment Date <span class="required">*</span></label>
              <input type="date" id="followup_date" name="followup_date" class="form-control"
                     value="<?= esc($mc['followup_date'] ?? date('Y-m-d', strtotime('+7 days'))) ?>">
            </div>
          </div>
        </div>

        <!-- d) Light Duty -->
        <div class="clause-card" id="card_light_duty" style="border:1.5px solid #e2e8f0;border-radius:8px;padding:12px 16px;margin-bottom:12px;background:#f8fafc;transition:all .2s;">
          <label style="display:flex;align-items:center;gap:10px;font-weight:600;cursor:pointer;margin:0;color:#1e293b;">
            <input type="checkbox" name="has_light_duty" id="has_light_duty" value="1"
                   <?= $mc['has_light_duty'] ? 'checked' : '' ?> style="width:18px;height:18px;cursor:pointer;">
            <span><strong style="color:#2563eb;">d)</strong> Fit for light duties only</span>
          </label>
          <div id="light_duty_fields" style="<?= $mc['has_light_duty'] ? '' : 'display:none;' ?>margin-top:12px;padding-top:12px;border-top:1px dashed #cbd5e1;">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="light_duty_start_date">Light Duty Start Date <span class="required">*</span></label>
                <input type="date" id="light_duty_start_date" name="light_duty_start_date" class="form-control"
                       value="<?= esc($mc['light_duty_start_date'] ?? date('Y-m-d')) ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="light_duty_num_days">Number of Days <span class="required">*</span></label>
                <input type="number" id="light_duty_num_days" name="light_duty_num_days" class="form-control"
                       min="1" max="365" value="<?= (int)($mc['light_duty_num_days'] ?? 1) ?>">
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
               placeholder="e.g. Fever, Headache, Gastroenteritis..."
               value="<?= esc($mc['diagnosis'] ?? '') ?>">
      </div>

      <!-- Notes -->
      <div class="form-group">
        <label class="form-label" for="notes">Additional Notes</label>
        <textarea id="notes" name="notes" class="form-control" rows="2"
                  placeholder="Doctor's additional instructions or remarks..."><?= esc($mc['notes'] ?? '') ?></textarea>
      </div>

      <div class="divider"></div>

      <div style="display:flex;gap:12px;justify-content:flex-end;">
        <a href="<?= base_url('mc/view/' . $mc['id']) ?>" class="btn btn-outline">✕ Cancel</a>
        <button type="submit" class="btn btn-primary btn-lg" id="btn-save-mc-edit">
          💾 Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function calcEndDate(startVal, daysVal) {
  const n = parseInt(daysVal) || 0;
  if (startVal && n > 0) {
    const d = new Date(startVal);
    d.setDate(d.getDate() + n - 1);
    return d.toLocaleDateString('en-GB', { day:'2-digit', month:'long', year:'numeric' });
  }
  return '—';
}

function setupClause(chkId, fieldsId, cardId, startId, daysId, dispId) {
  const chk   = document.getElementById(chkId);
  const flds  = document.getElementById(fieldsId);
  const card  = document.getElementById(cardId);
  const start = startId ? document.getElementById(startId) : null;
  const days  = daysId  ? document.getElementById(daysId)  : null;
  const disp  = dispId  ? document.getElementById(dispId)  : null;

  function refresh() {
    const on = chk.checked;
    flds.style.display = on ? 'block' : 'none';
    card.style.background  = on ? '#eff6ff' : '#f8fafc';
    card.style.borderColor = on ? '#93c5fd' : '#e2e8f0';
    if (start) on ? start.setAttribute('required','required') : start.removeAttribute('required');
    if (days)  on ? days.setAttribute('required','required')  : days.removeAttribute('required');
    if (on && start && days && disp) disp.textContent = calcEndDate(start.value, days.value);
  }

  chk.addEventListener('change', refresh);
  if (start) start.addEventListener('change', () => { if (disp) disp.textContent = calcEndDate(start.value, days.value); });
  if (days)  days.addEventListener('input',   () => { if (disp) disp.textContent = calcEndDate(start.value, days.value); });
  refresh();
}

setupClause('has_ward',       'ward_fields',       'card_ward',       'ward_start_date',       'ward_num_days',       'ward_end_date_display');
setupClause('has_unfit',      'unfit_fields',      'card_unfit',      'start_date',            'num_days',            'end_date_display');
setupClause('has_followup',   'followup_fields',   'card_followup',   'followup_date',         null,                  null);
setupClause('has_light_duty', 'light_duty_fields', 'card_light_duty', 'light_duty_start_date', 'light_duty_num_days', 'light_duty_end_date_display');

document.getElementById('form-edit-mc').addEventListener('submit', function(e) {
  if (!document.getElementById('has_ward').checked &&
      !document.getElementById('has_unfit').checked &&
      !document.getElementById('has_followup').checked &&
      !document.getElementById('has_light_duty').checked) {
    e.preventDefault();
    alert('Please check at least one certification clause (a, b, c, or d).');
  }
});
</script>
