<?php
require 'config/database.php';
include 'base/header.php';

$data = $conn->query("
    SELECT 
        du.id,
        p.nama,
        du.ktp,
        du.kk,
        du.ijazah
    FROM daftar_ulang du
    JOIN pendaftaran p ON du.pendaftaran_id = p.id
    WHERE du.keterangan = 'Tidak'
")->fetchAll();
?>

<h4 class="fw-bold text-danger mb-3">Rejected Documents</h4>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-striped align-middle mb-0">
            <thead class="table-danger">
                <tr>
                    <th>Name</th>
                    <th>ID Card</th>
                    <th>Family Card</th>
                    <th>Certificate</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data) > 0): ?>
                    <?php foreach ($data as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['nama']) ?></td>
                            <td><?= $d['ktp'] ?></td>
                            <td><?= $d['kk'] ?></td>
                            <td><?= $d['ijazah'] ?></td>
                            <td class="text-center">
                                <a href="edit_document.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning">
                                    Review / Fix
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No rejected documents found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'base/footer.php'; ?>