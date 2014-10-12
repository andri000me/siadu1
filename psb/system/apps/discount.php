<?php 
	require_once(MODDIR.'fform/fform.php'); 
	$opt =gpost('opt');
	$cid =gpost('cid');
	if($cid=='')
		$cid=0;

	// form Module
	$fmod    = 'discount';
	$dbtable = 'psb_disctunai';
	$fform   = new fform($fmod,$opt,$cid,'Diskon tunai');
	// $inp     = app_form_gpost('nilai','keterangan');
	$inp     = app_form_gpost('nilai','nilai2','keterangan');

	if($opt=='a'||$opt=='u'||$opt=='d'){ 
		$q=false;
		if($opt=='a'){ // add
			$q=dbInsert($dbtable,$inp);
		}else if($opt=='u') { // edit
			$q=dbUpdate($dbtable,$inp,"replid='$cid'");
		}else if($opt=='d'){ // delete
			$q=dbDel($dbtable,"replid='$cid'");
		}$fform->notif($q);
	} else {
		if($opt=='uf'||$opt=='df'){ // Prepocessing form
			$r=dbSFA("*",$dbtable,"W/replid='$cid'");
		} else {
			
		}
		$fform->head();
		if($opt=='af' || $opt=='uf'){  // add n update form
			require_once(MODDIR.'control.php'); // Add or Edit form
			$fform->fi('Diskon (%)',iText('nilai',$r['nilai'],'width:40px').' &nbsp;%');
			$fform->fi('Diskon (Rupiah)',iText('nilai2',$r['nilai2']));
			$fform->fa('Keterangan',iTextarea('keterangan',$r['keterangan'],$fform->rwidths,3));
		} else if($opt=='df'){ // Delete form 
			$fform->dlg_del($r['nilai']);
		} $fform->foot();
	} 
?>