<?php require_once(APPMOD.'psb/proses.php');
require_once(APPMOD.'psb/kelompok.php');
/* Load App libraries */
require_once(DBFILE);
require_once(LIBDIR.'common.php');
require_once(MODDIR.'date.php');
function kelompok_r(&$a,$b="",$s=0){
	$res=Array(); $in=false; $d=0;
	//if($c==1)$res['-']='Pilih kelompok:';
	if($s==1)$res[0]='- Semua -';
	$sql="SELECT * FROM  psb_kelompok".($b!=""?" WHERE proses='$b'":""." ORDER BY kelompok");
	$t=mysql_query($sql); while($r=mysql_fetch_array($t)){
		$res[$r['replid']]=$r['kelompok'];
		if($d==0)$d=$r['replid']; if($r['replid']==$a)$in=true;
	}
	if(!$in)$a=$s==1?0:$d;
	return $res;
}


$dept=gpost('departemen');
$departemen=departemen_r($dept);
$pros=gpost('proses');
$proses=proses_r($pros,$dept);
$kel=gpost('kelompok');
$kelompok=kelompok_r($kel,$pros);

$cid=gets('token');


$query = mysql_query("SELECT * FROM psb_calonsiswa ");
//$query = mysql_query("SELECT * FROM psb_calonsiswa WHERE proses='$pros' AND kelompok='$kel' ORDER BY nopendaftaran");


$token=doc_decrypt($token);

$doc=new doc();
$doc->dochead('Pendataan Calon Siswa',100);
//$doc->nl();

//$doc->row_blank(5);

//$t=dbQSql($token);
$no=1;
$doc->head('@Nomor Pendaftaran{2}','@Nama{2}','@Uang Pangkal{R,2}','Discount{C,1,3}','Denda{R,2}','Uang pangkal net{R,2,90px}','Angsuran{R}');
$doc->head('Subsidi{R}','Saudara{R}','Tunai{R}','!x bulan{R}');

while($r=dbFA($query)){

$doc->nl();
//$doc->cell($no++,20,'c');
$doc->cell($r['nopendaftaran'],90,'r');
$doc->cell($r['nama']);
$doc->cell(fRp($r['sumpokok']),90,'r');
$doc->cell(fRp($r['disctb']),90,'r');
$doc->cell(fRp($r['discsaudara']),90,'r');
$doc->cell(fRp($r['disctunai']),90,'r');
$doc->cell(fRp($r['denda']),90,'r');
$doc->cell(fRp($r['angsuran']).'<br/>x '.$r['jmlangsur'].' bulan',90,'r');

}

$doc->end(); ?>