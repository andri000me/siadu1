/* Callbacks */
function pageCallbacks(){
	
	PCBCODE=0;
}
/*************** Halaman-halaman ***************/
/** Halaman user **/

function user_get(){
	gPage("user");
}
function user_form(o,cid,g){
	var f=[['uname','Username'],['level','Level'],['app','Modul'],['departemen','Departemen']];
	fform_std(o,cid,g,"user",user_get,f);
}

function user_print(){
	print_fmod('user',['departemen']);
}