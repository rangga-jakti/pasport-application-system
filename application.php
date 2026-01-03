<?php
require 'config/database.php';
include 'base/header.php';

$success = null;

if (isset($_POST['simpan'])) {
    $nama    = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $jam     = $_POST['jam'];

    /* ===============================
       LIMIT TO 5 APPLICANTS PER DAY
    ================================ */
    $cek = $conn->prepare("
        SELECT COUNT(*) FROM pendaftaran 
        WHERE tanggal_daftar = :tanggal
    ");
    $cek->execute(['tanggal' => $tanggal]);
    $jumlah = $cek->fetchColumn();

    while ($jumlah >= 5) {
        $tanggal = date('Y-m-d', strtotime($tanggal . ' +1 day'));
        $cek->execute(['tanggal' => $tanggal]);
        $jumlah = $cek->fetchColumn();
    }

    /* ===============================
       DAY NAME (LOCALIZED)
    ================================ */
    $hari_map = [
        'Monday' => 'Monday',
        'Tuesday' => 'Tuesday',
        'Wednesday' => 'Wednesday',
        'Thursday' => 'Thursday',
        'Friday' => 'Friday',
        'Saturday' => 'Saturday',
        'Sunday' => 'Sunday'
    ];
    $hari = $hari_map[date('l', strtotime($tanggal))];

    /* ===============================
       GENERATE REGISTRATION NUMBER
    ================================ */
    $tahun = date('Y');

    $last = $conn->query("
        SELECT no_registrasi 
        FROM pendaftaran 
        WHERE no_registrasi LIKE 'REG-$tahun-%'
        ORDER BY id DESC 
        LIMIT 1
    ")->fetchColumn();

    $urut = $last ? ((int) substr($last, -4) + 1) : 1;
    $no_registrasi = 'REG-' . $tahun . '-' . str_pad($urut, 4, '0', STR_PAD_LEFT);

    /* ===============================
       SAVE DATA
    ================================ */
    $stmt = $conn->prepare("
        INSERT INTO pendaftaran 
        (no_registrasi, nama, tanggal_daftar, hari, jam)
        VALUES (:no, :nama, :tanggal, :hari, :jam)
    ");

    $stmt->execute([
        'no' => $no_registrasi,
        'nama' => $nama,
        'tanggal' => $tanggal,
        'hari' => $hari,
        'jam' => $jam
    ]);

    $success = [
        'no' => $no_registrasi,
        'hari' => $hari,
        'tanggal' => $tanggal,
        'jam' => $jam
    ];
}
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <strong>Passport Application</strong>
    </div>

    <div class="card-body">

        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>Application Submitted Successfully!</strong>
                <hr>
                <p><strong>Registration Number:</strong> <?= $success['no'] ?></p>
                <p><strong>Schedule:</strong> <?= $success['hari'] ?>,
                    <?= $success['tanggal'] ?> (<?= $success['jam'] ?>)
                </p>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Applicant Name</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Application Date</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Arrival Time</label>
                <input type="time" name="jam" class="form-control" required>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary">
                Save Application
            </button>
        </form>

    </div>
</div>

<?php include 'base/footer.php'; ?>