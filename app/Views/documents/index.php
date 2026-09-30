<?php // documents/index.php — Medical Documents hub (selection page) ?>

<style>
/* ── Documents Hub Page ─────────────────────────────────── */
.docs-hub {
  max-width: 780px;
  margin: 0 auto;
}

.docs-hub-header {
  text-align: center;
  margin-bottom: 36px;
}
.docs-hub-header h2 {
  font-size: 26px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 8px;
  letter-spacing: -0.4px;
}
.docs-hub-header p {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

/* ── iPesakit notice ──── */
.docs-ipesakit-notice {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(59,130,246,0.07);
  border: 1px solid rgba(59,130,246,0.2);
  border-radius: 10px;
  padding: 10px 16px;
  margin-bottom: 32px;
  font-size: 13px;
  color: #3b82f6;
}

/* ── Card Grid ─────────────────────────────────────────── */
.doc-choice-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 22px;
}
@media (max-width: 600px) {
  .doc-choice-grid { grid-template-columns: 1fr; }
}

.doc-choice-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  text-decoration: none;
  border-radius: 16px;
  padding: 36px 28px 30px;
  border: 2px solid transparent;
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(15,23,42,0.06);
  transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease;
  position: relative;
  overflow: hidden;
}
.doc-choice-card::before {
  content: '';
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity 0.18s ease;
}
.doc-choice-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(15,23,42,0.12);
}
.doc-choice-card:hover::before { opacity: 1; }

/* MC card — blue */
.doc-choice-card.mc { border-color: #bfdbfe; }
.doc-choice-card.mc::before { background: linear-gradient(160deg, rgba(219,234,254,0.5) 0%, rgba(239,246,255,0.3) 100%); }
.doc-choice-card.mc:hover { border-color: #3b82f6; background: #f0f7ff; }
.doc-choice-card.mc .doc-icon-wrap { background: linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%); }

/* TS card — green */
.doc-choice-card.ts { border-color: #bbf7d0; }
.doc-choice-card.ts::before { background: linear-gradient(160deg, rgba(220,252,231,0.5) 0%, rgba(240,253,244,0.3) 100%); }
.doc-choice-card.ts:hover { border-color: #22c55e; background: #f0fdf4; }
.doc-choice-card.ts .doc-icon-wrap { background: linear-gradient(135deg,#059669 0%,#047857 100%); }

.doc-icon-wrap {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 34px;
  margin-bottom: 20px;
  box-shadow: 0 4px 14px rgba(0,0,0,0.15);
  transition: transform 0.18s ease;
}
.doc-choice-card:hover .doc-icon-wrap {
  transform: scale(1.08);
}

.doc-choice-card h3 {
  font-size: 17px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 6px;
  line-height: 1.25;
}
.doc-choice-card p {
  font-size: 13px;
  color: #64748b;
  margin: 0 0 22px;
  line-height: 1.6;
}

.doc-cta {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 20px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.2px;
  pointer-events: none; /* parent <a> handles click */
  transition: background 0.15s;
}
.doc-choice-card.mc .doc-cta { background: #2563eb; color: #fff; }
.doc-choice-card.ts .doc-cta { background: #059669; color: #fff; }
</style>

<div class="docs-hub">

  <!-- Header -->
  <div class="docs-hub-header">
    <h2>Medical Documents</h2>
    <p>Select the type of document to create</p>
  </div>

  <!-- iPesakit notice -->
  <div class="docs-ipesakit-notice">
    <span style="font-size:18px;">🔗</span>
    <span>To issue a new document, open this system through <strong>iPesakit</strong> with the patient's record.</span>
  </div>

  <!-- Document Selection Cards -->
  <div class="doc-choice-grid">

    <!-- Medical Certificate (MC) -->
    <a href="<?= base_url('mc') ?>" class="doc-choice-card mc" id="btn-go-mc">
      <div class="doc-icon-wrap">📋</div>
      <h3>Medical Certificate<br>(MC)</h3>
      <p>Create and manage<br>Medical Certificates</p>
      <span class="doc-cta">View Records →</span>
    </a>

    <!-- Time Slip -->
    <a href="<?= base_url('timeslip') ?>" class="doc-choice-card ts" id="btn-go-timeslip">
      <div class="doc-icon-wrap">🕐</div>
      <h3>Time Slip</h3>
      <p>Create and manage<br>Time Slips</p>
      <span class="doc-cta">View Records →</span>
    </a>

  </div>

</div>
