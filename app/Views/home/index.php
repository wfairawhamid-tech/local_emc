<?php
// app/Views/home/index.php — Modernized eMC Dashboard Analytics
?>

<style>
/* ═══════════════════════════════════════════════════════
   eMC Professional Hospital Dashboard Styles
   ═══════════════════════════════════════════════════════ */

/* ── 1. Top KPI Grid ─────────────────────────────────── */
.emc-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 20px;
}
@media (max-width: 1100px) {
  .emc-kpi-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
  .emc-kpi-grid { grid-template-columns: 1fr; }
}

.emc-kpi-card {
  background: #ffffff;
  border-radius: 12px;
  padding: 16px 18px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
  display: flex;
  align-items: flex-start;
  gap: 14px;
  position: relative;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.emc-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
}
.emc-kpi-icon {
  width: 46px;
  height: 46px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}
.emc-kpi-card.blue .emc-kpi-icon   { background: #eff6ff; color: #2563eb; }
.emc-kpi-card.green .emc-kpi-icon  { background: #ecfdf5; color: #059669; }
.emc-kpi-card.purple .emc-kpi-icon { background: #f5f3ff; color: #7c3aed; }
.emc-kpi-card.amber .emc-kpi-icon  { background: #fffbeb; color: #d97706; }

.emc-kpi-content {
  flex: 1;
  min-width: 0;
}
.emc-kpi-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 4px;
}
.emc-kpi-label {
  font-size: 12.5px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}
.emc-kpi-badge {
  font-size: 10.5px;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 99px;
  background: #f1f5f9;
  color: #475569;
}
.emc-kpi-value {
  font-size: 28px;
  font-weight: 800;
  line-height: 1.15;
  color: #0f172a;
  letter-spacing: -0.5px;
  font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
  margin-bottom: 6px;
}
.emc-kpi-comp {
  font-size: 11.5px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.comp-up      { color: #059669; }
.comp-down    { color: #dc2626; }
.comp-neutral { color: #64748b; }

/* ── 2. Dashboard Filter Bar ─────────────────────────── */
.emc-filter-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px 18px;
  margin-bottom: 20px;
  box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03);
}
.emc-filter-form {
  display: flex;
  align-items: flex-end;
  gap: 12px;
  flex-wrap: wrap;
}
.emc-filter-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
  min-width: 130px;
}
.emc-filter-item.grow {
  flex: 1;
  min-width: 160px;
}
.emc-filter-item label {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.emc-filter-item select,
.emc-filter-item input {
  padding: 7px 10px;
  font-size: 13px;
  font-weight: 500;
  border-radius: 8px;
  border: 1px solid #cbd5e1;
  background-color: #ffffff;
  color: #1e293b;
  height: 36px;
  box-sizing: border-box;
}
.emc-filter-item select:focus,
.emc-filter-item input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
}
.emc-custom-dates {
  display: none;
  align-items: flex-end;
  gap: 10px;
}
.emc-custom-dates.active {
  display: flex;
}
.emc-filter-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-left: auto;
}
.emc-btn-filter {
  padding: 7px 16px;
  background: #2563eb;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  height: 36px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: background 0.15s ease;
}
.emc-btn-filter:hover {
  background: #1d4ed8;
}
.emc-btn-reset {
  padding: 7px 14px;
  background: #f8fafc;
  color: #475569;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  height: 36px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.15s ease;
}
.emc-btn-reset:hover {
  background: #f1f5f9;
  color: #0f172a;
}
.emc-active-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #1e40af;
  border-radius: 99px;
  padding: 3px 10px;
  font-size: 11.5px;
  font-weight: 600;
  margin-top: 8px;
}

/* ── 3. Quick Patient Search Section ─────────────────── */
.emc-quick-search-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 18px 22px;
  margin-bottom: 22px;
  box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
.emc-search-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
  flex-wrap: wrap;
  gap: 8px;
}
.emc-search-title {
  font-size: 15px;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0;
}
.emc-search-desc {
  font-size: 12.5px;
  color: #64748b;
  margin: 2px 0 0;
}
.emc-search-modes {
  display: flex;
  gap: 6px;
  background: #f1f5f9;
  padding: 3px;
  border-radius: 8px;
}
.emc-mode-btn {
  padding: 4px 10px;
  font-size: 11.5px;
  font-weight: 600;
  color: #64748b;
  border-radius: 6px;
  border: none;
  background: transparent;
  cursor: pointer;
  transition: all 0.15s ease;
}
.emc-mode-btn.active {
  background: #ffffff;
  color: #2563eb;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.emc-search-form {
  display: flex;
  gap: 10px;
  position: relative;
}
.emc-search-input-wrap {
  position: relative;
  flex: 1;
}
.emc-search-input-wrap span.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 15px;
  color: #94a3b8;
  pointer-events: none;
}
.emc-search-input {
  width: 100%;
  height: 42px;
  padding: 8px 14px 8px 38px;
  font-size: 14px;
  font-weight: 600;
  border-radius: 9px;
  border: 1.5px solid #cbd5e1;
  background: #ffffff;
  color: #0f172a;
  box-sizing: border-box;
  transition: all 0.15s ease;
}
.emc-search-input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}
.emc-search-submit {
  padding: 0 22px;
  height: 42px;
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff;
  border: none;
  border-radius: 9px;
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
  transition: opacity 0.15s, transform 0.15s;
}
.emc-search-submit:hover {
  opacity: 0.95;
  transform: translateY(-1px);
}

