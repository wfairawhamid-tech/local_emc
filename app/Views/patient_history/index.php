<?php // patient_history/index.php — Patient Document History ?>

<style>
/* ══════════════════════════════════════════════════════
   Patient History Page
══════════════════════════════════════════════════════ */

/* ── Search Bar ──────────────────────────────────────── */
.ph-search-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 24px 28px;
  box-shadow: 0 2px 10px rgba(15,23,42,.05);
  margin-bottom: 28px;
}
.ph-search-card h3 {
  font-size: 15px; font-weight: 700; color: #0f172a;
  margin: 0 0 16px; display: flex; align-items: center; gap: 8px;
}
.ph-search-form {
  display: flex; gap: 12px; align-items: flex-end;
  flex-wrap: wrap;
}
.ph-search-form .form-group {
  flex: 1; min-width: 200px; margin: 0;
}
.ph-search-form .form-label {
  font-size: 12px; font-weight: 700; color: #475569;
  text-transform: uppercase; letter-spacing: .5px;
  display: block; margin-bottom: 6px;
}
.ph-search-form input {
  font-size: 15px; font-weight: 600;
  letter-spacing: .5px;
}
.ph-search-btn {
  padding: 10px 26px;
  background: linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);
  color: #fff; border: none; border-radius: 9px;
  font-size: 13.5px; font-weight: 700; cursor: pointer;
  display: flex; align-items: center; gap: 8px;
  transition: opacity .15s, transform .15s;
  white-space: nowrap;
}
.ph-search-btn:hover { opacity:.9; transform:translateY(-1px); }

