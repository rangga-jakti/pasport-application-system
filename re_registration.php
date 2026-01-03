<?php
require 'config/database.php';
require_once 'helpers/view_helper.php';
include 'base/header.php';

/* ===============================
   FEEDBACK
================================ */
$success = false;
$error   = '';

/* ===============================
   APPLICANT DATA (NOT RE-REGISTERED YET)
================================ */
$data_pendaftaran = $conn->query("
    SELECT p.id, p.no_registrasi, p.nama
    FROM pendaftaran p
    LEFT JOIN daftar_ulang du ON du.pendaftaran_id = p.id
    WHERE du.id IS NULL
    ORDER BY p.id DESC
")->fetchAll();

/* ===============================
   FILTER + SEARCH STATUS
================================ */
$where  = [];
$params = [];

if (!empty($_GET['q'])) {
    $where[] = "(p.nama LIKE :q OR p.no_registrasi LIKE :q)";
    $params['q'] = '%' . $_GET['q'] . '%';
}

if (isset($_GET['status']) && $_GET['status'] !== '') {
    if ($_GET['status'] === 'NULL') {
        $where[] = "du.keterangan IS NULL";
    } else {
        $where[] = "du.keterangan = :status";
        $params['status'] = $_GET['status'];
    }
}

/* ===============================
   RE-REGISTRATION STATUS DATA
================================ */
$sql = "
    SELECT 
        p.no_registrasi,
        p.nama,
        du.ktp,
        du.kk,
        du.ijazah,
        du.keterangan,
        du.no_antrian
    FROM pendaftaran p
    LEFT JOIN daftar_ulang du ON du.pendaftaran_id = p.id
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY p.id DESC LIMIT 50";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$data_status = $stmt->fetchAll();

/* ===============================
   PROCESS RE-REGISTRATION SUBMISSION
================================ */
if (isset($_POST['simpan'])) {
    $pendaftaran_id = $_POST['pendaftaran_id'];
    $ktp    = $_POST['ktp'];
    $kk     = $_POST['kk'];
    $ijazah = $_POST['ijazah'];

    $cek = $conn->prepare("
        SELECT COUNT(*) FROM daftar_ulang
        WHERE pendaftaran_id = :id
    ");
    $cek->execute(['id' => $pendaftaran_id]);

    if ($cek->fetchColumn() > 0) {
        $error = "This applicant has already completed re-registration.";
    } else {

        if ($ktp === 'Ada' && $kk === 'Ada' && $ijazah === 'Ada') {
            $keterangan = 'OK';
            $no_antrian = $conn->query("
                SELECT IFNULL(MAX(no_antrian),0)+1 FROM daftar_ulang
            ")->fetchColumn();
        } else {
            $keterangan = 'Tidak';
            $no_antrian = null;
        }

        $stmt = $conn->prepare("
            INSERT INTO daftar_ulang
            (pendaftaran_id, ktp, kk, ijazah, keterangan, no_antrian)
            VALUES (:pid, :ktp, :kk, :ijazah, :ket, :no)
        ");

        $stmt->execute([
            'pid' => $pendaftaran_id,
            'ktp' => $ktp,
            'kk'  => $kk,
            'ijazah' => $ijazah,
            'ket' => $keterangan,
            'no'  => $no_antrian
        ]);

        $success = true;
    }
}
?>

<?php if ($success): ?>
    <div class="alert alert-success">
        Re-registration processed successfully.
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= $error ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning">
        <strong>Re-Registration Process</strong>
    </div>

    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Select Applicant</label>
                <select name="pendaftaran_id" class="form-select" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($data_pendaftaran as $d): ?>
                        <option value="<?= $d['id'] ?>">
                            <?= htmlspecialchars($d['no_registrasi']) ?> — <?= htmlspecialchars($d['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">ID Card (KTP)</label>
                    <select name="ktp" class="form-select" required>
                        <option>Ada</option>
                        <option>Tidak</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Family Card (KK)</label>
                    <select name="kk" class="form-select" required>
                        <option>Ada</option>
                        <option>Tidak</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Certificate / Birth Record</label>
                    <select name="ijazah" class="form-select" required>
                        <option>Ada</option>
                        <option>Tidak</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="simpan" class="btn btn-warning">
                Process Re-Registration
            </button>
        </form>
    </div>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control"
            placeholder="Search by name / registration number..."
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="OK">Approved</option>
            <option value="Tidak">Rejected</option>
            <option value="NULL">Not Reviewed</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary">Filter</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-header bg-light">
        <strong>Re-Registration Status</strong>
    </div>

    <div class="card-body p-0 table-wrapper">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
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
                <?php $no = 1;
                foreach ($data_status as $s): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($s['no_registrasi']) ?></td>
                        <td><?= htmlspecialchars($s['nama']) ?></td>
                        <td><?= isset($s['ktp']) ? yesNo($s['ktp']) : '-' ?></td>
                        <td><?= isset($s['kk']) ? yesNo($s['kk']) : '-' ?></td>
                        <td><?= isset($s['ijazah']) ? yesNo($s['ijazah']) : '-' ?></td>
                        <td><?= statusBadge($s['keterangan'] ?? null) ?></td>

                        <td>
                            <?= ($s['keterangan'] === 'OK' && !empty($s['no_antrian']))
                                ? $s['no_antrian']
                                : '-' ?>
                        </td>


                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'base/footer.php'; ?>