/* Quick Search Instant Result Preview */
.emc-search-results {
  margin-top: 14px;
  border-radius: 10px;
  background: #f8faff;
  border: 1px solid #e2e8f0;
  padding: 12px 14px;
  display: none;
}
.emc-search-results.has-results {
  display: block;
}
.emc-search-patient-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  margin-bottom: 8px;
  transition: border-color 0.15s;
}
.emc-search-patient-item:last-child {
  margin-bottom: 0;
}
.emc-search-patient-item:hover {
  border-color: #93c5fd;
}
.emc-sp-info {
  display: flex;
  align-items: center;
  gap: 12px;
}
.emc-sp-avatar {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  background: #eff6ff;
  color: #2563eb;
  font-weight: 800;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.emc-sp-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}
.emc-sp-meta {
  font-size: 11.5px;
  color: #64748b;
  display: flex;
  gap: 8px;
  align-items: center;
  margin-top: 2px;
}
.emc-rn-tag {
  background: #2563eb;
  color: #ffffff;
  padding: 1px 7px;
  border-radius: 99px;
  font-size: 11px;
  font-weight: 700;
}

/* ── 4. Two-Column Dashboard Layout Grid ─────────────── */
.emc-dash-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}
@media (max-width: 960px) {
  .emc-dash-row {
    grid-template-columns: 1fr;
  }
}

/* Standard Section Card */
.emc-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.emc-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  border-bottom: 1px solid #f1f5f9;
  background: #ffffff;
}
.emc-card-title {
  font-size: 14px;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 7px;
  margin: 0;
}
.emc-card-subtitle {
  font-size: 11.5px;
  color: #64748b;
  font-weight: 500;
  margin-left: 4px;
}
.emc-card-badge {
  font-size: 11.5px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 99px;
  background: #f1f5f9;
  color: #334155;
}
.emc-card-body {
  padding: 16px 18px;
  flex: 1;
}

