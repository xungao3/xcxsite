<?php
if(file_exists(XG_DATA.'/installed')){
	$sysdata=xg_model('sys')->where('status',1)->json('database')->cache('sys')->column('*','name');
	return xg_merge(['xg'=>['name'=>'xg','title'=>'讯高']],$sysdata);
}
return [];
?>