/* ── Patient Identity Card ───────────────────────────── */
.ph-patient-card {
  border-radius: 14px; padding: 22px 26px;
  background: linear-gradient(135deg,#f0f7ff 0%,#e8f4fd 100%);
  border: 1.5px solid #bfdbfe;
  display: flex; align-items: center; gap: 20px;
  margin-bottom: 28px;
  flex-wrap: wrap;
}
.ph-avatar {
  width: 56px; height: 56px; border-radius: 14px;
  background: linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; font-weight: 800; color: #fff;
  flex-shrink: 0; box-shadow: 0 4px 12px rgba(37,99,235,.3);
}
.ph-patient-info { flex: 1; min-width: 200px; }
.ph-patient-info h3 {
  font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 6px;
}
.ph-patient-meta {
  display: flex; flex-wrap: wrap; gap: 10px; margin-top: 4px;
}
.ph-meta-pill {
  display: inline-flex; align-items: center; gap: 5px;
  background: rgba(37,99,235,.1); border: 1px solid rgba(37,99,235,.2);
  color: #1e40af; font-size: 12px; font-weight: 600;
  padding: 3px 10px; border-radius: 99px;
}
.ph-rn-badge {
  background: linear-gradient(135deg,#2563eb,#1d4ed8);
  color: #fff; font-size: 13px; font-weight: 800;
  padding: 4px 14px; border-radius: 99px;
  letter-spacing: .4px; box-shadow: 0 2px 8px rgba(37,99,235,.3);
}

/* ── Stats Row ───────────────────────────────────────── */
.ph-stats-row {
  display: grid; grid-template-columns: repeat(2, 1fr);
  gap: 14px; margin-bottom: 28px;
}
.ph-stat-box {
  border-radius: 12px; padding: 16px 20px;
  display: flex; align-items: center; gap: 14px;
}
.ph-stat-box.mc-stat {
  background: rgba(37,99,235,.07); border: 1px solid rgba(37,99,235,.15);
}
.ph-stat-box.ts-stat {
  background: rgba(5,150,105,.07); border: 1px solid rgba(5,150,105,.15);
}
.ph-stat-icon {
  font-size: 26px; width: 46px; height: 46px;
  border-radius: 10px; display: flex; align-items: center;
  justify-content: center; flex-shrink: 0;
}
.mc-stat .ph-stat-icon { background: rgba(37,99,235,.12); }
.ts-stat .ph-stat-icon { background: rgba(5,150,105,.12); }
.ph-stat-num {
  font-size: 28px; font-weight: 900; line-height: 1;
  margin-bottom: 2px;
}
.mc-stat .ph-stat-num { color: #1d4ed8; }
.ts-stat .ph-stat-num { color: #047857; }
.ph-stat-label { font-size: 12px; font-weight: 600; color: #64748b; }

/* ── History Section ─────────────────────────────────── */
.ph-section {
  margin-bottom: 28px;
}
.ph-section-title {
  font-size: 13px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .7px; color: #64748b; margin-bottom: 12px;
  display: flex; align-items: center; gap: 8px;
}
.ph-section-title::after {
  content: ''; flex: 1; height: 1px; background: #e2e8f0;
}

/* ── History Items ───────────────────────────────────── */
.ph-history-list { display: flex; flex-direction: column; gap: 10px; }

.ph-history-item {
  background: #fff; border-radius: 12px;
  border: 1px solid #e2e8f0;
  padding: 14px 18px;
  display: flex; align-items: center; gap: 14px;
  text-decoration: none;
  transition: box-shadow .15s, border-color .15s, transform .15s;
}
.ph-history-item:hover {
  box-shadow: 0 4px 18px rgba(15,23,42,.09);
  transform: translateX(3px);
}
.ph-history-item.mc-item { border-left: 3.5px solid #3b82f6; }
.ph-history-item.mc-item:hover { border-color: #2563eb; }
.ph-history-item.ts-item { border-left: 3.5px solid #10b981; }
.ph-history-item.ts-item:hover { border-color: #059669; }

.ph-item-icon {
  font-size: 20px; width: 40px; height: 40px;
  border-radius: 10px; display: flex; align-items: center;
  justify-content: center; flex-shrink: 0;
}
.mc-item .ph-item-icon { background: rgba(59,130,246,.1); }
.ts-item .ph-item-icon { background: rgba(16,185,129,.1); }

.ph-item-body { flex: 1; min-width: 0; }
.ph-item-ref {
  font-size: 12px; font-weight: 700; margin-bottom: 3px;
}
.mc-item .ph-item-ref { color: #2563eb; }
.ts-item .ph-item-ref { color: #059669; }
.ph-item-title {
  font-size: 13.5px; font-weight: 700; color: #0f172a; margin-bottom: 2px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.ph-item-sub {
  font-size: 12px; color: #64748b;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.ph-item-right {
  text-align: right; flex-shrink: 0;
}
.ph-item-date {
  font-size: 12px; color: #94a3b8; font-weight: 600; margin-bottom: 4px;
}

/* ── Empty State ─────────────────────────────────────── */
.ph-empty {
  text-align: center; padding: 36px 20px;
  color: #94a3b8;
}
.ph-empty-icon { font-size: 36px; margin-bottom: 10px; }
.ph-empty p { font-size: 13.5px; margin: 0; }

/* ── Responsive ──────────────────────────────────────── */
@media (max-width:640px) {
  .ph-stats-row { grid-template-columns: 1fr; }
  .ph-patient-card { flex-direction: column; align-items: flex-start; }
}
</style>

<!-- ═══════ Page Header ═══════ -->
<div class="section-header">
  <div>
    <h2 class="section-title">Patient <span>History</span></h2>
    <p style="font-size:13px;color:var(--text-secondary);margin-top:4px;">
      Search patient medical document history by Registration Number (RN)
    </p>
  </div>
</div>

<!-- ═══════ Search Card ═══════ -->
<div class="ph-search-card">
  <h3>🔍 Search Patient History</h3>
  <form method="GET" action="<?= base_url('patient-history') ?>" id="form-ph-search" class="ph-search-form">
    <div class="form-group">
      <label class="form-label" for="rn">Search by Patient RN or Name</label>
      <input type="text" id="rn" name="rn" class="form-control"
             placeholder="e.g. RN: 1000001 or Patient Name"
             value="<?= esc($searchTerm ?? $rn) ?>"
             autocomplete="off"
             autofocus>
    </div>
    <button type="submit" class="ph-search-btn" id="btn-ph-search">
      🔍 Search
    </button>
    <?php if (($searchTerm ?? $rn) !== ''): ?>
      <a href="<?= base_url('patient-history') ?>" class="btn btn-outline" style="padding:10px 18px;font-size:13px;">
        ✕ Clear
      </a>
    <?php endif; ?>
  </form>
</div>

<?php if (!empty($multiplePatients)): ?>
  <!-- ═══════ Disambiguation Selection Card ═══════ -->
  <div style="background:#fff; border:1px solid #bfdbfe; border-radius:14px; padding:20px 24px; margin-bottom:28px; box-shadow:0 2px 10px rgba(15,23,42,0.05);">
    <h4 style="margin:0 0 8px; font-size:15px; color:#1e40af; display:flex; align-items:center; gap:8px;">
      👥 Multiple Patients Found (<?= count($multiplePatients) ?> matches)
    </h4>
    <p style="font-size:12.5px; color:#64748b; margin:0 0 16px;">
      Please select a patient below to view their complete document history:
    </p>
    <div style="display:flex; flex-direction:column; gap:10px;">
      <?php foreach ($multiplePatients as $mp): ?>
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; padding:12px 16px; border-radius:10px; background:#f8faff; border:1px solid #e2e8f0;">
          <div>
            <div style="font-weight:700; font-size:14px; color:#0f172a;"><?= esc($mp['full_name']) ?></div>
            <div style="font-size:12px; color:#64748b; margin-top:2px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
              <span class="badge badge-blue" style="font-size:11px;">RN: <?= esc($mp['rn'] ?: 'No RN') ?></span>
              <?php if (!empty($mp['ic_number'])): ?>
                <span>🪪 <?= esc($mp['ic_number']) ?></span>
              <?php endif; ?>
              <span>📄 <?= (int)($mp['total_docs'] ?? 0) ?> Document(s)</span>
            </div>
          </div>
          <a href="<?= base_url('patient-history?rn=' . urlencode($mp['rn'] ?: $mp['id'])) ?>" class="btn btn-primary btn-sm" style="padding:6px 14px; font-size:12px;">
            Select Patient →
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<?php if (($searchTerm ?? $rn) !== '' && $error): ?>
  <!-- ═══════ Error State ═══════ -->
  <div class="alert alert-danger" style="border-radius:12px;">
    🔍 <?= $error ?>
  </div>

<?php elseif ($patient): ?>
  <!-- ═══════ Patient Identity Card ═══════ -->
  <?php
    $initials = '';
    foreach (array_slice(explode(' ', trim($patient['full_name'] ?? '')), 0, 2) as $part) {
      if (!empty($part)) $initials .= strtoupper($part[0]);
    }
    $dob = $patient['date_of_birth'] ?? null;
    $gender = $patient['gender'] ?? null;
  ?>
  <div class="ph-patient-card">
    <div class="ph-avatar"><?= $initials ?: '?' ?></div>
    <div class="ph-patient-info">
      <h3><?= esc($patient['full_name'] ?? '—') ?></h3>
      <div class="ph-patient-meta">
        <span class="ph-rn-badge">RN: <?= esc($patient['rn']) ?></span>
        <?php if (!empty($patient['ic_number'])): ?>
          <span class="ph-meta-pill">🪪 <?= esc($patient['ic_number']) ?></span>
        <?php endif; ?>
        <?php if ($dob): ?>
          <span class="ph-meta-pill">🗓 <?= date('d/m/Y', strtotime($dob)) ?></span>
        <?php endif; ?>
        <?php if ($gender): ?>
          <span class="ph-meta-pill"><?= ($gender === 'Lelaki' || strtolower($gender) === 'male') ? '♂ Male' : '♀ Female' ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ═══════ Summary Stats ═══════ -->
  <div class="ph-stats-row">
    <div class="ph-stat-box mc-stat">
      <div class="ph-stat-icon">📋</div>
      <div>
        <div class="ph-stat-num"><?= count($mcList) ?></div>
        <div class="ph-stat-label">Medical Certificate(s)</div>
      </div>
    </div>
    <div class="ph-stat-box ts-stat">
      <div class="ph-stat-icon">🕐</div>
      <div>
        <div class="ph-stat-num"><?= count($tsList) ?></div>
        <div class="ph-stat-label">Time Slip(s)</div>
      </div>
    </div>
  </div>

  <!-- ═══════ Document History ═══════ -->
  <div class="ph-section">
    <div class="ph-section-title">📋 Medical Certificate History</div>
    <div class="ph-history-list">
      <?php if (empty($mcList)): ?>
        <div class="ph-empty">
          <div class="ph-empty-icon">📭</div>
          <p>No medical certificates found for this patient.</p>
        </div>
      <?php else: ?>
        <?php foreach ($mcList as $mc): ?>
          <?php
            $clauses = [];
            if ($mc['has_ward'])       $clauses[] = 'Ward Admission';
            if ($mc['has_unfit'])      $clauses[] = ($mc['num_days'] ? $mc['num_days'] . ' day' . ($mc['num_days'] > 1 ? 's' : '') . ' MC' : 'Medical Leave');
            if ($mc['has_followup'])   $clauses[] = 'Follow-up';
            if ($mc['has_light_duty']) $clauses[] = 'Light Duty';
            $clauseStr = implode(' · ', $clauses) ?: '—';
          ?>
          <a href="<?= base_url('mc/view/' . $mc['id']) ?>" class="ph-history-item mc-item" id="ph-mc-<?= $mc['id'] ?>">
            <div class="ph-item-icon">📋</div>
            <div class="ph-item-body">
              <div class="ph-item-ref"><?= esc($mc['reference_no']) ?></div>
              <div class="ph-item-title"><?= esc($mc['doctor_name']) ?><?= !empty($mc['department']) ? ' · ' . esc($mc['department']) : '' ?></div>
              <div class="ph-item-sub"><?= $clauseStr ?><?= !empty($mc['diagnosis']) ? ' — ' . esc($mc['diagnosis']) : '' ?></div>
            </div>
            <div class="ph-item-right">
              <div class="ph-item-date"><?= date('d/m/Y', strtotime($mc['date_issued'])) ?></div>
              <span class="badge badge-blue" style="font-size:11px;">View →</span>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- ═══════ Time Slip History ═══════ -->
  <div class="ph-section">
    <div class="ph-section-title">🕐 Time Slip History</div>
    <div class="ph-history-list">
      <?php if (empty($tsList)): ?>
        <div class="ph-empty">
          <div class="ph-empty-icon">📭</div>
          <p>No time slips found for this patient.</p>
        </div>
      <?php else: ?>
        <?php foreach ($tsList as $ts): ?>
          <?php
            $vt = $ts['visit_type'] ?? 'diri_sendiri';
            $vtLabel = match($vt) {
              'diri_sendiri' => '🧍 Self',
              'anak'         => '👶 Child',
              'suami_isteri' => '💑 Spouse',
              'ibu_bapa'     => '👴 Parent',
              default        => '👥 Other',
            };
          ?>
          <a href="<?= base_url('timeslip/view/' . $ts['id']) ?>" class="ph-history-item ts-item" id="ph-ts-<?= $ts['id'] ?>">
            <div class="ph-item-icon">🕐</div>
            <div class="ph-item-body">
              <div class="ph-item-ref"><?= esc($ts['reference_no']) ?></div>
              <div class="ph-item-title"><?= esc($ts['doctor_name']) ?><?= !empty($ts['department']) ? ' · ' . esc($ts['department']) : '' ?></div>
              <div class="ph-item-sub">
                <?= $vtLabel ?> &bull;
                <?= date('H:i', strtotime($ts['time_in'])) ?> – <?= date('H:i', strtotime($ts['time_out'])) ?>
                <?= !empty($ts['purpose']) ? ' · ' . esc($ts['purpose']) : '' ?>
              </div>
            </div>
            <div class="ph-item-right">
              <div class="ph-item-date"><?= date('d/m/Y', strtotime($ts['date_issued'])) ?></div>
              <span class="badge badge-green" style="font-size:11px;">View →</span>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

<?php elseif ($rn === ''): ?>
  <!-- ═══════ Initial State (no search yet) ═══════ -->
  <div style="text-align:center; padding:48px 20px; color:#94a3b8;">
    <div style="font-size:48px; margin-bottom:14px;">🔍</div>
    <p style="font-size:15px; font-weight:600; color:#64748b; margin:0 0 6px;">Enter a Patient RN to search</p>
    <p style="font-size:13px; margin:0;">
      The RN (Registration Number) is the master identifier that links all documents across departments.
    </p>
  </div>
<?php endif; ?>