/* ── 5. Duration Distribution Bars ───────────────────── */
.emc-duration-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.emc-duration-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.emc-duration-meta {
  display: flex;
  justify-content: space-between;
  font-size: 12.5px;
}
.emc-duration-label {
  font-weight: 700;
  color: #334155;
  display: flex;
  align-items: center;
  gap: 6px;
}
.emc-duration-count {
  font-weight: 700;
  color: #0f172a;
}
.emc-duration-pct {
  color: #94a3b8;
  font-size: 11.5px;
  font-weight: 500;
  margin-left: 4px;
}
.emc-progress-track {
  height: 8px;
  background: #f1f5f9;
  border-radius: 99px;
  overflow: hidden;
  position: relative;
}
.emc-progress-fill {
  height: 100%;
  border-radius: 99px;
  transition: width 0.4s ease;
}
.emc-fill-1 { background: #3b82f6; }
.emc-fill-2 { background: #06b6d4; }
.emc-fill-3 { background: #10b981; }
.emc-fill-4 { background: #f59e0b; }
.emc-fill-5 { background: #8b5cf6; }

/* ── 6. Recent Activity List ─────────────────────────── */
.emc-activity-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.emc-activity-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  border-radius: 9px;
  background: #f8fafc;
  border: 1px solid #edf2f7;
  text-decoration: none;
  transition: all 0.15s ease;
  gap: 12px;
}
.emc-activity-item:hover {
  background: #ffffff;
  border-color: #cbd5e1;
  box-shadow: 0 2px 8px rgba(15,23,42,0.06);
  transform: translateX(2px);
}
.emc-act-left {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}
.emc-act-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  flex-shrink: 0;
}
.emc-act-icon.mc { background: #eff6ff; color: #2563eb; }
.emc-act-icon.ts { background: #ecfdf5; color: #059669; }

.emc-act-info {
  min-width: 0;
}
.emc-act-patient {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: flex;
  align-items: center;
  gap: 6px;
}
.emc-act-sub {
  font-size: 11.5px;
  color: #64748b;
  margin-top: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.emc-act-right {
  text-align: right;
  flex-shrink: 0;
}
.emc-act-ref {
  font-size: 11.5px;
  font-weight: 700;
  color: #1e293b;
}
.emc-act-date {
  font-size: 10.5px;
  color: #94a3b8;
  margin-top: 1px;
}

/* ── 7. Today's Summary & Quick Actions ──────────────── */
.emc-today-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  text-align: center;
}
@media (max-width: 480px) {
  .emc-today-grid { grid-template-columns: 1fr; }
}
.emc-today-item {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 14px 10px;
}
.emc-today-num {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.1;
  font-family: 'Segoe UI', system-ui, sans-serif;
  margin-bottom: 4px;
}
.emc-today-label {
  font-size: 11.5px;
  font-weight: 600;
  color: #64748b;
}

.emc-actions-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}
@media (max-width: 480px) {
  .emc-actions-grid { grid-template-columns: 1fr; }
}
.emc-action-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  border-radius: 10px;
  text-decoration: none;
  font-size: 12.5px;
  font-weight: 700;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #1e293b;
  transition: all 0.15s ease;
}
.emc-action-btn:hover {
  border-color: #2563eb;
  background: #eff6ff;
  color: #1d4ed8;
  transform: translateY(-1px);
}
.emc-action-icon {
  font-size: 18px;
  width: 32px;
  height: 32px;
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f1f5f9;
}
.emc-action-btn:hover .emc-action-icon {
  background: #dbeafe;
}

/* Empty state helper */
.emc-empty-state {
  text-align: center;
  padding: 30px 10px;
  color: #94a3b8;
}
.emc-empty-icon {
  font-size: 32px;
  margin-bottom: 6px;
}
.emc-empty-text {
  font-size: 12.5px;
  font-weight: 500;
}
</style>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- ROW 1 — Top 4 KPI Cards                                 -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="emc-kpi-grid">

  <!-- CARD 1: Total Patients -->
  <div class="emc-kpi-card blue" id="kpi-card-patients">
    <div class="emc-kpi-icon">👥</div>
    <div class="emc-kpi-content">
      <div class="emc-kpi-top">
        <span class="emc-kpi-label">Total Patients</span>
        <span class="emc-kpi-badge" id="kpi-period-patients"><?= esc($periodLabel) ?></span>
      </div>
      <div class="emc-kpi-value" id="kpi-val-patients"><?= number_format($totalPatients) ?></div>
      <div class="emc-kpi-comp <?= $compPatients['class'] ?? 'comp-neutral' ?>" id="kpi-comp-patients">
        <?php if ($compPatients): ?>
          <span><?= $compPatients['symbol'] ?></span>
          <span><?= esc($compPatients['text']) ?></span>
        <?php else: ?>
          <span style="color:#94a3b8; font-weight:normal;">Selected Period Record</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- CARD 2: Medical Certificates -->
  <div class="emc-kpi-card green" id="kpi-card-mc">
    <div class="emc-kpi-icon">📋</div>
    <div class="emc-kpi-content">
      <div class="emc-kpi-top">
        <span class="emc-kpi-label">Medical Certificates</span>
        <span class="emc-kpi-badge" id="kpi-period-mc"><?= esc($periodLabel) ?></span>
      </div>
      <div class="emc-kpi-value" id="kpi-val-mc"><?= number_format($totalMC) ?></div>
      <div class="emc-kpi-comp <?= $compMC['class'] ?? 'comp-neutral' ?>" id="kpi-comp-mc">
        <?php if ($compMC): ?>
          <span><?= $compMC['symbol'] ?></span>
          <span><?= esc($compMC['text']) ?></span>
        <?php else: ?>
          <span style="color:#94a3b8; font-weight:normal;">Selected Period Record</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- CARD 3: Time Slips -->
  <div class="emc-kpi-card purple" id="kpi-card-ts">
    <div class="emc-kpi-icon">🕐</div>
    <div class="emc-kpi-content">
      <div class="emc-kpi-top">
        <span class="emc-kpi-label">Time Slips</span>
        <span class="emc-kpi-badge" id="kpi-period-ts"><?= esc($periodLabel) ?></span>
      </div>
      <div class="emc-kpi-value" id="kpi-val-ts"><?= number_format($totalTS) ?></div>
      <div class="emc-kpi-comp <?= $compTS['class'] ?? 'comp-neutral' ?>" id="kpi-comp-ts">
        <?php if ($compTS): ?>
          <span><?= $compTS['symbol'] ?></span>
          <span><?= esc($compTS['text']) ?></span>
        <?php else: ?>
          <span style="color:#94a3b8; font-weight:normal;">Selected Period Record</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- CARD 4: Departments / Clinics -->
  <div class="emc-kpi-card amber" id="kpi-card-depts">
    <div class="emc-kpi-icon">🏥</div>
    <div class="emc-kpi-content">
      <div class="emc-kpi-top">
        <span class="emc-kpi-label">Departments / Clinics</span>
        <span class="emc-kpi-badge">Active Units</span>
      </div>
      <div class="emc-kpi-value" id="kpi-val-depts"><?= number_format($totalDepts) ?></div>
      <div class="emc-kpi-comp comp-neutral" id="kpi-comp-depts">
        <span>🏥</span>
        <span><?= !empty($selectedDept) ? esc($selectedDept) : 'All Issuing Units' ?></span>
      </div>
    </div>
  </div>

</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- ROW 2 — Dashboard Period Filter                         -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="emc-filter-card">
  <form method="GET" action="<?= base_url('/') ?>" id="form-dashboard-filter" class="emc-filter-form">

    <!-- Period Selector -->
    <div class="emc-filter-item">
      <label for="filter-period">📅 Period</label>
      <select name="period" id="filter-period">
        <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>This Month</option>
        <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>This Year</option>
        <option value="all" <?= $period === 'all' ? 'selected' : '' ?>>All Time</option>
        <option value="custom" <?= $period === 'custom' ? 'selected' : '' ?>>Custom Range</option>
      </select>
    </div>

    <!-- Custom Date Range (Shown when Custom Range is chosen) -->
    <div class="emc-custom-dates <?= $period === 'custom' ? 'active' : '' ?>" id="wrap-custom-dates">
      <div class="emc-filter-item">
        <label for="filter-date-from">Date From</label>
        <input type="date" name="date_from" id="filter-date-from" value="<?= esc($dateFrom ?: date('Y-m-01')) ?>">
      </div>
      <div class="emc-filter-item">
        <label for="filter-date-to">Date To</label>
        <input type="date" name="date_to" id="filter-date-to" value="<?= esc($dateTo ?: date('Y-m-d')) ?>">
      </div>
    </div>

    <!-- Department Dropdown -->
    <div class="emc-filter-item grow">
      <label for="filter-department">🏥 Department</label>
      <select name="department" id="filter-department">
        <option value="">All Departments</option>
        <?php foreach ($allDepartments as $dept): ?>
          <option value="<?= esc($dept) ?>" <?= ($selectedDept === $dept) ? 'selected' : '' ?>>
            <?= esc($dept) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Document Type Dropdown -->
    <div class="emc-filter-item">
      <label for="filter-doctype">📄 Document Type</label>
      <select name="doc_type" id="filter-doctype">
        <option value="all" <?= ($selectedDocType === 'all') ? 'selected' : '' ?>>All Documents</option>
        <option value="mc" <?= ($selectedDocType === 'mc') ? 'selected' : '' ?>>Medical Certificates</option>
        <option value="timeslip" <?= ($selectedDocType === 'timeslip') ? 'selected' : '' ?>>Time Slips</option>
      </select>
    </div>

    <!-- Actions -->
    <div class="emc-filter-actions">
      <button type="submit" class="emc-btn-filter" id="btn-apply-filter">
        <span>🔍</span> Apply Filter
      </button>
      <a href="<?= base_url('/') ?>" class="emc-btn-reset" id="btn-reset-filter" title="Reset to This Month">
        <span>✕</span> Reset
      </a>
    </div>

  </form>

  <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
    <div class="emc-active-pill" id="badge-active-filter">
      <span>📌 Active Period:</span>
      <strong id="label-active-filter"><?= esc($periodLabel) ?></strong>
      <?php if (!empty($selectedDept)): ?>
        <span>&bull;</span>
        <span>Dept: <strong><?= esc($selectedDept) ?></strong></span>
      <?php endif; ?>
      <?php if ($selectedDocType !== 'all'): ?>
        <span>&bull;</span>
        <span>Type: <strong><?= $selectedDocType === 'mc' ? 'MC Only' : 'Time Slip Only' ?></strong></span>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- ROW 3 — Quick Patient Search                            -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="emc-quick-search-card" id="section-quick-search">
  <div class="emc-search-header">
    <div>
      <h3 class="emc-search-title">
        <span>🔍</span> Quick Patient Search
      </h3>
      <p class="emc-search-desc">
        Search patient records using <strong>RN (Registration Number)</strong> or Patient Name.
      </p>
    </div>

    <div class="emc-search-modes">
      <button type="button" class="emc-mode-btn active" data-mode="all">Auto / All</button>
      <button type="button" class="emc-mode-btn" data-mode="rn">By RN</button>
      <button type="button" class="emc-mode-btn" data-mode="name">By Patient Name</button>
    </div>
  </div>

  <form method="GET" action="<?= base_url('patient-history') ?>" id="form-quick-search" class="emc-search-form">
    <div class="emc-search-input-wrap">
      <span class="search-icon">🔍</span>
      <input type="text"
             name="rn"
             id="input-quick-search"
             class="emc-search-input"
             placeholder="Enter Patient RN (e.g. 1000002) or Patient Name..."
             autocomplete="off">
    </div>
    <button type="submit" class="emc-search-submit" id="btn-quick-search-submit">
      <span>🔍</span> Search
    </button>
  </form>

  <!-- Live Search Result Box -->
  <div class="emc-search-results" id="quick-search-results">
    <div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:8px; display:flex; justify-content:space-between;">
      <span id="quick-search-header-text">Matching Patients</span>
      <button type="button" id="btn-close-search-results" style="background:none; border:none; color:#94a3b8; cursor:pointer; font-size:12px;">✕ Close</button>
    </div>
    <div id="quick-search-list"></div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- ROW 4 — Monthly Trend + Documents by Department         -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="emc-dash-row">

  <!-- Monthly Document Trend (Chart.js Line Chart) -->
  <div class="emc-card">
    <div class="emc-card-header">
      <h4 class="emc-card-title">
        <span>📈</span> Monthly Document Trend
        <span class="emc-card-subtitle">Medical Certificates vs Time Slips</span>
      </h4>
      <span class="emc-card-badge" id="trend-year-badge">Year <?= esc($chartYear) ?></span>
    </div>
    <div class="emc-card-body" style="height:280px; position:relative;">
      <canvas id="monthlyTrendChart"></canvas>
    </div>
  </div>

  <!-- Documents by Department (Chart.js Horizontal Bar) -->
  <div class="emc-card">
    <div class="emc-card-header">
      <h4 class="emc-card-title">
        <span>🏥</span> Documents by Department
        <span class="emc-card-subtitle">Distribution by Clinic &amp; Ward</span>
      </h4>
      <button type="button" class="btn btn-outline btn-sm" id="btn-toggle-dept-view" style="font-size:11.5px; padding:3px 10px;">
        📋 View All
      </button>
    </div>
    <div class="emc-card-body" style="height:280px; position:relative; overflow-y:auto;" id="dept-chart-container">
      <canvas id="deptDistributionChart"></canvas>
      <div id="dept-list-table" style="display:none; padding-top:4px;">
        <table class="table" style="width:100%; font-size:12.5px;">
          <thead>
            <tr>
              <th>Department / Clinic</th>
              <th style="text-align:right;">Issued Documents</th>
            </tr>
          </thead>
          <tbody id="dept-table-body">
            <?php foreach ($deptDistribution as $d): ?>
              <tr>
                <td><strong><?= esc($d['name']) ?></strong></td>
                <td style="text-align:right;"><span class="badge badge-blue"><?= number_format($d['count']) ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- ROW 5 — MC Duration Overview + Recent Activity          -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="emc-dash-row">

  <!-- Medical Certificate Duration Overview -->
  <div class="emc-card">
    <div class="emc-card-header">
      <h4 class="emc-card-title">
        <span>⏱️</span> Medical Certificate Duration
        <span class="emc-card-subtitle">Distribution by Leave Days</span>
      </h4>
      <span class="emc-card-badge" id="mc-dur-total-badge"><?= number_format($mcDuration['total'] ?? 0) ?> MCs</span>
    </div>
    <div class="emc-card-body">
      <div class="emc-duration-list" id="mc-duration-container">
        <?php
          $totalMcCount = (int) ($mcDuration['total'] ?? 0);
          $durCategories = [
            '1 Day'            => ['count' => (int) ($mcDuration['1 Day'] ?? 0), 'class' => 'emc-fill-1'],
            '2 Days'           => ['count' => (int) ($mcDuration['2 Days'] ?? 0), 'class' => 'emc-fill-2'],
            '3 Days'           => ['count' => (int) ($mcDuration['3 Days'] ?? 0), 'class' => 'emc-fill-3'],
            '4–7 Days'         => ['count' => (int) ($mcDuration['4–7 Days'] ?? 0), 'class' => 'emc-fill-4'],
            'More than 7 Days' => ['count' => (int) ($mcDuration['More than 7 Days'] ?? 0), 'class' => 'emc-fill-5'],
          ];
        ?>
        <?php foreach ($durCategories as $catLabel => $catData): ?>
          <?php
            $c = $catData['count'];
            $pct = $totalMcCount > 0 ? round(($c / $totalMcCount) * 100, 1) : 0;
          ?>
          <div class="emc-duration-item">
            <div class="emc-duration-meta">
              <span class="emc-duration-label">
                <span>🗓</span> <?= esc($catLabel) ?>
              </span>
              <div>
                <span class="emc-duration-count"><?= number_format($c) ?> records</span>
                <span class="emc-duration-pct">(<?= $pct ?>%)</span>
              </div>
            </div>
            <div class="emc-progress-track">
              <div class="emc-progress-fill <?= $catData['class'] ?>" style="width: <?= $pct ?>%;"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div style="font-size:11px; color:#94a3b8; margin-top:14px; text-align:center;">
        Analytical summary only. Reflects leave days granted in the selected filter period.
      </div>
    </div>
  </div>

  <!-- Recent Activity Panel -->
  <div class="emc-card">
    <div class="emc-card-header">
      <h4 class="emc-card-title">
        <span>⚡</span> Recent Activity
        <span class="emc-card-subtitle">Latest Issued Documents</span>
      </h4>
      <a href="<?= base_url('documents') ?>" class="btn btn-outline btn-sm" id="btn-recent-view-all" style="font-size:11.5px; padding:3px 10px;">
        View All →
      </a>
    </div>
    <div class="emc-card-body" style="overflow-y:auto; max-height:340px;">
      <div class="emc-activity-list" id="recent-activity-container">
        <?php if (empty($recentActivity)): ?>
          <div class="emc-empty-state">
            <div class="emc-empty-icon">📭</div>
            <div class="emc-empty-text">No document activity found for this period.</div>
          </div>
        <?php else: ?>
          <?php foreach ($recentActivity as $act): ?>
            <?php
              $isMc     = ($act['doc_type'] === 'mc');
              $viewUrl  = $isMc ? base_url('mc/view/' . $act['id']) : base_url('timeslip/view/' . $act['id']);
              $durText  = $isMc
                ? ($act['num_days'] . ' Day' . ($act['num_days'] > 1 ? 's' : '') . ' MC')
                : (date('H:i', strtotime($act['time_in'] ?? '00:00')) . ' – ' . date('H:i', strtotime($act['time_out'] ?? '00:00')));
              $timeDate = !empty($act['created_at']) ? date('d M, H:i', strtotime($act['created_at'])) : date('d M Y', strtotime($act['date_issued']));
            ?>
            <a href="<?= $viewUrl ?>" class="emc-activity-item">
              <div class="emc-act-left">
                <div class="emc-act-icon <?= $isMc ? 'mc' : 'ts' ?>">
                  <?= $isMc ? '📋' : '🕐' ?>
                </div>
                <div class="emc-act-info">
                  <div class="emc-act-patient">
                    <?= esc($act['patient_name'] ?? 'Patient') ?>
                    <?php if (!empty($act['rn'])): ?>
                      <span class="emc-rn-tag">RN: <?= esc($act['rn']) ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="emc-act-sub">
                    <?= $isMc ? 'Medical Certificate' : 'Time Slip' ?> &bull;
                    <?= esc($act['department'] ?: 'General') ?> &bull;
                    <strong><?= esc($durText) ?></strong>
                  </div>
                </div>
              </div>
              <div class="emc-act-right">
                <div class="emc-act-ref"><?= esc($act['reference_no']) ?></div>
                <div class="emc-act-date"><?= esc($timeDate) ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- ROW 6 — Today's Summary + Quick Actions                 -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="emc-dash-row" style="margin-bottom:0;">

  <!-- Today's Summary -->
  <div class="emc-card">
    <div class="emc-card-header">
      <h4 class="emc-card-title">
        <span>☀️</span> Today's Summary
        <span class="emc-card-subtitle"><?= esc($todaySummary['date'] ?? date('d M Y')) ?></span>
      </h4>
      <span class="badge badge-green" style="font-size:11px;">● Live Today</span>
    </div>
    <div class="emc-card-body">
      <div class="emc-today-grid">

        <div class="emc-today-item">
          <div class="emc-today-num" id="today-val-patients"><?= number_format($todaySummary['patients'] ?? 0) ?></div>
          <div class="emc-today-label">👥 Today's Patients</div>
        </div>

        <div class="emc-today-item">
          <div class="emc-today-num" id="today-val-mc"><?= number_format($todaySummary['mc'] ?? 0) ?></div>
          <div class="emc-today-label">📋 MCs Today</div>
        </div>

        <div class="emc-today-item">
          <div class="emc-today-num" id="today-val-ts"><?= number_format($todaySummary['ts'] ?? 0) ?></div>
          <div class="emc-today-label">🕐 Time Slips Today</div>
        </div>

      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="emc-card">
    <div class="emc-card-header">
      <h4 class="emc-card-title">
        <span>⚡</span> Quick Actions
        <span class="emc-card-subtitle">Common Shortcuts</span>
      </h4>
    </div>
    <div class="emc-card-body">
      <div class="emc-actions-grid">

        <a href="<?= base_url('documents') ?>" class="emc-action-btn" id="qa-create-mc">
          <span class="emc-action-icon">📋</span>
          <div>
            <div>+ Create MC</div>
            <span style="font-size:10.5px; color:#64748b; font-weight:normal;">Medical Certificate</span>
          </div>
        </a>

        <a href="<?= base_url('documents') ?>" class="emc-action-btn" id="qa-create-ts">
          <span class="emc-action-icon">🕐</span>
          <div>
            <div>+ Create Time Slip</div>
            <span style="font-size:10.5px; color:#64748b; font-weight:normal;">Attendance Slip</span>
          </div>
        </a>

        <button type="button" class="emc-action-btn" id="qa-search-patient" style="cursor:pointer; text-align:left;">
          <span class="emc-action-icon">🔍</span>
          <div>
            <div>Search Patient</div>
            <span style="font-size:10.5px; color:#64748b; font-weight:normal;">Find by RN / Name</span>
          </div>
        </button>

        <a href="<?= base_url('patient-history') ?>" class="emc-action-btn" id="qa-patient-history">
          <span class="emc-action-icon">📋</span>
          <div>
            <div>Patient History</div>
            <span style="font-size:10.5px; color:#64748b; font-weight:normal;">Master RN Records</span>
          </div>
        </a>

      </div>
    </div>
  </div>

</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- Chart.js and Interactive Dashboard Scripts             -->
<!-- ═══════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
/* ── Global State & Config ───────────────────────────── */
const CHART_API_URL    = '<?= base_url('dashboard/chartData') ?>';
const SEARCH_API_URL   = '<?= base_url('dashboard/searchPatient') ?>';
const PATIENT_HIST_URL = '<?= base_url('patient-history') ?>';

let monthlyChartInst   = null;
let deptChartInst      = null;
let searchDebounce     = null;
let currentSearchMode  = 'all';

// Initial Server Data
let initialTrendData   = <?= json_encode($trend) ?>;
let initialDeptData    = <?= json_encode($deptDistribution) ?>;

/* ── Custom Chart.js Plugin for Soft Shadows ─────────── */
const softShadowPlugin = {
  id: 'softShadow',
  beforeDatasetDraw(chart, args) {
    const { ctx } = chart;
    ctx.save();
    ctx.shadowColor = 'rgba(15, 23, 42, 0.08)';
    ctx.shadowBlur = 6;
    ctx.shadowOffsetY = 3;
    ctx.shadowOffsetX = 0;
  },
  afterDatasetDraw(chart) {
    chart.ctx.restore();
  }
};

/* ── 1. Render Monthly Document Trend (Line Chart) ───── */
function renderMonthlyTrendChart(trendData) {
  const ctx = document.getElementById('monthlyTrendChart');
  if (!ctx) return;

  if (monthlyChartInst) {
    monthlyChartInst.destroy();
  }

  const chartCtx = ctx.getContext('2d');

  // Gradients for lines and soft area fills
  const mcGradient = chartCtx.createLinearGradient(0, 0, 0, 260);
  mcGradient.addColorStop(0, 'rgba(37, 99, 235, 0.22)');
  mcGradient.addColorStop(1, 'rgba(37, 99, 235, 0.01)');

  const tsGradient = chartCtx.createLinearGradient(0, 0, 0, 260);
  tsGradient.addColorStop(0, 'rgba(124, 58, 237, 0.20)');
  tsGradient.addColorStop(1, 'rgba(124, 58, 237, 0.01)');

  monthlyChartInst = new Chart(chartCtx, {
    type: 'line',
    plugins: [softShadowPlugin],
    data: {
      labels: trendData.labels || ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets: [
        {
          label: 'Medical Certificates',
          data: trendData.mc || [],
          borderColor: '#2563eb',
          backgroundColor: mcGradient,
          borderWidth: 2.5,
          tension: 0.35,
          fill: true,
          pointBackgroundColor: '#2563eb',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
        },
        {
          label: 'Time Slips',
          data: trendData.ts || [],
          borderColor: '#7c3aed',
          backgroundColor: tsGradient,
          borderWidth: 2.5,
          tension: 0.35,
          fill: true,
          pointBackgroundColor: '#7c3aed',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      plugins: {
        legend: {
          position: 'top',
          align: 'end',
          labels: {
            boxWidth: 10,
            boxHeight: 10,
            usePointStyle: true,
            pointStyle: 'circle',
            font: { family: "'Segoe UI', system-ui, sans-serif", size: 11.5, weight: '600' },
            color: '#475569',
            padding: 12
          }
        },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.94)',
          titleFont: { size: 12.5, weight: '700' },
          bodyFont: { size: 12, weight: '500' },
          padding: 10,
          cornerRadius: 8,
          boxPadding: 4,
          usePointStyle: true,
          callbacks: {
            label: function(context) {
              return ` ${context.dataset.label}: ${context.formattedValue} documents`;
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: { size: 11, weight: '600' }, color: '#64748b' }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(226, 232, 240, 0.6)' },
          ticks: {
            font: { size: 11 },
            color: '#64748b',
            precision: 0,
            callback: (v) => Number.isInteger(v) ? v : null
          }
        }
      }
    }
  });
}

/* ── 2. Render Documents by Department (Horizontal Bar) ─ */
function renderDeptDistributionChart(deptData) {
  const ctx = document.getElementById('deptDistributionChart');
  if (!ctx) return;

  if (deptChartInst) {
    deptChartInst.destroy();
  }

  // Top 6 departments for chart readability
  const topDepts = (deptData || []).slice(0, 6);
  const labels   = topDepts.map(d => d.name);
  const counts   = topDepts.map(d => d.count);

  if (labels.length === 0) {
    labels.push('No Records');
    counts.push(0);
  }

  const chartCtx = ctx.getContext('2d');
  const barGradient = chartCtx.createLinearGradient(0, 0, 300, 0);
  barGradient.addColorStop(0, '#3b82f6');
  barGradient.addColorStop(1, '#1d4ed8');

  deptChartInst = new Chart(chartCtx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Documents Issued',
        data: counts,
        backgroundColor: barGradient,
        borderRadius: 6,
        borderSkipped: false,
        maxBarThickness: 22,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.94)',
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            label: (item) => ` ${item.raw} total documents issued`
          }
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          grid: { color: 'rgba(226, 232, 240, 0.6)' },
          ticks: {
            font: { size: 11 },
            color: '#64748b',
            precision: 0
          }
        },
        y: {
          grid: { display: false },
          ticks: {
            font: { size: 11.5, weight: '600' },
            color: '#334155',
            callback: function(value) {
              const label = this.getLabelForValue(value);
              return label.length > 24 ? label.substr(0, 22) + '...' : label;
            }
          }
        }
      }
    }
  });
}

