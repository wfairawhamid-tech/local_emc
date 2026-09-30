<?php // patients/form.php — Add new patient form ?>

<div class="section-header">
  <div>
    <h2 class="section-title">Add <span>New Patient</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">Fill in the patient information. Date of birth &amp; gender will be auto-filled from the IC Number.</p>
  </div>
  <a href="<?= base_url('patients') ?>" class="btn btn-outline">← Back</a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    ⚠️
    <?php foreach ((array)$errors as $e): ?>
      <div><?= esc($e) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <span class="icon">📝</span>
      Patient Information
    </h3>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= base_url('patients/store') ?>" id="form-add-patient">
      <?= csrf_field() ?>

      <!-- IC Number with auto-parse -->
      <div class="form-group">
        <label class="form-label" for="ic_number">Identification Card No. (IC) <span class="required">*</span></label>
        <div style="display:flex; gap:10px;">
          <input
            type="text"
            id="ic_number"
            name="ic_number"
            class="form-control"
            placeholder="e.g. 850312-10-5671"
            value="<?= esc($ic ?? '') ?>"
            maxlength="14"
            required
            autocomplete="off"
          >
          <button type="button" class="btn btn-outline" id="btn-parse-ic">🔍 Parse IC</button>
        </div>
        <p class="form-hint">12-digit Malaysian IC number. Date of birth &amp; gender will be filled automatically.</p>
      </div>

      <!-- Auto-filled IC info banner -->
      <?php if ($parsed): ?>
        <div class="alert alert-success" id="ic-info-banner">
          ✅ IC identified —
          Date of Birth: <strong><?= date('d/m/Y', strtotime($parsed['dob'])) ?></strong> |
          Gender: <strong><?= $parsed['gender'] === 'Lelaki' ? 'Male' : 'Female' ?></strong>
        </div>
      <?php endif; ?>

      <div id="ic-result-banner" style="display:none;" class="alert alert-success"></div>

      <!-- Full Name -->
      <div class="form-group">
        <label class="form-label" for="full_name">Full Name <span class="required">*</span></label>
        <input
          type="text"
          id="full_name"
          name="full_name"
          class="form-control"
          placeholder="e.g. Hafiz bin Abdullah"
          value="<?= esc(old('full_name')) ?>"
          required
        >
      </div>

      <!-- Patient RN (iSyifaa) -->
      <div class="form-group">
        <label class="form-label" for="rn">Patient RN (iSyifaa)</label>
        <input
          type="text"
          id="rn"
          name="rn"
          class="form-control"
          placeholder="e.g. 1000001"
          value="<?= esc(old('rn')) ?>"
          maxlength="20"
          autocomplete="off"
        >
        <p class="form-hint">Registration Number from the iSyifaa system. Leave blank if unknown.</p>
      </div>

      <!-- Auto-filled fields (readonly) -->
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="date_of_birth_display">Date of Birth</label>
          <input
            type="text"
            id="date_of_birth_display"
            class="form-control"
            value="<?= $parsed ? date('d/m/Y', strtotime($parsed['dob'])) : '' ?>"
            readonly
            placeholder="Auto from IC"
          >
        </div>
        <div class="form-group">
          <label class="form-label" for="gender_display">Gender</label>
          <input
            type="text"
            id="gender_display"
            class="form-control"
            value="<?= isset($parsed['gender']) ? ($parsed['gender'] === 'Lelaki' ? 'Male' : 'Female') : '' ?>"
            readonly
            placeholder="Auto from IC"
          >
        </div>
      </div>

      <!-- Phone & Address -->
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="phone">Phone Number</label>
          <input
            type="tel"
            id="phone"
            name="phone"
            class="form-control"
            placeholder="e.g. 012-3456789"
            value="<?= esc(old('phone')) ?>"
          >
        </div>
        <div class="form-group"><!-- spacer --></div>
      </div>

      <div class="form-group">
        <label class="form-label" for="address">Address</label>
        <textarea
          id="address"
          name="address"
          class="form-control"
          rows="3"
          placeholder="Patient's residential address..."
        ><?= esc(old('address')) ?></textarea>
      </div>

      <div class="divider"></div>

      <div style="display:flex; gap:12px; justify-content:flex-end;">
        <a href="<?= base_url('patients') ?>" class="btn btn-outline">✕ Cancel</a>
        <button type="submit" class="btn btn-primary btn-lg" id="btn-save-patient">
          💾 Save &amp; Continue
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // IC Parser via inline logic
  const IC_INPUT    = document.getElementById('ic_number');
  const PARSE_BTN   = document.getElementById('btn-parse-ic');
  const DOB_FIELD   = document.getElementById('date_of_birth_display');
  const GENDER_FIELD = document.getElementById('gender_display');
  const BANNER      = document.getElementById('ic-result-banner');

  function parseIC() {
    const raw = IC_INPUT.value.replace(/\D/g, '');
    if (raw.length !== 12) {
      BANNER.style.display = 'block';
      BANNER.className = 'alert alert-danger';
      BANNER.innerHTML = '⚠️ IC number must be exactly 12 digits.';
      return;
    }

    const yy = raw.substr(0,2), mm = raw.substr(2,2), dd = raw.substr(4,2);
    const lastDigit = parseInt(raw.substr(-1));
    const year = parseInt(yy) <= parseInt(new Date().getFullYear().toString().substr(-2)) ? '20'+yy : '19'+yy;
    const gender = (lastDigit % 2 !== 0) ? 'Male' : 'Female';

    DOB_FIELD.value    = dd + '/' + mm + '/' + year;
    GENDER_FIELD.value = gender;

    // Format IC
    IC_INPUT.value = raw.substr(0,6) + '-' + raw.substr(6,2) + '-' + raw.substr(8,4);

    BANNER.style.display = 'block';
    BANNER.className = 'alert alert-success';
    BANNER.innerHTML = '✅ IC identified — Date of Birth: <strong>' + dd + '/' + mm + '/' + year + '</strong> | Gender: <strong>' + gender + '</strong>';
  }

  PARSE_BTN.addEventListener('click', parseIC);

  IC_INPUT.addEventListener('input', function() {
    const raw = this.value.replace(/\D/g,'');
    if (raw.length === 12) parseIC();
  });
</script>
