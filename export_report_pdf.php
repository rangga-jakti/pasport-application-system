<?php
ob_start();

require 'config/database.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;

/* ===============================
   SEARCH FILTER
================================ */

$where = [];
$params = [];

if (!empty($_GET['q'])) {
    $where[] = "(p.nama LIKE :q OR p.no_registrasi LIKE :q)";
    $params['q'] = "%" . $_GET['q'] . "%";
}

/* ===============================
   SUMMARY
================================ */
$total_applications  = $conn->query("SELECT COUNT(*) FROM pendaftaran")->fetchColumn();
$total_re_registration = $conn->query("SELECT COUNT(*) FROM daftar_ulang")->fetchColumn();
$total_processing   = $conn->query("SELECT COUNT(*) FROM pengurusan")->fetchColumn();
$total_revenue      = $conn->query("SELECT IFNULL(SUM(pembayaran),0) FROM pengurusan")->fetchColumn();

/* ===============================
   APPLICATION DATA
================================ */
$sql = "SELECT no_registrasi, nama, tanggal_daftar FROM pendaftaran p";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY tanggal_daftar DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();

/* ===============================
   PROCESSING DATA
================================ */
$sql = "
SELECT p.no_registrasi, p.nama, pg.pembayaran
FROM pengurusan pg
JOIN daftar_ulang du ON pg.daftar_ulang_id = du.id
JOIN pendaftaran p ON du.pendaftaran_id = p.id
";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$processing = $stmt->fetchAll();

/* ===============================
   HTML PDF
================================ */
$html = "
<style>
body { font-family: DejaVu Sans; font-size: 11px; }
table { width:100%; border-collapse: collapse; margin-bottom:15px; }
th,td { border:1px solid #000; padding:4px; }
th { background:#eee; }
h2,h3 { text-align:center; }
</style>

<h2>PASSPORT APPLICATION REPORT</h2>

<p>
<b>Total Applications:</b> $total_applications<br>
<b>Total Re-Registrations:</b> $total_re_registration<br>
<b>Total Processing:</b> $total_processing<br>
<b>Total Revenue:</b> Rp " . number_format($total_revenue, 0, ',', '.') . "
</p>

<h3>Applications</h3>
<table>
<tr><th>No</th><th>Registration No</th><th>Name</th><th>Date</th></tr>";

$no = 1;
foreach ($applications as $a) {
    $html .= "<tr>
        <td>$no</td>
        <td>{$a['no_registrasi']}</td>
        <td>{$a['nama']}</td>
        <td>{$a['tanggal_daftar']}</td>
    </tr>";
    $no++;
}

$html .= "</table>

<h3>Processing</h3>
<table>
<tr><th>No</th><th>Registration No</th><th>Name</th><th>Payment</th></tr>";

$no = 1;
foreach ($processing as $p) {
    $html .= "<tr>
        <td>$no</td>
        <td>{$p['no_registrasi']}</td>
        <td>{$p['nama']}</td>
        <td>Rp " . number_format($p['pembayaran'], 0, ',', '.') . "</td>
    </tr>";
    $no++;
}

$html .= "</table>";

ob_end_clean();

/* ===============================
   GENERATE PDF
================================ */
$dompdf = new Dompdf(['defaultFont' => 'DejaVu Sans']);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("passport_report.pdf", ["Attachment" => false]);
exit;
