<?php // mc/index.php — Medical Certificate Records with Search & Server-Side Pagination ?>

<div class="section-header">
  <div>
    <h2 class="section-title">Medical Certificate <span>Records</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">All issued medical certificates</p>
  </div>
  <div style="display:flex; align-items:center; gap:8px; background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.2); border-radius:10px; padding:8px 14px;">
    <span style="font-size:16px;">🔗</span>
    <span style="font-size:12px; color:#60a5fa;">Issue new MC via <strong>iPesakit</strong> system</span>
  </div>
</div>

<!-- ── Search Filter Card ──────────────────────────────── -->
<div class="card" style="margin-bottom: 24px; padding: 20px 24px;">
  <form method="GET" action="<?= base_url('mc') ?>" id="form-mc-search">
    <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end;">

      <!-- 1. RN Search Field -->
      <div style="flex:1; min-width:200px;">
        <label for="input-search-rn" class="form-label" style="margin-bottom:6px; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px;">
          Patient RN
        </label>
        <input type="text"
               name="rn"
               id="input-search-rn"
               class="form-control"
               placeholder="Search by RN"
               value="<?= esc($rn ?? '') ?>"
               autocomplete="off">
      </div>

      <!-- 2. Patient Name Search Field -->
      <div style="flex:1.4; min-width:240px;">
        <label for="input-search-name" class="form-label" style="margin-bottom:6px; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px;">
          Patient Name
        </label>
        <input type="text"
               name="name"
               id="input-search-name"
               class="form-control"
               placeholder="Search by Patient Name"
               value="<?= esc($name ?? '') ?>"
               autocomplete="off">
      </div>

      <!-- Search & Reset Buttons -->
      <div style="display:flex; gap:10px; align-items:center;">
        <button type="submit" class="btn btn-primary" id="btn-submit-search" style="padding:10px 22px;">
          <span>🔍</span> Search
        </button>
        <a href="<?= base_url('mc') ?>" class="btn btn-outline" id="btn-reset-search" style="padding:10px 20px;">
          <span>↺</span> Reset
        </a>
      </div>

    </div>

    <!-- Active Filters Feedback -->
    <?php if (!empty($rn) || !empty($name)): ?>
      <div style="margin-top:14px; padding-top:12px; border-top:1px dashed var(--border); display:flex; align-items:center; gap:10px; font-size:12.5px; color:var(--text-secondary); flex-wrap:wrap;">
        <span style="font-weight:600; color:var(--text-primary);">Active Filters:</span>
        <?php if (!empty($rn)): ?>
          <span class="badge badge-blue">RN: "<?= esc($rn) ?>"</span>
        <?php endif; ?>
        <?php if (!empty($name)): ?>
          <span class="badge badge-blue">Patient Name: "<?= esc($name) ?>"</span>
        <?php endif; ?>
        <a href="<?= base_url('mc') ?>" style="color:var(--danger); text-decoration:none; font-size:12px; margin-left:4px; font-weight:600;">✕ Clear filters</a>
      </div>
    <?php endif; ?>
  </form>
</div>

<!-- ── Records Table Card ──────────────────────────────── -->
<div class="card">
  <!-- Card Header with Count & Meta Info -->
  <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border); flex-wrap:wrap; gap:10px;">
    <div style="font-size:13.5px; font-weight:600; color:var(--text-primary);">
      <?php if ($totalRecords > 0): ?>
        Showing <?= number_format($from) ?>–<?= number_format($to) ?> of <?= number_format($totalRecords) ?> records
      <?php else: ?>
        Showing 0 records
      <?php endif; ?>
    </div>
    <div style="font-size:12px; color:var(--text-muted);">
      30 records per page
    </div>
  </div>

  <?php if (empty($records)): ?>
    <div class="empty-state" style="padding:48px 24px; text-align:center;">
      <div class="empty-icon" style="font-size:44px; margin-bottom:12px;">📋</div>
      <h3 style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">No Medical Certificate Records Found</h3>
      <?php if (!empty($rn) || !empty($name)): ?>
        <p style="font-size:13.5px; color:var(--text-secondary); margin-bottom:16px;">
          No medical certificates match your search criteria.
        </p>
        <a href="<?= base_url('mc') ?>" class="btn btn-outline btn-sm">
          <span>↺</span> Clear Search Filters
        </a>
      <?php else: ?>
        <p style="font-size:13.5px; color:var(--text-secondary); margin-bottom:16px;">
          No medical certificates have been issued yet.
        </p>
        <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.25); border-radius:10px; padding:10px 18px;">
          <span>🔗</span>
          <span style="font-size:13px; color:#93c5fd;">Issue new MC via <strong>iPesakit</strong> system</span>
        </div>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th style="width:40px;">#</th>
            <th>Reference Number</th>
            <th>Patient Name</th>
            <th>RN</th>
            <th>Department / Clinic</th>
            <th>Medical Certificate Date</th>
            <th>MC Duration</th>
            <th>Doctor</th>
            <th style="text-align:center; width:150px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($records as $i => $mc): ?>
          <tr id="row-mc-<?= $mc['id'] ?>">
            <td style="color:var(--text-muted); font-size:12.5px;"><?= $from + $i ?></td>
            <td><span class="badge badge-blue"><?= esc($mc['reference_no']) ?></span></td>
            <td>
              <div style="font-weight:600; color:var(--text-primary);"><?= esc($mc['full_name']) ?></div>
              <?php if (!empty($mc['ic_number'])): ?>
                <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">IC: <?= esc($mc['ic_number']) ?></div>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($mc['rn'])): ?>
                <span class="badge badge-blue"><?= esc($mc['rn']) ?></span>
              <?php else: ?>
                <span style="color:var(--text-muted);">—</span>
              <?php endif; ?>
            </td>
            <td style="color:var(--text-secondary); font-size:13px;">
              <?= esc($mc['department'] ?? '—') ?>
            </td>
            <td>
              <span style="font-weight:500;"><?= date('d/m/Y', strtotime($mc['date_issued'])) ?></span>
            </td>
            <td>
              <span class="badge badge-orange"><?= $mc['num_days'] ?> day<?= $mc['num_days'] > 1 ? 's' : '' ?></span>
            </td>
            <td>
              <div style="font-weight:500; font-size:13px; color:var(--text-primary);"><?= esc($mc['doctor_name'] ?? '—') ?></div>
            </td>
            <td style="text-align:center;">
              <div style="display:inline-flex; gap:6px; justify-content:center;">
                <a href="<?= base_url('mc/view/' . $mc['id']) ?>" class="btn btn-outline btn-sm" id="btn-view-mc-<?= $mc['id'] ?>" title="View Medical Certificate">👁 View</a>
                <a href="<?= base_url('mc/edit/' . $mc['id']) ?>" class="btn btn-sm" id="btn-edit-mc-<?= $mc['id'] ?>" style="background:rgba(234,179,8,.12);color:#92400e;border:1px solid rgba(234,179,8,.35);font-weight:600;" title="Edit Medical Certificate">✏️ Edit</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Server-Side Pagination Bar -->
    <?php if ($pager): ?>
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Showing <strong><?= number_format($from) ?>–<?= number_format($to) ?></strong> of <strong><?= number_format($totalRecords) ?></strong> records
        </div>
        <div>
          <?= $pager->only(['rn', 'name'])->links('default', 'emc_pagination') ?>
        </div>
      </div>
    <?php endif; ?>

  <?php endif; ?>
</div>
