<?php
	$fmod   ='discount';
	$xtable =new xtable($fmod);
	$xtable->btnbar_f('add');
	// Query
	$t     	=mysql_query("SELECT * FROM psb_disctunai ORDER BY nilai");
	$xtable->ndata=mysql_num_rows($t);
	if($xtable->ndata>0){
		// Table head
		$xtable->head('Diskon (%)~C','Diskon (Tunai)~C','Keterangan~C');
		while($r=mysql_fetch_array($t)){
			$xtable->row_begin();
			$xtable->td($r['nilai'],100,'c');
			
			$xtable->td(fRp($r['nilai2']),100);

			// $xtable->td($r['nilai2']);
			$xtable->td($r['keterangan']);
			$xtable->opt_ud($r['replid']);
			$xtable->row_end();
		}$xtable->foot();
	}else{
		$xtable->nodata();
	}
?>