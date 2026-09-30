<?php // admin/doctors.php — Doctor Management for IT Admin ?>

<div class="section-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
  <div>
    <h2 class="section-title">Medical Practitioners <span>(Doctors) Management</span></h2>
    <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">
      Manage registered medical officers authorized to issue Medical Certificates (MC) and Time Slips.
    </p>
  </div>
  <button type="button" class="btn btn-primary" onclick="openDoctorModal()">
    + Add New Doctor
  </button>
</div>

<?php if (session()->has('success')): ?>
  <div class="alert alert-success" style="margin-bottom: 20px;">
    ✅ <?= session('success') ?>
  </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
  <div class="alert alert-danger" style="margin-bottom: 20px;">
    ⚠️ <?= session('error') ?>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
    <h3 class="card-title"><span class="icon">👨‍⚕️</span> Registered Medical Officers</h3>
    <span style="font-size:12px; color:var(--text-secondary);">Total: <?= count($doctors) ?> Doctors</span>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th style="width: 60px;">ID</th>
            <th>Doctor Name</th>
            <th>MMC Registration No.</th>
            <th>Professional Qualifications</th>
            <th>Department / Unit</th>
            <th style="width: 110px; text-align:center;">Status</th>
            <th style="width: 160px; text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($doctors)): ?>
            <?php foreach ($doctors as $doc): ?>
              <tr>
                <td style="font-weight:700; color:#64748b;">#<?= $doc['id'] ?></td>
                <td>
                  <strong style="color:var(--text-primary); font-size:14px;"><?= esc($doc['name']) ?></strong>
                </td>
                <td>
                  <code style="background:#f1f5f9; padding:2px 8px; border-radius:4px; font-weight:600; color:#0f172a;">
                    <?= esc($doc['mcr_number'] ?: '—') ?>
                  </code>
                </td>
                <td style="font-size:13px; color:#475569;"><?= esc($doc['qualification'] ?: '—') ?></td>
                <td style="font-size:13px;"><?= esc($doc['department'] ?: 'Medicine') ?></td>
                <td style="text-align:center;">
                  <?php if ($doc['is_active']): ?>
                    <span class="badge" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-weight:700; padding:4px 10px; border-radius:20px; font-size:11.5px;">
                      Active
                    </span>
                  <?php else: ?>
                    <span class="badge" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; font-weight:700; padding:4px 10px; border-radius:20px; font-size:11.5px;">
                      Inactive
                    </span>
                  <?php endif; ?>
                </td>
                <td style="text-align:right; white-space:nowrap;">
                  <button type="button" class="btn btn-outline btn-sm"
                          onclick='editDoctor(<?= json_encode($doc) ?>)'
                          style="margin-right: 6px;">
                    Edit
                  </button>
                  <a href="<?= base_url('admin/doctors/toggle/' . $doc['id']) ?>"
                     class="btn btn-sm <?= $doc['is_active'] ? 'btn-danger' : 'btn-success' ?>"
                     onclick="return confirm('Are you sure you want to change this doctor\'s status?')">
                    <?= $doc['is_active'] ? 'Deactivate' : 'Activate' ?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" style="text-align:center; padding: 32px; color:#64748b;">
                No doctor records registered. Please click the button above to register.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal: Add / Edit Doctor -->
<div id="doctorModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.6); z-index:9999; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#ffffff; border-radius:14px; max-width:540px; width:100%; box-shadow:0 20px 40px rgba(0,0,0,0.25); overflow:hidden;">
    <div style="background:#1e3a8a; color:#fff; padding:18px 24px; display:flex; justify-content:space-between; align-items:center;">
      <h3 id="modalTitle" style="margin:0; font-size:17px; font-weight:700;">Register New Doctor</h3>
      <button type="button" onclick="closeDoctorModal()" style="background:none; border:none; color:#fff; font-size:22px; cursor:pointer; line-height:1;">&times;</button>
    </div>
    <form method="POST" action="<?= base_url('admin/doctors/save') ?>" id="doctorForm" style="padding:24px;">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="doc_id" value="">

      <div class="form-group" style="margin-bottom:16px;">
        <label class="form-label" for="doc_name">Doctor Full Name <span class="required">*</span></label>
        <input type="text" name="name" id="doc_name" class="form-control" required placeholder="e.g. Dr. Ahmad Fauzi bin Ismail">
      </div>

      <div class="form-row" style="display:flex; gap:16px; margin-bottom:16px;">
        <div class="form-group" style="flex:1;">
          <label class="form-label" for="doc_mcr">MMC / MCR Number</label>
          <input type="text" name="mcr_number" id="doc_mcr" class="form-control" placeholder="e.g. MMC-12345">
        </div>
        <div class="form-group" style="flex:1;">
          <label class="form-label" for="doc_dept">Department / Unit</label>
          <input type="text" name="department" id="doc_dept" class="form-control" placeholder="e.g. General Medicine">
        </div>
      </div>

      <div class="form-group" style="margin-bottom:20px;">
        <label class="form-label" for="doc_qual">Professional Qualifications (Printed on Certificate)</label>
        <input type="text" name="qualification" id="doc_qual" class="form-control" placeholder="e.g. MBBS (UM), FRACGP">
      </div>

      <div class="form-group" style="margin-bottom:24px;">
        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600; color:#1e293b;">
          <input type="checkbox" name="is_active" id="doc_is_active" value="1" checked style="width:18px; height:18px;">
          <span>Active Status (Authorized to issue MC and Time Slips)</span>
        </label>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; padding-top:18px;">
        <button type="button" class="btn btn-outline" onclick="closeDoctorModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btnSaveDoc">Save Information</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openDoctorModal() {
    document.getElementById('modalTitle').innerText = 'Register New Doctor';
    document.getElementById('doc_id').value = '';
    document.getElementById('doc_name').value = '';
    document.getElementById('doc_mcr').value = '';
    document.getElementById('doc_dept').value = '';
    document.getElementById('doc_qual').value = '';
    document.getElementById('doc_is_active').checked = true;
    document.getElementById('doctorModal').style.display = 'flex';
  }

  function editDoctor(doc) {
    document.getElementById('modalTitle').innerText = 'Update Doctor Information';
    document.getElementById('doc_id').value = doc.id;
    document.getElementById('doc_name').value = doc.name;
    document.getElementById('doc_mcr').value = doc.mcr_number || '';
    document.getElementById('doc_dept').value = doc.department || '';
    document.getElementById('doc_qual').value = doc.qualification || '';
    document.getElementById('doc_is_active').checked = (doc.is_active == 1);
    document.getElementById('doctorModal').style.display = 'flex';
  }

  function closeDoctorModal() {
    document.getElementById('doctorModal').style.display = 'none';
  }
</script>
