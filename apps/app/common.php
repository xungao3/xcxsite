<?php
function xg_sets($thid=0,$pagename=''){
	$app=xg_input('get.appname');
	$appsets=xg_model('app_list')->where(['status'=>1,'name'=>$app])->json('sets')->find();
	if(!$appsets)xg_jsonerr('没有此应用');
	if(!$thid)$thid=xg_input('get.thid');
	if(!$thid and $appsets)$thid=$appsets['theme_id'];
	if(!$thid)$thid=xg_model('app_theme')->where('status',1)->order('thid desc')->value('thid');
	static $sets=null;
	if(is_null($sets)){
		if($app!='preview')$cache=xg_cache("app-sets-{$app}-{$thid}");
		if($cache){
			$sets=$cache;
		}else{
			$syslist=xg_config('sys');
			$sets['pages']=xg_pages($thid);
			$sets['blocks']=xg_blocks($thid);
			$sets['links']=xg_links($thid);
			$sets['cates']=[];
			$sets['systems']=[];
			$sets['config']=xg_app_config();
			//$sets['hooks']=xg_apphooks();
			$sets['thid']=$thid;
			foreach($syslist as $sysname => $sys){
				$obj=xg_sys($sysname);
				if($obj){
					$sets['cates'][$sysname]=$obj->cates();
					$sets['systems'][$sysname]=$obj->sysinfo();
					if($sys_sets=$obj->sets($sets))$sets=xg_merge($sets,$sys_sets,true);
				}
			}
			if($hooks=xg_hooks('app-sets')->run($sets)->data()){
				foreach($hooks as $v){
					if($v and is_array($v))$sets=xg_merge($sets,$v,true);
				}
			}
			xg_cache("app-sets-{$app}-{$thid}",$sets,'app-sets');
		}
	}
	return $sets;
}
function xg_apphooks(){
	return xg_hooks('apphooks')->def([])->run()->last();
}
function xg_app_config(){
	$app=xg_input('appname');
	$data=xg_model('app_list')->where('name',$app)->json('data')->find();
	$data=xg_extend($data,$data['data']);
	foreach($data as $k=>$v){
		unset($data[$k]);
		$data[str_replace('-','_',$k)]=$v;
	}
	if(!$data['site_name'])$data['site_name']=xg_config('site.site-name');
	$data['route']=XG_ROUTE;
	return $data;
}
function xg_links($thid=''){
	if(!$thid)$thid=xg_input('get.thid');
	$data=xg_model('app_links')->where(array('thid'=>$thid))->json('data')->value('data');
	return $data;
}
function xg_pages($thid=''){
	if(!$thid)$thid=xg_input('get.thid');
	$pages=xg_model('app_page')->where(array('thid'=>$thid))->json('data')->column('show_title,type,name,title,pid,data','name');
	return $pages;
}
function xg_blocks_data($thid){
	static $data=[];
	if(!$data)$data=xg_model('app_block')->where('thid',$thid)->order('`order` asc')->json('data')->select();
	return $data;
}
function xg_blocks($thid=''){
	if(!$thid)$thid=xg_input('get.thid');
	$data=xg_blocks_data($thid);
	$blocks=[];
	foreach($data as $v){
		if(is_numeric($v['pagename']))continue;
		$v=xg_block($v,xg_input('request.'));
		$blocks[$v['pagename']][]=$v;
	}
	return $blocks;
}
function xg_blocks_items($block){
	$blocks=[];
	$data=xg_blocks_data($block['thid']);
	foreach($data as $v){
		if($v['pagename']!=$block['bid'] or $block['pagename']==$block['bid'])continue;
		$v=xg_block($v,xg_input('request.'));
		$blocks[]=$v;
	}
	return $blocks;
}
function xg_block($block,$param=[]){
	if(is_numeric($block))$block=xg_model('app_block')->json('data')->find($block);
	$data=$block;
	unset($data['data']);
	$fun=function($arr)use(&$fun){
		foreach($arr as $k=>$v){
			if(is_array($v))$v=$fun($v);
			$k2=str_replace('-','_',$k);
			unset($arr[$k]);
			$arr[$k2]=$v;
		}
		return $arr;
	};
	$block['data']=$fun($block['data']);
	$block=xg_merge($block['data'],$data);
	$block['sys']=$block['sys']?$block['sys']:'xg';
	$block['blocks']=xg_blocks_items($block);
	if($block['block']=='cate-boxes'){
		$recom=$block['recom_cate'];
		$cateids=xg_model('recom')->where(['type'=>'category','status'=>1,'recom'=>$recom])->order('`order` asc')->values('cateid');
		$block['cateids']=$cateids;
	}
	if($block['border']){
		if(strpos($block['border'],' ')===false){
			$block['border']='solid 1px '.$block['border'].'';
		}
	}
	return $block;
}
// function xg_def_theme(){
// 	static $theme;
// 	$itheme=xg_input('theme');
// 	if($itheme)$theme=$itheme;
// 	if(!$theme){
// 		//$theme=xg_model('app_list')->where(array('status'=>1))->value('thid');
// 	}
// 	if(!$theme){
// 		$theme=xg_model('app_theme')->where(array('status'=>1))->value('thid');
// 	}
// 	return $theme;
// }
function xg_app_sets($app='',$fields=''){
	$sets=xg_model('app_list')->where('name',$app)->json('data')->value('data');
	if($fields){
		$fields=xg_arr($fields);
		foreach($sets as $k=>$v){
			if(!in_array($k,$fields)){
				unset($sets[$k]);
			}
		}
	}
	return $sets;
}
?>