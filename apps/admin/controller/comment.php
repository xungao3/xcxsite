<?php
namespace apps\admin\controller;
class comment extends base{
	public function comment(){
		$page=xg_input('get.page/i',1);
		$cid=xg_input('get.cid/i',0);
		$id=xg_input('get.id/i',0);
		$model=xg_input('get.model');
		$pagesize=100;
		$where=xg_where();
		if($model)$where->where('model',$model);
		if($cid)$where->where('cid',$cid);
		if($id)$where->where('infoid',$id);
		$allcount=$count=xg_model('comment')->count();
		if($id||$cid||$model)$count=xg_model('comment')->where($where)->count();
		$list=xg_model('comment')->where($where)->page($page,$pagesize)->order('id desc')->select();
		if($model||$cid){
			$ids=[];
			foreach($list as $v){
				$ids[]=$v['infoid'];
			}
			if($ids)$infos=xg_model($model?$model:xg_category($cid,'model'))->where('id',$ids)->column('title,cid,id');
		}
		$pagehtml=xg_pagehtml($count,$pagesize);
		$this->assign('list',$list);
		$this->assign('allcount',$allcount);
		$this->assign('count',$count);
		$this->assign('infos',$infos);
		$this->assign('info',$info);
		$this->assign('model',$model);
		$this->assign('cid',$cid);
		$this->assign('id',$id);
		$this->assign('pagehtml',$pagehtml);
		$this->display();
	}
	public function delete(){
		$id=xg_input('id/i');
		$info=xg_model('comment')->find($id);
		xg('hooks.admin.status-delete.comment',function($status)use($info){
			if($info and $status){
				$tbname=$info['model'];
				$value=xg_model($tbname)->where($info['infoid'])->value('cmt');
				if($value>0)xg_model($tbname)->where($info['infoid'])->dec('cmt');
			}
		});
		parent::delete();
	}
}
?>