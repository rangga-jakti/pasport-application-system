<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Execute a prepared SQL statement to count queue data
 *
 * @param string $status_panggil
 * @return int
 */
function stmtSQLPrepared($status_panggil)
{
    global $conn;

    $stmt = $conn->prepare(
        "SELECT COUNT(id) AS total
         FROM tbl_antrian_obat
         WHERE status_panggil = :status"
    );
    $stmt->execute([':status' => $status_panggil]);

    return $stmt->fetchColumn();
}
