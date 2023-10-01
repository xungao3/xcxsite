<?php
namespace addons\wangeditor\controller;
class upload{
	use \xg\traits\file;
	function image(){
		$this->isimg=true;
		$res=$this->doupload();
		if($res['ok']){
			xg_jsonmsg(['errno'=>0,'data'=>['url'=>$res['fileurl'],'alt'=>$res['name']]]);
		}elseif(!$res['msg']){
			$res['msg']='发生错误';
		}
		xg_jsonmsg(['errno'=>1,'message'=>$res['msg']]);
	}
	function video(){
		$this->isvideo=true;
		$res=$this->doupload();
		if($res['ok']){
			xg_jsonmsg(['errno'=>0,'data'=>['url'=>$res['fileurl'],'alt'=>$res['name']]]);
		}elseif(!$res['msg']){
			$res['msg']='发生错误';
		}
		xg_jsonmsg(['errno'=>1,'message'=>$res['msg']]);
	}
}
?>