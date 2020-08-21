<?php
require_once('config.php');
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

$select_report = $trDB->query("SELECT report_id, report_number, date_from, date_to FROM report WHERE status=0 LIMIT ".(TESTMODE? 1 : 1000));
while ($select_report_result = $select_report->fetch_assoc()) {
    $report_id = $select_report_result["report_id"];
    $report_number = $select_report_result["report_number"];
    $date_from = new DateTime($select_report_result["date_from"]);
    $date_to = new DateTime($select_report_result["date_to"]);

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // set document information
    $pdf->SetCreator(FILE_AUTHOR);
    $pdf->SetAuthor(FILE_AUTHOR);
    $pdf->SetTitle(PROGRAMM_NAME);
    
    // remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetFont("Helvetica");
    
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);
    //$pdf->SetCellPadding(0);
    $pdf->setHtmlVSpace(array('p' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0))));
    $pdf->setCellHeightRatio(1.25);
    $pdf->setImageScale(0.47);
    
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    // add a page
    $pdf->AddPage();
    
    $html = '';
    include('generator_template.php');
     
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // reset pointer to the last page
    $pdf->lastPage();
    //Close and output PDF document
    $pdf->Output(GENERATOR_PATH .'/export/bericht_'.$report_number.'.pdf', (TESTMODE? 'I' : 'F'));

    if(!TESTMODE) {
        echo '<div>Report #'.$report_id.' exported</div>';
        $trDB->query("UPDATE report SET status=1 WHERE report_id=".$report_id);
    }
}

?>
<?php $trDB->close(); ?>