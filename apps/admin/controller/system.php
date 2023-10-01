<?php
namespace apps\admin\controller;
class system extends base{
	function clear(){
		if(XG_POST){
			$clear=xg_input('post.clear',[]);
			if(in_array('db',$clear))xg_deldir(XG_RUNTIME.'/db/');
			if(in_array('cache',$clear))xg_deldir(XG_RUNTIME.'/cache/');
			if(in_array('log',$clear))xg_deldir(XG_RUNTIME.'/log/');
			if(in_array('temp',$clear))xg_deldir(XG_RUNTIME.'/temp/');
			if(in_array('content',$clear)){
				foreach(xg_allmodels() as $model){
					xg_deldir(XG_PUBLIC.'/data/xg/xg-'.$model['name']);
				}
			}
			if(in_array('contents',$clear))xg_deldir(XG_PUBLIC.'/data/xg/contents');
			if(in_array('allcate',$clear))xg_deldir(XG_PUBLIC.'/data/xg/allcate');
			if(in_array('curcate',$clear))xg_deldir(XG_PUBLIC.'/data/xg/curcate');
			if(in_array('topic',$clear))xg_deldir(XG_PUBLIC.'/data/xg/topic');
			if(in_array('recom',$clear))xg_deldir(XG_PUBLIC.'/data/xg/recom');
			xg_success('清除完成');
		}
		$this->display();
	}
}
?>