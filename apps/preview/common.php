<?php
require XG_APPS.'/app/common.php';
function itemdata($item=[]){
	$rt='';
	foreach($item as $k=>$v){
		if(xg_in_array($k,['id','cid','page']))$rt.=' data-'.$k.'="'.$v.'"';
	}
	return $rt;
}
function navthis($item=[]){
	unset($item['order'],$item['key'],$item['title']);
	foreach($item as $k=>$v){
		if($k=='id'){
			if($v!=xg_input('get.id')){
				return false;
			}elseif($v!=xg_input('get.cid')){
				return false;
			}
		}elseif($k=='cid'){
			if($v!=xg_input('get.cid')){
				return false;
			}
		}
	}
	return true;
}
function menuthis($link){
	$link=menulink($link);
	if(strpos(xg('url'),$link)===false)return false;
	return true;
}
function menulink($link){
	$thid=xg_input('thid');
	if(strpos($link,'?')===false){
		return '?thid='.$thid.'&pagename='.$link;
	}else{
		return '?thid='.$thid.'&pagename='.substr($link,0,strpos($link,'?')).'&'.substr($link,strpos($link,'?')+1);
	}
}

function sets(){
	return xg_sets();
}
function listbottom($item,$v,$name){
	return xg_listbottom($item,$v,$name);
}
?>