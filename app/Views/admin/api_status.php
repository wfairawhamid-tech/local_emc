<?php // admin/api_status.php — API Connectivity Diagnostic for IT Admin ?>

<div class="section-header" style="margin-bottom: 24px;">
  <div>
    <h2 class="section-title">iPesakit API <span>Connectivity Diagnostics</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">
      Review environment configuration and test real-time connectivity to the iPesakit hospital server.
    </p>
  </div>
</div>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 24px;">
  <!-- Current Configuration -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><span class="icon">⚙️</span> iPesakit Server Configuration (.env)</h3>
    </div>
    <div class="card-body">
      <div style="margin-bottom: 14px;">
        <label style="font-size:12px; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">ENDPOINT URL (PATIENT_API_URL):</label>
        <code style="background:#f1f5f9; padding:8px 12px; border-radius:6px; display:block; word-break:break-all; font-size:13px; color:#0f172a;">
          <?= esc($apiUrl ?: 'NOT CONFIGURED') ?>
        </code>
      </div>
      <div>
        <label style="font-size:12px; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">BEARER TOKEN (PATIENT_API_TOKEN):</label>
        <code style="background:#f1f5f9; padding:8px 12px; border-radius:6px; display:block; font-size:13px; color:#0f172a;">
          <?= esc($tokenMasked) ?>
        </code>
      </div>
    </div>
  </div>

  <!-- Ping / Connection Test Tool -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><span class="icon">📡</span> Real-Time Connection Test</h3>
    </div>
    <div class="card-body">
      <form method="GET" action="<?= base_url('admin/api-status') ?>">
        <input type="hidden" name="test_connection" value="1">
        <div class="form-group" style="margin-bottom:16px;">
          <label class="form-label" for="test_rn">Test Patient RN Number:</label>
          <input type="text" name="test_rn" id="test_rn" class="form-control" value="1000002" placeholder="Enter patient RN for test query">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">
          ⚡ Send Ping / Test Connection
        </button>
      </form>
    </div>
  </div>
</div>

<?php if ($pingStatus !== null): ?>
  <div class="card" style="margin-top: 20px;">
    <div class="card-header">
      <h3 class="card-title"><span class="icon">📊</span> Connection Test Results</h3>
    </div>
    <div class="card-body">
      <div style="display:flex; align-items:center; gap:16px; margin-bottom:16px;">
        <?php if ($pingStatus['is_success']): ?>
          <span class="badge" style="background:#dcfce7; color:#166534; font-size:14px; padding:6px 14px; border-radius:8px; font-weight:700;">
            ✔ HTTP 200 OK (Connection Successful)
          </span>
          <span style="font-size:13px; color:#475569;">
            Response Time: <strong><?= $pingStatus['time_ms'] ?> ms</strong>
          </span>
        <?php else: ?>
          <span class="badge" style="background:#fee2e2; color:#991b1b; font-size:14px; padding:6px 14px; border-radius:8px; font-weight:700;">
            ✖ Failed / HTTP Status: <?= $pingStatus['code'] ?: 'Connection Terminated / Timeout' ?>
          </span>
        <?php endif; ?>
      </div>

      <?php if ($errorMsg): ?>
        <div class="alert alert-danger" style="margin-bottom:16px;">
          <strong>Connection Error:</strong> <?= esc($errorMsg) ?>
        </div>
      <?php endif; ?>

      <?php if ($responseBody): ?>
        <div>
          <label style="font-size:12px; font-weight:700; color:#64748b; margin-bottom:6px; display:block;">API Response Payload from iPesakit:</label>
          <pre style="background:#0f172a; color:#f8fafc; padding:16px; border-radius:8px; font-size:12.5px; overflow-x:auto; max-height:260px;"><?= esc($responseBody) ?></pre>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
