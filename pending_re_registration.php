<?php
require 'config/database.php';
include 'base/header.php';

/* Retrieve applicants who have NOT completed re-registration */
$data = $conn->query("
    SELECT p.nama, p.tanggal_daftar, p.hari, p.jam
    FROM pendaftaran p
    LEFT JOIN daftar_ulang du ON du.pendaftaran_id = p.id
    WHERE du.id IS NULL
    ORDER BY p.tanggal_daftar ASC
")->fetchAll();
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Applicants Pending Re-Registration</h5>
    </div>

    <div class="card-body">

        <?php if (count($data) === 0): ?>
            <div class="alert alert-success mb-0">
                All applicants have completed re-registration.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['nama']) ?></td>
                                <td><?= $d['tanggal_daftar'] ?></td>
                                <td><?= $d['hari'] ?></td>
                                <td><?= $d['jam'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'base/footer.php'; ?>