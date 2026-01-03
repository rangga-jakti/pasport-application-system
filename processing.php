<?php
require 'config/database.php';
include 'base/header.php';

/* ===============================
   FEEDBACK
================================ */
$success = false;
$error   = '';

/* ===============================
   SEARCH PROCESSED DATA
================================ */
$where  = [];
$params = [];

if (!empty($_GET['q'])) {
    $where[] = "(p.nama LIKE :q OR p.no_registrasi LIKE :q)";
    $params['q'] = '%' . $_GET['q'] . '%';
}

/* ===============================
   A. READY TO PROCESS DATA
================================ */
$siap_diproses = $conn->query("
    SELECT 
        du.id,
        p.no_registrasi,
        p.nama,
        du.no_antrian
    FROM daftar_ulang du
    JOIN pendaftaran p ON du.pendaftaran_id = p.id
    LEFT JOIN pengurusan pu ON pu.daftar_ulang_id = du.id
    WHERE du.keterangan = 'OK'
      AND du.no_antrian IS NOT NULL
      AND pu.id IS NULL
    ORDER BY du.no_antrian ASC
")->fetchAll();

/* ===============================
   B. PROCESS DOCUMENT
================================ */
if (isset($_POST['proses'])) {
    $daftar_ulang_id = $_POST['daftar_ulang_id'] ?? null;

    if (!$daftar_ulang_id) {
        $error = "Please select a document to process.";
    } else {
        try {
            $stmt = $conn->prepare("
                INSERT INTO pengurusan
                (daftar_ulang_id, status, pembayaran)
                VALUES (:id, 'Diterima', 355000)
            ");
            $stmt->execute(['id' => $daftar_ulang_id]);
            $success = true;
        } catch (PDOException $e) {
            $error = "This document has already been processed.";
        }
    }
}

/* ===============================
   C. PROCESSED DATA
================================ */
$sql = "
    SELECT 
        p.no_registrasi,
        p.nama,
        du.no_antrian,
        pu.pembayaran
    FROM pengurusan pu
    JOIN daftar_ulang du ON pu.daftar_ulang_id = du.id
    JOIN pendaftaran p ON du.pendaftaran_id = p.id
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY pu.id DESC LIMIT 50";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$sudah_diproses = $stmt->fetchAll();

/* ===============================
   D. TOTAL REVENUE
================================ */
$total = $conn->query("
    SELECT IFNULL(SUM(pembayaran),0) FROM pengurusan
")->fetchColumn();
?>

<?php if ($success): ?>
    <div class="alert alert-success">
        Document processed successfully.
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= $error ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-success text-white">
        <strong>Document Processing</strong>
    </div>

    <div class="card-body">

        <form method="POST" class="mb-4">
            <label class="form-label">Select Document to Process</label>

            <select name="daftar_ulang_id" class="form-select mb-3" required>
                <option value="">-- Select --</option>
                <?php foreach ($siap_diproses as $s): ?>
                    <option value="<?= $s['id']; ?>">
                        <?= htmlspecialchars($s['no_registrasi']); ?> —
                        <?= htmlspecialchars($s['nama']); ?>
                        (Queue #<?= $s['no_antrian']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="proses" class="btn btn-success">
                Process Document
            </button>
        </form>

        <?php if (count($siap_diproses) === 0): ?>
            <div class="alert alert-info">
                No documents are ready to be processed.
            </div>
        <?php endif; ?>

        <hr>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input
                    type="text"
                    name="q"
                    class="form-control"
                    placeholder="Search by name / registration number..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Search</button>
            </div>
        </form>

        <h6 class="fw-bold mb-2">Processed Documents</h6>

        <div class="table-wrapper">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Registration No</th>
                        <th>Name</th>
                        <th>Queue No</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sudah_diproses): ?>
                        <?php $no = 1;
                        foreach ($sudah_diproses as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($p['no_registrasi']); ?></td>
                                <td><?= htmlspecialchars($p['nama']); ?></td>
                                <td><?= $p['no_antrian']; ?></td>
                                <td>
                                    Rp <?= number_format($p['pembayaran'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No processed documents found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <hr>

        <h6>Total Revenue:</h6>
        <h4 class="text-success">
            Rp <?= number_format($total, 0, ',', '.'); ?>
        </h4>

    </div>
</div>

<?php include 'base/footer.php'; ?>