/* ── 3. Quick Patient Search Integration ─────────────── */
const searchInput   = document.getElementById('input-quick-search');
const searchResults = document.getElementById('quick-search-results');
const searchList    = document.getElementById('quick-search-list');
const closeSearchBtn = document.getElementById('btn-close-search-results');

// Mode buttons: Auto / RN / Patient Name
document.querySelectorAll('.emc-mode-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.emc-mode-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    currentSearchMode = this.getAttribute('data-mode');

    if (currentSearchMode === 'rn') {
      searchInput.placeholder = 'Enter Patient RN (e.g. 1000002)...';
    } else if (currentSearchMode === 'name') {
      searchInput.placeholder = 'Enter Patient Name (e.g. Ahmad, Siti)...';
    } else {
      searchInput.placeholder = 'Enter Patient RN or Patient Name...';
    }
    searchInput.focus();
    triggerLiveSearch();
  });
});

if (closeSearchBtn) {
  closeSearchBtn.addEventListener('click', () => {
    searchResults.classList.remove('has-results');
  });
}

if (searchInput) {
  searchInput.addEventListener('input', function() {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(triggerLiveSearch, 250);
  });
}

async function triggerLiveSearch() {
  const query = (searchInput.value || '').trim();
  if (query.length < 1) {
    searchResults.classList.remove('has-results');
    return;
  }

  try {
    const res = await fetch(`${SEARCH_API_URL}?q=${encodeURIComponent(query)}`);
    if (!res.ok) throw new Error('Search failed');
    const json = await res.json();

    if (!json.data || json.data.length === 0) {
      searchList.innerHTML = `
        <div style="padding:14px; text-align:center; color:#94a3b8; font-size:12.5px;">
          🔍 No patients found matching "<strong>${escapeHtml(query)}</strong>".
        </div>`;
      searchResults.classList.add('has-results');
      return;
    }

    let html = '';
    json.data.forEach(p => {
      const initials = (p.full_name || 'P').split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
      const rnParam  = encodeURIComponent(p.rn || p.id);
      const histUrl  = `${PATIENT_HIST_URL}?rn=${rnParam}`;

      html += `
        <div class="emc-search-patient-item">
          <div class="emc-sp-info">
            <div class="emc-sp-avatar">${initials}</div>
            <div>
              <div class="emc-sp-name">${escapeHtml(p.full_name)}</div>
              <div class="emc-sp-meta">
                <span class="emc-rn-tag">RN: ${escapeHtml(p.rn || 'None')}</span>
                ${p.ic_number ? `<span>🪪 ${escapeHtml(p.ic_number)}</span>` : ''}
                <span>📄 ${p.total_docs || 0} Docs (${p.mc_count || 0} MC / ${p.ts_count || 0} TS)</span>
              </div>
            </div>
          </div>
          <div style="display:flex; gap:8px;">
            <a href="${histUrl}" class="btn btn-primary btn-sm" style="padding:5px 14px; font-size:12px; display:inline-flex; align-items:center; gap:4px;">
              📋 View History →
            </a>
          </div>
        </div>
      `;
    });

    searchList.innerHTML = html;
    searchResults.classList.add('has-results');
  } catch (err) {
    console.error('Patient search error:', err);
  }
}

