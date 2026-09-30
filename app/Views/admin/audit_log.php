<?php // admin/audit_log.php — Audit Log for IT Admin ?>

<div class="section-header" style="margin-bottom: 24px;">
  <div>
    <h2 class="section-title">Document Issuance <span>Audit Log</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">
      Comprehensive audit trail of all Medical Certificates (MC) and Time Slips issued by medical officers.
    </p>
  </div>
</div>

<div class="card" style="margin-bottom: 24px;">
  <div class="card-body" style="padding: 16px 20px;">
    <form method="GET" action="<?= base_url('admin/audit-log') ?>" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
      <div style="flex:1; min-width:240px;">
        <input type="text" name="q" class="form-control" placeholder="Search Reference No, Patient Name, IC, RN, or Doctor..." value="<?= esc($search) ?>">
      </div>
      <div>
        <select name="type" class="form-control" style="width:180px;">
          <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All Documents</option>
          <option value="mc" <?= $type === 'mc' ? 'selected' : '' ?>>Medical Certificate (MC)</option>
          <option value="ts" <?= $type === 'ts' ? 'selected' : '' ?>>Time Slip</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter / Search</button>
      <?php if ($search || $type !== 'all'): ?>
        <a href="<?= base_url('admin/audit-log') ?>" class="btn btn-outline">Reset</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
    <h3 class="card-title"><span class="icon">📋</span> Recently Issued Documents</h3>
    <span style="font-size:12px; color:var(--text-secondary);">Found: <?= count($logs) ?> Records</span>
  </div>
  <div class="card-body" style="padding:0;">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Type</th>
            <th>Reference No.</th>
            <th>Date Issued</th>
            <th>Patient Information</th>
            <th>Medical Practitioner (Doctor)</th>
            <th style="text-align:right;">Link</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $row): ?>
              <tr>
                <td>
                  <?php if ($row['doc_type'] === 'MC'): ?>
                    <span class="badge" style="background:#dbeafe; color:#1d4ed8; font-weight:700; padding:4px 8px; border-radius:4px; font-size:11.5px;">MC</span>
                  <?php else: ?>
                    <span class="badge" style="background:#fef3c7; color:#92400e; font-weight:700; padding:4px 8px; border-radius:4px; font-size:11.5px;">Time Slip</span>
                  <?php endif; ?>
                </td>
                <td>
                  <strong style="color:#0f172a;"><?= esc($row['reference_no']) ?></strong>
                </td>
                <td style="color:#64748b; font-size:13px;">
                  <?= date('d/m/Y', strtotime($row['date_issued'])) ?>
                </td>
                <td>
                  <div style="font-weight:600; color:#0f172a;"><?= esc($row['patient_name']) ?></div>
                  <div style="font-size:12px; color:#64748b;">
                    IC: <?= esc($row['ic_number']) ?>
                    <?php if (!empty($row['rn'])): ?> &bull; RN: <strong><?= esc($row['rn']) ?></strong><?php endif; ?>
                  </div>
                </td>
                <td>
                  <div style="font-weight:600; color:#1e293b;"><?= esc($row['doctor_name']) ?></div>
                </td>
                <td style="text-align:right;">
                  <?php if ($row['doc_type'] === 'MC'): ?>
                    <a href="<?= base_url('mc/view/' . $row['id']) ?>" target="_blank" class="btn btn-outline btn-sm">View MC ↗</a>
                  <?php else: ?>
                    <a href="<?= base_url('timeslip/view/' . $row['id']) ?>" target="_blank" class="btn btn-outline btn-sm">View Slip ↗</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align:center; padding:32px; color:#64748b;">
                No document issuance records found.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
