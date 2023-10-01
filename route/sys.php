<?php
if(file_exists(XG_DATA.'/installed')){
	$sys=xg_config('sys');
	if($sys){
		$sys=array_keys($sys);
		foreach($sys as $sysi){
			if(file_exists(XG_PATH.'/sys/'.$sysi.'/route.php'))require_once XG_PATH.'/sys/'.$sysi.'/route.php';
		}
	}
}
?>