function escapeHtml(str) {
  if (!str) return '';
  return str.toString()
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

/* ── 4. Period Filter Behavior & AJAX Updates ─────────── */
const periodSelect     = document.getElementById('filter-period');
const customDatesWrap  = document.getElementById('wrap-custom-dates');
const filterForm       = document.getElementById('form-dashboard-filter');

if (periodSelect) {
  periodSelect.addEventListener('change', function() {
    if (this.value === 'custom') {
      customDatesWrap.classList.add('active');
    } else {
      customDatesWrap.classList.remove('active');
    }
  });
}

// Department View Toggle: Chart vs Table
const btnToggleDept = document.getElementById('btn-toggle-dept-view');
const deptCanvas    = document.getElementById('deptDistributionChart');
const deptTable     = document.getElementById('dept-list-table');
let deptViewMode    = 'chart';

if (btnToggleDept) {
  btnToggleDept.addEventListener('click', function() {
    if (deptViewMode === 'chart') {
      deptViewMode = 'table';
      deptCanvas.style.display = 'none';
      deptTable.style.display = 'block';
      this.textContent = '📊 View Chart';
    } else {
      deptViewMode = 'chart';
      deptCanvas.style.display = 'block';
      deptTable.style.display = 'none';
      this.textContent = '📋 View All';
    }
  });
}

// Quick Action: Focus Patient Search
const qaSearchBtn = document.getElementById('qa-search-patient');
if (qaSearchBtn) {
  qaSearchBtn.addEventListener('click', function() {
    const s = document.getElementById('section-quick-search');
    if (s) {
      s.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(() => searchInput.focus(), 300);
    }
  });
}

/* ── 5. Initialize Charts ─────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
  renderMonthlyTrendChart(initialTrendData);
  renderDeptDistributionChart(initialDeptData);
});
</script>
