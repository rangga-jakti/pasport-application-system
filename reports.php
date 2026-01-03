<?php
require 'config/database.php';
require_once 'helpers/view_helper.php';
include 'base/header.php';

/* ===============================
   GLOBAL SEARCH
================================ */
$where = [];
$params = [];

if (!empty($_GET['q'])) {
    $where[] = "(p.nama LIKE :q OR p.no_registrasi LIKE :q)";
    $params['q'] = "%" . $_GET['q'] . "%";
}

/* ===============================
   SUMMARY TOTAL DATA
================================ */
$total_applications = $conn->query("SELECT COUNT(*) FROM pendaftaran")->fetchColumn();

$total_re_registration = $conn->query("SELECT COUNT(*) FROM daftar_ulang")->fetchColumn();

$pending_re_registration = $conn->query("
    SELECT COUNT(*) 
    FROM pendaftaran p
    LEFT JOIN daftar_ulang du ON du.pendaftaran_id = p.id
    WHERE du.id IS NULL
")->fetchColumn();

$total_approved = $conn->query("
    SELECT COUNT(*) FROM daftar_ulang WHERE keterangan = 'OK'
")->fetchColumn();

$total_rejected = $conn->query("
    SELECT COUNT(*) FROM daftar_ulang WHERE keterangan = 'Tidak'
")->fetchColumn();

$total_processing = $conn->query("
    SELECT COUNT(*) FROM pengurusan
")->fetchColumn();

$pending_processing = $conn->query("
    SELECT COUNT(*)
    FROM daftar_ulang du
    LEFT JOIN pengurusan pu ON pu.daftar_ulang_id = du.id
    WHERE du.keterangan = 'OK'
      AND du.no_antrian IS NOT NULL
      AND pu.id IS NULL
")->fetchColumn();

$total_revenue = $conn->query("
    SELECT IFNULL(SUM(pembayaran),0) FROM pengurusan
")->fetchColumn();

/* ===============================
   A. APPLICATION REPORT
================================ */
$sql_applications = "
    SELECT no_registrasi, nama, tanggal_daftar, hari, jam
    FROM pendaftaran p
";
if ($where) {
    $sql_applications .= " WHERE " . implode(" AND ", $where);
}
$sql_applications .= " ORDER BY tanggal_daftar DESC";

$stmt = $conn->prepare($sql_applications);
$stmt->execute($params);
$applications = $stmt->fetchAll();

/* ===============================
   DAY TRANSLATION (ID → EN)
================================ */
$days = [
    'Senin'   => 'Monday',
    'Selasa'  => 'Tuesday',
    'Rabu'    => 'Wednesday',
    'Kamis'   => 'Thursday',
    'Jumat'   => 'Friday',
    'Sabtu'   => 'Saturday',
    'Minggu'  => 'Sunday',
];


/* ===============================
   B. RE-REGISTRATION REPORT
================================ */
$sql_re_registration = "
    SELECT 
        p.no_registrasi,
        p.nama,
        du.ktp,
        du.kk,
        du.ijazah,
        du.keterangan,
        du.no_antrian
    FROM daftar_ulang du
    JOIN pendaftaran p ON du.pendaftaran_id = p.id
";
if ($where) {
    $sql_re_registration .= " WHERE " . implode(" AND ", $where);
}
$sql_re_registration .= " ORDER BY du.id DESC";

$stmt = $conn->prepare($sql_re_registration);
$stmt->execute($params);
$re_registration = $stmt->fetchAll();

/* ===============================
   C. PROCESSING REPORT
================================ */
$sql_processing = "
    SELECT 
        p.no_registrasi,
        p.nama,
        du.no_antrian,
        pg.pembayaran
    FROM pengurusan pg
    JOIN daftar_ulang du ON pg.daftar_ulang_id = du.id
    JOIN pendaftaran p ON du.pendaftaran_id = p.id
";
if ($where) {
    $sql_processing .= " WHERE " . implode(" AND ", $where);
}
$sql_processing .= " ORDER BY pg.id DESC";

$stmt = $conn->prepare($sql_processing);
$stmt->execute($params);
$processing = $stmt->fetchAll();
?>

<!-- ===============================
     SEARCH
================================ -->
<form method="GET" class="row g-2 mb-4">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control"
            placeholder="Search by name or registration number..."
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary">Search</button>
    </div>
</form>

<!-- ===============================
     SUMMARY
================================ -->
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Total Applications</small>
                <h4 class="fw-bold"><?= $total_applications ?></h4>
                <small class="text-muted">
                    <?= $pending_re_registration ?> pending re-registration
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Re-Registrations</small>
                <h4 class="fw-bold"><?= $total_re_registration ?></h4>
                <small class="text-success"><?= $total_approved ?> approved</small> ·
                <small class="text-danger"><?= $total_rejected ?> rejected</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Processing</small>
                <h4 class="fw-bold"><?= $total_processing ?></h4>
                <small class="text-warning">
                    <?= $pending_processing ?> pending
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Total Revenue</small>
                <h4 class="fw-bold text-success">
                    Rp <?= number_format($total_revenue, 0, ',', '.') ?>
                </h4>
            </div>
        </div>
    </div>

</div>

<!-- ===============================
     APPLICATION REPORT
================================ -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <strong>Application Report</strong>
    </div>
    <div class="card-body p-0 table-wrapper">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Registration No</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($applications): foreach ($applications as $a): ?>
                        <tr>
                            <td><?= $a['no_registrasi'] ?></td>
                            <td><?= htmlspecialchars($a['nama']) ?></td>
                            <td><?= $a['tanggal_daftar'] ?></td>
                            <td><?= $days[$a['hari']] ?? $a['hari'] ?></td>
                            <td><?= $a['jam'] ?></td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===============================
     RE-REGISTRATION REPORT
================================ -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning">
        <strong>Re-Registration Report</strong>
    </div>
    <div class="card-body p-0 table-wrapper">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Registration No</th>
                    <th>Name</th>
                    <th>ID Card</th>
                    <th>Family Card</th>
                    <th>Certificate</th>
                    <th>Status</th>
                    <th>Queue No</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($re_registration): foreach ($re_registration as $r): ?>
                        <tr>
                            <td><?= $r['no_registrasi'] ?></td>
                            <td><?= htmlspecialchars($r['nama']) ?></td>
                            <td><?= yesNo($r['ktp']) ?></td>
                            <td><?= yesNo($r['kk']) ?></td>
                            <td><?= yesNo($r['ijazah']) ?></td>
                            <td><?= statusBadge($r['keterangan']) ?></td>

                            <!-- 🔥 INI YANG KURANG -->
                            <td>
                                <?= $r['keterangan'] === 'OK' && $r['no_antrian']
                                    ? $r['no_antrian']
                                    : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No data available
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===============================
     PROCESSING REPORT
================================ -->
<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        <strong>Processing Report</strong>
    </div>
    <div class="card-body p-0 table-wrapper">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Registration No</th>
                    <th>Name</th>
                    <th>Queue No</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($processing): foreach ($processing as $p): ?>
                        <tr>
                            <td><?= $p['no_registrasi'] ?></td>
                            <td><?= htmlspecialchars($p['nama']) ?></td>
                            <td><?= $p['no_antrian'] ?></td>
                            <td>Rp <?= number_format($p['pembayaran'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">No data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<a href="export_report_pdf.php?q=<?= urlencode($_GET['q'] ?? '') ?>"
    target="_blank"
    class="btn btn-danger mt-3">
    Export PDF
</a>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h6 class="fw-bold mb-2">Report Description</h6>
        <p class="text-muted small mb-0">
            This report presents a summary of passport applications, re-registrations,
            and processing activities based on the data stored in the system.
        </p>
    </div>
</div>

<?php include 'base/footer.php'; ?>