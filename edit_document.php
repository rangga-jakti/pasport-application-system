<?php
require 'config/database.php';
include 'base/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: rejected_documents.php");
    exit;
}

/* ===============================
   FETCH DOCUMENT DATA
================================ */
$stmt = $conn->prepare("
    SELECT du.*, p.nama
    FROM daftar_ulang du
    JOIN pendaftaran p ON du.pendaftaran_id = p.id
    WHERE du.id = :id
");
$stmt->execute(['id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<div class='alert alert-danger'>Rejected document data not found.</div>";
    include 'base/footer.php';
    exit;
}

/* ===============================
   UPDATE PROCESS
================================ */
if (isset($_POST['save'])) {

    $ktp    = $_POST['ktp'];
    $kk     = $_POST['kk'];
    $ijazah = $_POST['ijazah'];

    if ($ktp === 'Ada' && $kk === 'Ada' && $ijazah === 'Ada') {
        $no_antrian = $conn->query("
            SELECT IFNULL(MAX(no_antrian),0)+1
            FROM daftar_ulang
            WHERE no_antrian IS NOT NULL
        ")->fetchColumn();
        $keterangan = 'OK';
    } else {
        $keterangan = 'Tidak';
        $no_antrian = null;
    }

    $update = $conn->prepare("
        UPDATE daftar_ulang SET
            ktp = :ktp,
            kk = :kk,
            ijazah = :ijazah,
            keterangan = :keterangan,
            no_antrian = :no_antrian
        WHERE id = :id
    ");

    $update->execute([
        'ktp'        => $ktp,
        'kk'         => $kk,
        'ijazah'     => $ijazah,
        'keterangan' => $keterangan,
        'no_antrian' => $no_antrian,
        'id'         => $id
    ]);

    echo "<script>
        alert('Document updated successfully.');
        window.location.href='rejected_documents.php';
    </script>";
    exit;
}
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-warning">
        <strong>Edit Rejected Document</strong>
    </div>

    <div class="card-body">
        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Applicant Name</label>
                <input type="text"
                    class="form-control"
                    value="<?= htmlspecialchars($data['nama']) ?>"
                    disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">ID Card (KTP)</label>
                <select name="ktp" class="form-select" required>
                    <option value="Ada" <?= $data['ktp'] === 'Ada' ? 'selected' : '' ?>>Available</option>
                    <option value="Tidak" <?= $data['ktp'] === 'Tidak' ? 'selected' : '' ?>>Not Available</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Family Card (KK)</label>
                <select name="kk" class="form-select" required>
                    <option value="Ada" <?= $data['kk'] === 'Ada' ? 'selected' : '' ?>>Available</option>
                    <option value="Tidak" <?= $data['kk'] === 'Tidak' ? 'selected' : '' ?>>Not Available</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Certificate / Birth Record</label>
                <select name="ijazah" class="form-select" required>
                    <option value="Ada" <?= $data['ijazah'] === 'Ada' ? 'selected' : '' ?>>Available</option>
                    <option value="Tidak" <?= $data['ijazah'] === 'Tidak' ? 'selected' : '' ?>>Not Available</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="save" class="btn btn-success">
                    Save Changes
                </button>

                <a href="rejected_documents.php" class="btn btn-secondary">
                    Back
                </a>
            </div>

        </form>
    </div>
</div>

<?php include 'base/footer.php'; ?>