<?php // patients/select.php — Search & select patient ?>

<!-- Header -->
<div class="section-header">
  <div>
    <h2 class="section-title">Select <span>Patient</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">Search for an existing patient or add a new one</p>
  </div>
  <a href="<?= base_url('patients/create') ?>" class="btn btn-primary" id="btn-add-patient">
    ➕ Add New Patient
  </a>
</div>

<!-- Search Bar -->
<div class="card" style="margin-bottom: 24px;">
  <div class="card-body">
    <form method="GET" action="<?= base_url('patients') ?>" id="form-search-patient">
      <div style="display:flex; gap:12px; align-items:center;">
        <div class="search-bar" style="flex:1; max-width:100%;">
          <span class="search-icon">🔍</span>
          <input
            type="text"
            name="search"
            id="input-search"
            class="form-control"
            placeholder="Search by name, IC No., or patient RN..."
            value="<?= esc($search ?? '') ?>"
            autofocus
          >
        </div>
        <button type="submit" class="btn btn-primary" id="btn-search">🔍 Search</button>
        <?php if ($search): ?>
          <a href="<?= base_url('patients') ?>" class="btn btn-outline" id="btn-reset-search">✕ Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<!-- Results -->
<?php if (empty($patients)): ?>
  <div class="empty-state">
    <div class="empty-icon">👤</div>
    <h3>No patients found</h3>
    <?php if ($search): ?>
      <p>No records found for "<strong><?= esc($search) ?></strong>"</p>
      <a href="<?= base_url('patients/create?ic=' . urlencode($search)) ?>" class="btn btn-primary" id="btn-add-from-search">
        ➕ Add This Patient
      </a>
    <?php else: ?>
      <p>No patients have been registered in the system yet</p>
      <a href="<?= base_url('patients/create') ?>" class="btn btn-primary">➕ Add First Patient</a>
    <?php endif; ?>
  </div>
<?php else: ?>
  <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">
    <?= count($patients) ?> patient(s) found
    <?php if ($search): ?> for search "<strong><?= esc($search) ?></strong>"<?php endif; ?>
  </p>

  <div class="patient-grid">
    <?php foreach ($patients as $p): ?>
      <?php
        $initials = '';
        $nameParts = explode(' ', $p['full_name']);
        foreach (array_slice($nameParts, 0, 2) as $part) {
          if (!empty($part)) $initials .= strtoupper($part[0]);
        }
        $isMale = $p['gender'] === 'Lelaki';
        $age    = date_diff(date_create($p['date_of_birth']), date_create())->y;
      ?>
      <div class="patient-card" id="card-patient-<?= $p['id'] ?>">
        <div class="patient-card-header">
          <div class="patient-avatar <?= $isMale ? 'male' : 'female' ?>">
            <?= $initials ?>
          </div>
          <div class="patient-card-info">
            <h3><?= esc($p['full_name']) ?></h3>
            <p><?= $isMale ? 'Male' : 'Female' ?> &bull; <?= $age ?> years old</p>
          </div>
        </div>
        <div class="patient-meta">
          <div class="patient-meta-row">
            <span class="label">IC No.</span>
            <span><?= esc($p['ic_number']) ?></span>
          </div>
          <?php if (!empty($p['rn'])): ?>
          <div class="patient-meta-row">
            <span class="label">RN (iSyifaa)</span>
            <span><strong><?= esc($p['rn']) ?></strong></span>
          </div>
          <?php endif; ?>
          <div class="patient-meta-row">
            <span class="label">Date of Birth</span>
            <span><?= date('d/m/Y', strtotime($p['date_of_birth'])) ?></span>
          </div>
          <?php if ($p['phone']): ?>
          <div class="patient-meta-row">
            <span class="label">Phone No.</span>
            <span><?= esc($p['phone']) ?></span>
          </div>
          <?php endif; ?>
        </div>
        <div class="patient-card-actions">
          <a href="<?= base_url('patients/select/' . $p['id']) ?>"
             class="btn btn-primary" style="flex:1; justify-content:center;"
             id="btn-select-<?= $p['id'] ?>">
            ✅ Select This Patient
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
