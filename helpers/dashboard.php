<?php
require 'config/database.php';

/* =================================================
   DASHBOARD KPI (HEADLINE)
================================================= */
$total_pendaftaran = $conn->query("SELECT COUNT(*) FROM pendaftaran")->fetchColumn();
$total_daftar_ulang = $conn->query("SELECT COUNT(*) FROM daftar_ulang")->fetchColumn();
$total_pengurusan  = $conn->query("SELECT COUNT(*) FROM pengurusan")->fetchColumn();
$total_pendapatan  = $conn->query("SELECT IFNULL(SUM(pembayaran),0) FROM pengurusan")->fetchColumn();

/* =================================================
   STATUS DATA
================================================= */

// Not re-registered yet
$belum_daftar_ulang = $conn->query("
    SELECT COUNT(*)
    FROM pendaftaran p
    LEFT JOIN daftar_ulang du ON du.pendaftaran_id = p.id
    WHERE du.id IS NULL
")->fetchColumn();

// Approved but not yet processed
$belum_pengurusan = $conn->query("
    SELECT COUNT(*)
    FROM daftar_ulang du
    LEFT JOIN pengurusan pu ON pu.daftar_ulang_id = du.id
    WHERE du.keterangan = 'OK'
      AND du.no_antrian IS NOT NULL
      AND pu.id IS NULL
")->fetchColumn();

// ❗ REJECTED BUT CAN BE CORRECTED
$ditolak_perlu_perbaikan = $conn->query("
    SELECT COUNT(*)
    FROM daftar_ulang du
    LEFT JOIN pengurusan pu ON pu.daftar_ulang_id = du.id
    WHERE du.keterangan = 'Tidak'
      AND pu.id IS NULL
")->fetchColumn();

/* =================================================
   PROGRESS
================================================= */
$progress_daftar_ulang = $total_pendaftaran > 0
    ? round(($total_daftar_ulang / $total_pendaftaran) * 100)
    : 0;

$progress_pengurusan = $total_daftar_ulang > 0
    ? round(($total_pengurusan / $total_daftar_ulang) * 100)
    : 0;
?>

<!-- ===============================
     HEADER
================================ -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Dashboard</h4>
        <small class="text-muted">Welcome to the Passport Application System</small>
    </div>
    <span class="badge bg-primary px-3 py-2">Rangga System</span>
</div>

<!-- ===============================
     ALERTS
================================ -->
<?php if ($belum_daftar_ulang > 0): ?>
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <div>
            <strong>Attention Required</strong><br>
            <?= $belum_daftar_ulang ?> applicants have not completed re-registration
        </div>
        <a href="pending_re_registration.php" class="btn btn-info btn-sm">View List</a>
    </div>
<?php endif; ?>

<?php if ($belum_pengurusan > 0): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <div>
            <strong>Action Required</strong><br>
            <?= $belum_pengurusan ?> documents are ready to be processed
        </div>
        <a href="processing.php" class="btn btn-warning btn-sm">Process Now</a>
    </div>
<?php endif; ?>

<?php if ($ditolak_perlu_perbaikan > 0): ?>
    <div class="alert alert-danger d-flex justify-content-between align-items-center">
        <div>
            <strong>Rejected Documents</strong><br>
            <?= $ditolak_perlu_perbaikan ?> documents require correction
        </div>
        <a href="rejected_documents.php" class="btn btn-danger btn-sm">View Documents</a>
    </div>
<?php endif; ?>

<!-- ===============================
     KPI SUMMARY
================================ -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Total Applications</small>
                <h3 class="fw-bold"><?= $total_pendaftaran ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Re-Registrations</small>
                <h3 class="fw-bold"><?= $total_daftar_ulang ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Processed Files</small>
                <h3 class="fw-bold text-success"><?= $total_pengurusan ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Total Revenue</small>
                <h5 class="fw-bold text-success">
                    Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
                </h5>
            </div>
        </div>
    </div>
</div>

<!-- ===============================
     QUICK ACTIONS
================================ -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <a href="application.php" class="text-dark">
            <div class="card shadow-sm border-0 h-100 action-card">
                <div class="card-body">
                    <h6 class="text-primary fw-bold">Application</h6>
                    <h5>Data Entry</h5>
                    <p class="text-muted mb-0">Initial applicant data registration.</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="re_registration.php" class="text-dark">
            <div class="card shadow-sm border-0 h-100 action-card">
                <div class="card-body">
                    <h6 class="text-warning fw-bold">Re-Registration</h6>
                    <h5>Document Validation</h5>
                    <p class="text-muted mb-0">Applicant document verification.</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="Processing.php" class="text-dark">
            <div class="card shadow-sm border-0 h-100 action-card">
                <div class="card-body">
                    <h6 class="text-success fw-bold">Processing</h6>
                    <h5>Payment</h5>
                    <p class="text-muted mb-0">Final stage of the application process.</p>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- ===============================
     FOOTER NOTES
================================ -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h6 class="fw-bold">System Notes</h6>
        <ul class="text-muted small mb-0">
            <li>This system is developed as an academic simulation of a real-world passport application workflow.</li>
            <li>Created for final academic assessment and as part of a professional backend development portfolio.</li>
            <li>All data displayed within the system is simulated (dummy data) for demonstration purposes only.</li>

        </ul>
    </div>
</div>

<p class="text-muted small text-center mt-4">
    © Passport Application System — Academic Simulation
</p>