<?php require_once(APPMOD.'psb/proses.php');
/* Load App libraries */
require_once(DBFILE);
require_once(LIBDIR.'common.php');
require_once(MODDIR.'date.php');
define('IMGDIR',ROTDIR.'images/');

$dept=gpost('departemen');
$departemen=departemen_r($dept);
$proses=proses_r($pros,$dept);
// cell($a,$w=0,$c=1,$r=1,$al='',$b=-1,$bg='',$s='',$atr='')
// $cid=gets('token');



// $query = mysql_query("SELECT * FROM psb_calonsiswa WHERE replid='$cid' LIMIT 0,1");

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
	<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<td>No</td>
			<td>No Jurnal</td>
			<td>Tanggal</td>
			<td>Kode Akun</td>
			<td>Nama Akun</td>
			<td>Keterangan</td>
			<td>Debet</td>
			<td>Kredit</td>
		</tr>
		
	</table>
</body>
</html>

<?php
$out = ob_get_contents();
ob_end_clean();
include("../../libraries/mpdf/mpdf.php");
$mpdf = new mPDF('c','Legal','');
$mpdf->SetDisplayMode('fullpage');
$stylesheet = file_get_contents('../../libraries//mpdf/mpdf.css');
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($out);
$mpdf->Output();

/*
$token=doc_decrypt($token);

$proses=mysql_fetch_array(mysql_query("SELECT * FROM psb_proses WHERE replid='".$r['proses']."' LIMIT 0,1"));
$kelompok=mysql_fetch_array(mysql_query("SELECT * FROM psb_kelompok WHERE replid='".$r['kelompok']."' LIMIT 0,1"));
$departemen=mysql_fetch_array(mysql_query("SELECT * FROM departemen WHERE replid='".$proses['departemen']."' LIMIT 0,1"));

$doc=new doc();
$doc->dochead("Data Calon Siswa ".gets('kelompok'),9);
$doc->n1();
$doc->cell('<b>Proses Penerimaan</b>',100,'',2);
$doc->cell('<b>: '.$proses['proses'].'</b>',0,'',4);


//$t=dbQSql($token);
$no=1;
$doc->head('No{C}','@Periode Penerimaan','@Kode Awalan','@Angkatan','@Kapasitas','@Calon Siswa','@Siswa diterima','@Status','Keterangan');

while($r=dbFA($query)){

		$q = mysql_query("SELECT replid FROM psb_calonsiswa WHERE proses = '".$r['replid']."'");
		$n = mysql_num_rows($q);
		$q = mysql_query("SELECT replid FROM psb_calonsiswa WHERE proses = '".$r['replid']."' AND status<>0");
		$n1 = mysql_num_rows($q);
		
$doc->nl();
$doc->cell($no++,20,'c');
$doc->cell($r['proses'],80);
$doc->cell($r['kodeawalan'],30);
$doc->cell($r['angkatan'],30);
$doc->cell($r['kapasitas'],50);
$doc->cell($n,50);
$doc->cell($n1,50);
$doc->cell(($r['aktif']=='1'?'<span style="color:#00A000"><b>Dibuka</b></span>':'Ditutup'),50);
}$doc->cell(($r['keterangan']),50);

$doc->end(); */

?>