<?php
session_start();
$ssid=session_id()

require_once('../../shared/config.php');
require_once('../system/config.php');
require_once(DBFILE);
require_once(LIBDIR.'common.php');
require_once(MODDIR.'date.php');

require_once(LIBDIR.'tcpdf/config/lang/eng.php');
require_once(LIBDIR.'tcpdf/tcpdf.php');
define('mydeffont','dejavusans');

// create new PDF document
$pori=gets('cetak_orientasi');
if($pori=='') $pori=PDF_PAGE_ORIENTATION;
$psize=gets('cetak_ukuran');
if($psize=='') $psize=PDF_PAGE_FORMAT;
$pdf = new TCPDF($pori, PDF_UNIT, $psize, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Elyon School - SIADU');
$pdf->SetTitle('Cetak Barcode');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//Array('F4'=>'F4 &nbsp; 210x330mm','A4'=>'A4 &nbsp; 210x297mm')
$paper=Array();
$sp=$psize.$pori;
$paper['F4P']=Array(210,330);
$paper['F4L']=Array(330,210);
$paper['A4P']=Array(210,297);
$paper['A4L']=Array(297,210);
$paper['A5P']=Array(148,210);
$paper['A5L']=Array(210,148);
/* Page Setup */
$dcMarginT=10;
$dcMarginB=10;
$dcMarginR=10;
$dcMarginL=10;
$dcPaperW=$paper[$sp][0];
$dcPaperH=$paper[$sp][1];
$dcPageW=$dcPaperW-$dcMarginR-$dcMarginL;
$dcPageH=$dcPaperH-$dcMarginT-$dcMarginB;
$dcMarginRX=$dcPaperW-$dcMarginR;

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print a table

/* dc_YDown() */
function dc_YDown($a=0){
	global $pdf;
	$pdf->SetY($pdf->GetY()+$a); // Line break 2mm
	return $pdf->GetY();
}

// set JPEG quality
$pdf->setJPEGQuality(75);

// Image method signature:
// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

// set font
$pdf->SetFont('dejavusans', '', 11, '', true);

// set cell padding
$pdf->setCellPaddings(0, 0, 0, 0.5); //$left='', $top='', $right='', $bottom='')

// set cell margins
$pdf->setCellMargins(0, 0, 0, 0);

// set color for background
$pdf->SetFillColor(255, 255, 255);

$token=explode("-",gets('token'));
$dept=$token[0];
$pros=$token[1];
$kel=$token[2];

if($dept==$PSB_ADMIN_DEPT||$PSB_ADMIN_DEPT==0){
$t=mysql_query("SELECT * FROM  `departemen` WHERE replid='$dept'");
if(mysql_num_rows($t)>0){
$departemen=mysql_fetch_array($t);

// Queries:
$t=mysql_query("SELECT * FROM psb_calonsiswa WHERE proses='$pros' AND kelompok='$kel' ORDER BY nopendaftaran");

// add a page
$pdf->AddPage();
page_header();
require_once('header.php');

$pdf->SetFont(mydeffont, '', 12, '', true);
$pdf->MultiCell($dcPageW, 0, 'DATA CALON SISWA BARU', 0, 'C', 0, 1, '', '', true);
dc_YDown(3);

if(mysql_num_rows($t)>0){

$pdf->SetFont(mydeffont, '', 8, '', true);

$pdf->MultiCell(30, 0, 'Departemen', 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(100, 0, ': '.$departemen['departemen'], 0, 'L', 0, 1, '', '', true);
$pdf->MultiCell(30, 0, 'Proses Penerimaan', 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(100, 0, ': '.proses_name($pros), 0, 'L', 0, 1, '', '', true);
$pdf->MultiCell(30, 0, 'Kelompok', 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(100, 0, ': '.kelompok_name($kel), 0, 'L', 0, 1, '', '', true);

dc_YDown(2);
$thx=Array('No.','No Daftar','Nama','Uang Pangkal','Subsidi','Saudara','Tunai','Denda','Uang Pangkal Net','Angsuran','#1','#2','#3','Status');
$twx=Array(   11,         25,     0,            23,       23,       23,     16,    23,                 23,        23,  10,  10, 10,       15);
$tax=Array(  'C',        'L',   'L',           'C',      'C',      'C',    'C',   'C',                'C',       'C', 'C', 'C','C',      'L');

$tx=0;
for($i=0;$i<count($twx);$i++){
	$tx+=$twx[$i];
}
$twx[2]=$dcPageW-$tx;
$tcx=Array();
$tcx[0]=$dcMarginL;
for($i=1;$i<count($twx);$i++){
	$tcx[$i]=$tcx[$i-1]+($twx[$i-1]);
}
$pdf->SetFont(mydeffont, '', 8, '', true);
$pdf->setCellPaddings(1, 1, 1, 1);

// Table head
$pdf->SetTextColor(255);
$pdf->SetFillColor(80);
$cy=$pdf->GetY();

$pdf->setCellPaddings(1, 4, 1, 1);
$i=0; $pdf->MultiCell($twx[$i], 12, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy, true);
$pdf->setCellPaddings(1, 1, 1, 1);
$i++; $pdf->MultiCell($twx[$i], 6, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy, true);
      $pdf->MultiCell($twx[$i], 6, 'No Formulir', 0, $tax[$i], 1, 0, $tcx[$i], $cy+6, true);
$pdf->setCellPaddings(1, 4, 1, 1);
$i++; $pdf->MultiCell($twx[$i], 12, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy, true);
$pdf->setCellPaddings(1, 2, 1, 1);
$i++; $pdf->MultiCell($twx[$i], 12, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy, true);
$pdf->setCellPaddings(1, 1, 1, 1);
$i++; $pdf->MultiCell($twx[$i], 6, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy+6, true); $ti=$i;
$i++; $pdf->MultiCell($twx[$i], 6, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy+6, true);
$i++; $pdf->MultiCell($twx[$i], 6, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy+6, true);
	  $pdf->MultiCell($twx[$ti]+$twx[$ti+1]+$twx[$ti+2], 6, 'Discount', 0, 'C', 1, 0, $tcx[$ti], $cy, true);
$pdf->setCellPaddings(1, 4, 1, 1);
$i++; $pdf->MultiCell($twx[$i], 12, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy, true);
$pdf->setCellPaddings(1, 2, 1, 1);
$i++; $pdf->MultiCell($twx[$i], 12, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy, true);
$pdf->setCellPaddings(1, 1, 1, 1);
$i++; $pdf->MultiCell($twx[$i], 6, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy, true);
      $pdf->MultiCell($twx[$i], 6, 'x bulan', 0, $tax[$i], 1, 0, $tcx[$i], $cy+6, true);
$i++; $pdf->MultiCell($twx[$i], 6, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy+6, true); $ti=$i;
$i++; $pdf->MultiCell($twx[$i], 6, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy+6, true);
$i++; $pdf->MultiCell($twx[$i], 6, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy+6, true);
	  $pdf->MultiCell($twx[$ti]+$twx[$ti+1]+$twx[$ti+2], 6, 'Nilai Ujian', 0, 'C', 1, 0, $tcx[$ti], $cy, true);
$pdf->setCellPaddings(1, 4, 1, 1);
$i++; $pdf->MultiCell($twx[$i], 12, $thx[$i], 0, $tax[$i], 1, 0, $tcx[$i], $cy, true);

$pdf->Line($dcMarginL,$cy,$dcMarginRX,$cy);
$pdf->Line($dcMarginL,$cy+12,$dcMarginRX,$cy+12);
for($i=0;$i<count($tcx);$i++){
	if($i!=5&&$i!=6&&$i!=11&&$i!=12)
	$pdf->Line($tcx[$i],$cy,$tcx[$i],$cy+12);
	else
	$pdf->Line($tcx[$i],$cy+6,$tcx[$i],$cy+12);
}
$pdf->Line($tcx[1],$cy+6,$tcx[2],$cy+6);
$pdf->Line($tcx[4],$cy+6,$tcx[7],$cy+6);
$pdf->Line($tcx[10],$cy+6,$tcx[13],$cy+6);
$pdf->Line($dcMarginRX,$cy,$dcMarginRX,$cy+12);

$pdf->SetY($cy+12);
$pdf->SetTextColor(0);
// End of table head

// Table body formatting
$pdf->SetFont(mydeffont, '', 8, '', true);
$pdf->setCellPaddings(1, 1, 1, 1);

$row=1;

while($r=dbFA($t)){ $i=0;
	if($pdf->GetY()>180) {
		$pdf->AddPage();
		page_header();
		
		// Table head
		$pdf->SetTextColor(255);
		$pdf->SetFillColor(0);
		for($i=0;$i<count($thx);$i++){
			$pdf->MultiCell($twx[$i], 0, $thx[$i], 1, $tax[$i], 1, 0, '', '', true);
		}
		$pdf->Ln();
		$pdf->SetTextColor(0);
		// End of table head
		//dc_YDown(2);
	}
	
	// Row data:	
	$ny=$pdf->GetY();
	$my=0; $i=0;
	$pdf->MultiCell($twx[$i], 0, $row++, 0, $tax[$i++], 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH();
	
	$pdf->MultiCell($twx[$i], 0, $r['nopendaftaran'].$lnbr.$r['noformulir'], 0, $tax[$i++], 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH();
	
	$pdf->MultiCell($twx[$i], 0, $r['nama'], 0, $tax[$i++], 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH();
	
	$pdf->MultiCell($twx[$i], 0, fRp($r['sumpokok']), 0, 'R', 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH(); $i++;
	
	$pdf->MultiCell($twx[$i], 0, fRp($r['disctb']), 0, 'R', 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH(); $i++;
	
	$pdf->MultiCell($twx[$i], 0, fRp($r['discsaudara']), 0, 'R', 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH(); $i++;
	
	$pdf->MultiCell($twx[$i], 0, $r['disctunai'].' %', 0, 'R', 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH(); $i++;
	
	$pdf->MultiCell($twx[$i], 0, fRp($r['denda']), 0, 'R', 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH(); $i++;
	
	$pdf->MultiCell($twx[$i], 0, fRp($r['sumnet']), 0, 'R', 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH(); $i++;

	$pdf->MultiCell($twx[$i], 0, fRp($r['angsuran']).$lnbr.'x '.$r['jmlangsur'].' bulan', 0, 'R', 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH(); $i++;
	
	$pdf->MultiCell($twx[$i], 0, $r['ujian1'], 0, $tax[$i++], 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH();
	
	$pdf->MultiCell($twx[$i], 0, $r['ujian2'], 0, $tax[$i++], 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH();
	
	$pdf->MultiCell($twx[$i], 0, $r['ujian3'], 0, $tax[$i++], 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH();
	
	$pdf->MultiCell($twx[$i], 0, ($r['aktif']=='1'?'Aktif':'Tidak aktif'), 0, $tax[$i++], 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH();
	
	$tx=$dcMarginL;
	$pdf->Line($tx,$ny,$tx,$ny+$my);
	//$tx=$tw[0]+$dcMarginL;
	for($l=0;$l<$i;$l++){
		$pdf->Line($tx+$twx[$l],$ny,$tx+$twx[$l],$ny+$my);
		$tx+=$twx[$l];
	}
	$pdf->Line($dcMarginL,$ny+$my,$tx,$ny+$my);
	$pdf->Ln();
	$pdf->setY($ny+$my);
}

$pdf->setCellPaddings(0,0,0,0);
$pdf->Ln();
// reset pointer to the last page
} else {
	$pdf->SetFont(mydeffont, '', 8, '', true);

	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();
	$pdf->MultiCell($dcPageW, 0, 'Tidak ada data calon siswa baru.', 0, 'C', 0, 1, '', '', true);
}
$pdf->lastPage();

// ---------------------------------------------------------
$pdf->Output('PSB Kriteria Calon Siswa.pdf', 'I');
} else echo "Dokumen tidak tersedia!";
} else echo "Dokumen tidak tersedia!";
?>