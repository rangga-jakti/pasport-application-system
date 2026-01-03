<?php
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml('<h1>DOMPDF OK</h1>');
$dompdf->render();
$dompdf->stream('test.pdf', ['Attachment' => false]);
