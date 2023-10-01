<?php
namespace apps\admin\controller;
class file extends base{
	use \xg\traits\file;
	function delete(){
		$id=xg_input('id/i');
		$url=xg_model('files')->where($id)->value('url');
		$rt=xg_model('files')->delete($id);
		if($rt){
			xg_success('删除成功');
		}
		xg_error('删除失败');
	}
	function select(){
		$classid=xg_input('classid/i');
		$infoid=xg_input('infoid/i');
		$type=xg_input('model');
		$group=xg_input('group','current');
		$keywords=xg_input('keywords');
		$count=xg_input('count/i');
		$isimg=xg_input('isimg',null);
		$page=xg_input('page/i',1);
		$pagesize=30;
		$where=new \xg\where();
		if($type)$where->where('type',$type);
		if($classid)$where->where('classid',$classid);
		if(!is_null($isimg))$where->where('isimg',$isimg);
		if($group=='content'){
			if(xg_models()[$type])$ids=xg_model($type)->where('title','like','%'.$keywords.'%')->column('id');
			if($ids){
				$where->where('type',$type)->where('infoid','in',$ids);
			}else{
				xg_jsonok(['html'=>'']);
			}
		}elseif($group=='filename'){
			$where->where('name','like','%'.$keywords.'%');
		}
		if($infoid and $group=='current')$where->where('infoid',$infoid);
		$rscount=xg_model('files')->where($where)->count();
		$list=xg_model('files')->where($where)->fields('id,url,name')->page($page,$pagesize)->order('id desc')->select();
		$data=['list'=>$list,'type'=>$type,'group'=>$group,'isimg'=>$isimg,'count'=>$count,'pagehtml'=>xg_pagehtml($rscount,$pagesize,$page,'javascript:page([PAGE]);')];
		if(xg_input('html')){
			xg_jsonok(['html'=>$this->fetch('html',$data)]);
		}
		$this->display($data);
	}
}
?>