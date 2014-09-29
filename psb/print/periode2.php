<?php
// 1. Parameter: sesuaikan dg parameter di Page Selection Bar >> Edit
$dept=gets('departemen');

// 2. Queries: samakan dg Query >> Edit
$t=mysql_query("SELECT * FROM psb_proses WHERE departemen='$dept'");


$pdf->AddPage();
page_header();
require_once('header.php');

// 3. Judul Halaman: Contoh format "DATA <nama halaman>" >> Edit
$JUDUL_HALAMAN = 'Data Periode Penerimaan Siswa Baru';
$pdf->SetFont(mydeffont, '', 12, '', true);
$pdf->MultiCell($dcPageW, 0, strtoupper($JUDUL_HALAMAN), 0, 'C', 0, 1, '', '', true);
dc_YDown(3);

if(mysql_num_rows($t)>0){

$pdf->SetFont(mydeffont, '', 8, '', true);

function cetak_infohalaman($label,$info){
	global $pdf;
	$pdf->MultiCell(30, 0, $label, 0, 'L', 0, 0, '', '', true);
	$pdf->MultiCell(100, 0, ': '.$info, 0, 'L', 0, 0, '', '', true);
	$pdf->Ln();
}

// 4. Cetak info halaman: Sesuaikan Page Selection Bar >> Edit
cetak_infohalaman('Departemen',departemen_name($dept));

dc_YDown(2);

// 5. Header tabel: samakan di $xtable->head() >> Edit
// $xtable->head('Periode penerimaan','Kode Awalan','Kapasitas~C','Calon Siswa~C','Siswa Diterima~C','status~C','keterangan');
$thx=Array('No.','Periode penerimaan','Kode Awalan','Kapasitas','Calon Siswa','Siswa Diterima','Status','Keterangan');

// 7. Alignment kolom: >> Edit
$tax=Array('C','L','L','C','C','C','C','L');

// 8. Lebar kolom: sesuaikan dg $xtable->td(..., lebar), rumus: lebar*$kc >> Edit
$kc=0.3;
$twx=Array( 11, 200*$kc, 120*$kc, 100*$kc, 100*$kc, 100*$kc, 80*$kc, 0);

$idx_o = 7; // 9. Index kolom yang lebar 0 (Lebar menyesuaikan) >> Edit

$tx=0;
for($i=0;$i<count($twx);$i++){
	$tx+=$twx[$i];
} $newline=chr(10);
$twx[$idx_o]=$dcPageW-$tx;
$pdf->SetFont(mydeffont, '', 8, '', true);
$pdf->setCellPaddings(1, 1, 1, 1);

// Table head
$pdf->setCellPaddings(1, 1, 1, 1);
$pdf->SetTextColor(255);
$pdf->SetFillColor(0);
for($i=0;$i<count($thx);$i++){
	$pdf->MultiCell($twx[$i], 0, $thx[$i], 1, $tax[$i], 1, 0, '', '', true);
}
$pdf->Ln();
$pdf->SetTextColor(0);
// End of table head

// Table body formatting
$pdf->SetFont(mydeffont, '', 8, '', true);
$pdf->setCellPaddings(1, 1, 1, 1);

$row=1;

function cetak_kolom($ik){
	global $pdf,$twx,$tax,$my,$i;
	$pdf->MultiCell($twx[$i], 0, $ik, 0, $tax[$i++], 0, 0, '', '', true);
	if($pdf->getLastH()>$my)$my=$pdf->getLastH();
}

while($r=dbFA($t)){ $i=0;
	if($pdf->GetY()>180) {
		$pdf->AddPage();
		page_header();
		
		$pdf->SetTextColor(255);
		$pdf->SetFillColor(0);
		for($i=0;$i<count($thx);$i++){
			$pdf->MultiCell($twx[$i], 0, $thx[$i], 1, $tax[$i], 1, 0, '', '', true);
		}
		$pdf->Ln();
		$pdf->SetTextColor(0);
	}
	
	$ny=$pdf->GetY();$my=0; $i=0;$pdf->MultiCell($twx[$i], 0, $row++, 0, $tax[$i++], 0, 0, '', '', true);if($pdf->getLastH()>$my)$my=$pdf->getLastH();
	
	// 10. Row data: >> Edit
	$q = mysql_query("SELECT replid FROM psb_calonsiswa WHERE proses = '".$r['replid']."'");
	$n = mysql_num_rows($q);
	$q = mysql_query("SELECT replid FROM psb_calonsiswa WHERE proses = '".$r['replid']."' AND status<>0");
	$n1 = mysql_num_rows($q);
	
	cetak_kolom($r['proses']);
	cetak_kolom($r['kodeawalan']);
	cetak_kolom($r['kapasitas']);
	cetak_kolom($n);
	cetak_kolom($n1);
	cetak_kolom(($r['aktif']=='1'?'Dibuka':'Ditutup'));
	cetak_kolom($r['keterangan']);
	
	// End of Row data (10): Udah :)
	
$tx=$dcMarginL;$pdf->Line($tx,$ny,$tx,$ny+$my);for($l=0;$l<$i;$l++){$pdf->Line($tx+$twx[$l],$ny,$tx+$twx[$l],$ny+$my);$tx+=$twx[$l];}$pdf->Line($dcMarginL,$ny+$my,$tx,$ny+$my);$pdf->Ln();$pdf->setY($ny+$my);
}

$pdf->setCellPaddings(0,0,0,0);
$pdf->Ln();
// reset pointer to the last page
} else {
	$pdf->SetFont(mydeffont, '', 8, '', true);

	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();
	$pdf->MultiCell($dcPageW, 0, 'Tidak ada data proses penerimaan calon siswa baru.', 0, 'C', 0, 1, '', '', true);
}
$pdf->lastPage();

// ---------------------------------------------------------
$pdf->Output(strtoupper(APID).' - '.$JUDUL_HALAMAN.'.pdf', 'I');
?>