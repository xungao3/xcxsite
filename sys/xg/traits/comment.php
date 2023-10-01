<?php
namespace sys\xg\traits;
trait comment{
	function comment(){
		$data=xg_input('request.');
		$id=$data['id'];
		$cid=$data['cid'];
		$model=xg_category($cid,'model');
		if(!$model)xg_jsonerr('没有此模型');
		if(!$data['content'])xg_jsonerr('请填写内容');
		xg_model('comment')->add(['content'=>$data['content'],'cid'=>$cid,'model'=>$model,'infoid'=>$id,'time'=>XG_TIME,'status'=>1,'ip'=>xg_ip()]);
		xg_model($model)->where($id)->inc('cmt');
		xg_jsonok(['list'=>$this->comments(),'msg'=>'提交成功']);
	}
	function comments(){
		$data=xg_input('request.');
		$id=$data['id'];
		$cid=$data['cid'];
		$model=xg_category($cid,'model');
		if(!$model)xg_jsonerr('没有此模型');
		$list=xg_model('comment')->where('status',1)->where(['cid'=>$cid,'infoid'=>$id])->time('time')->order('id desc')->select();
		if(XG_ACT!='comments')return $list;
		xg_jsonok(['list'=>$list]);
	}
}