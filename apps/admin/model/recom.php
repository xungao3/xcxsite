<?php
namespace apps\admin\model;

class recom extends \xg\model{
	function category(){
		$list=$this->where('type','category')->where('status',1)->select();
		$data=[];
		foreach($list as $v){
			$data[$v['recom']][]=$v['cateid'];
		}
		return $data;
	}
	function topic(){
		$list=$this->where('type','topic')->where('status',1)->select();
		$data=[];
		foreach($list as $v){
			$data[$v['recom']][]=$v['infoid'];
		}
		return $data;
	}
	function __call($fun,$args){
		if(xg_allmodels($fun)){
			$list=$this->where(['status'=>1,'type'=>'model','model'=>$fun])->select();
			$data=[];
			foreach($list as $v){
				$data[$v['recom']][$v['id']]=$v['infoid'];
			}
			return $data;
		}else{
			return parent::__call($fun,$args);
		}
	}
}