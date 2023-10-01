<?php
namespace apps\admin\model;

class nav extends \xg\model{
	function category(){
		return $this->where('status',1)->where('type','category')->where('pid',0)->column('infoid','infoid');
	}
	function __call($fun,$args){
		if(xg_allmodels($fun)){
			return $this->where('status',1)->where('type',$fun)->column('infoid','infoid');
		}else{
			return parent::__call($fun,$args);
		}
	}
}