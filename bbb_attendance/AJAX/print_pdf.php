<?php

require_once('../../../config.php');

require_once($CFG->libdir.'/pdflib.php');
require_once($CFG->libdir.'/tcpdf/tcpdf.php');


 if(isset($_POST))
{
$pdf = new pdf();
$pdf->AddPage();
$pdf->WriteHTML('chahcha');
$pdf->Output('mypdf.pdf', 'D');

echo json_encode($pdf->Output('mypdf.pdf', 'D'));
} 

print_r('kvik');