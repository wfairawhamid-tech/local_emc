<?php // timeslip/index.php — List of all Time Slips ?>

<div class="section-header">
  <div>
    <h2 class="section-title">Time Slip <span>Records</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">All issued time slips</p>
  </div>
  <div style="display:flex; align-items:center; gap:8px; background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.2); border-radius:10px; padding:8px 14px;">
    <span style="font-size:16px;">🔗</span>
    <span style="font-size:12px; color:#4ade80;">Keluarkan Time Slip baru melalui sistem <strong>iPesakit</strong></span>
  </div>
</div>

<div class="card">
  <?php if (empty($records)): ?>
    <div class="empty-state">
      <div class="empty-icon">🕐</div>
      <h3>No Time Slip Records</h3>
      <p>No time slips have been issued yet</p>
      <div style="margin-top:16px; display:inline-flex; align-items:center; gap:8px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.25); border-radius:10px; padding:10px 18px;">
        <span>🔗</span>
        <span style="font-size:13px; color:#4ade80;">Keluarkan Time Slip baru melalui sistem <strong>iPesakit</strong></span>
      </div>
    </div>
  <?php else: ?>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Reference No.</th>
            <th>Patient Name</th>
            <th>RN (iSyifaa)</th>
            <th>IC Number</th>
            <th>Visit Type</th>
            <th>Date</th>
            <th>Arrival Time</th>
            <th>Departure Time</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($records as $i => $ts): ?>
          <tr id="row-ts-<?= $ts['id'] ?>">
            <td style="color:var(--text-muted);"><?= $i + 1 ?></td>
            <td><span class="badge badge-green"><?= esc($ts['reference_no']) ?></span></td>
            <td style="font-weight:600;"><?= esc($ts['full_name']) ?></td>
            <td><?php if (!empty($ts['rn'])): ?><span class="badge badge-green"><?= esc($ts['rn']) ?></span><?php else: ?><span style="color:var(--text-muted);">—</span><?php endif; ?></td>
            <td style="color:var(--text-secondary);"><?= esc($ts['ic_number']) ?></td>
            <td>
              <?php $vt = $ts['visit_type'] ?? 'diri_sendiri'; ?>
              <?php if ($vt === 'diri_sendiri'): ?>
                <span class="badge badge-green" style="font-size:11px;">Self</span>
              <?php elseif ($vt === 'anak'): ?>
                <span class="badge badge-blue" style="font-size:11px;">👶 Child</span>
              <?php elseif ($vt === 'suami_isteri'): ?>
                <span class="badge badge-orange" style="font-size:11px;">💑 Spouse</span>
              <?php elseif ($vt === 'ibu_bapa'): ?>
                <span class="badge badge-orange" style="font-size:11px;">👴 Parent</span>
              <?php else: ?>
                <span class="badge badge-orange" style="font-size:11px;">👥 Other</span>
              <?php endif; ?>
            </td>
            <td><?= date('d/m/Y', strtotime($ts['date_issued'])) ?></td>
            <td><?= date('H:i', strtotime($ts['time_in'])) ?></td>
            <td><?= date('H:i', strtotime($ts['time_out'])) ?></td>
            <td>
              <div style="display:flex; gap:6px;">
                <a href="<?= base_url('timeslip/view/' . $ts['id']) ?>" class="btn btn-outline btn-sm" id="btn-view-ts-<?= $ts['id'] ?>">👁 View</a>
                <a href="<?= base_url('timeslip/edit/' . $ts['id']) ?>" class="btn btn-sm" id="btn-edit-ts-<?= $ts['id'] ?>" style="background:rgba(234,179,8,.12);color:#92400e;border:1px solid rgba(234,179,8,.35);font-weight:600;">✏️ Edit</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
