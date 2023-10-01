<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg\traits;
trait status{
	public function softdelete(){
		$this->status();
	}
	public function restore(){
		$this->status();
	}
	public function status(){
		$model=xg_input('model');
		if($model)$tbname='xg_'.$model;
		if(!$tbname)$tbname=xg_input('tbname');
		$act=XG_ACT;
		if($act=='softdelete'){
			$status=-1;
			$actname='删除';
		}elseif($act=='restore'){
			$status=1;
			$actname='还原';
		}else{
			$status=xg_input('status',null);
			$actname='更新';
		}
		$id=xg_input('id');
		if(!$tbname)xg_error('没有表名');
		if(!$id)xg_error('没有ID');
		$rt=xg_model($tbname)->update_status($id,$status);
		if($rt===false)xg_error($actname.'失败');
		if(xg_models()[$tbname]){
			$cids=xg_model('content')->name($tbname)->where('id',$id)->column('cid');
			xg_model('content')->name($tbname)->recount($cids);
		}
		xg_success(array('msg'=>$actname.'成功','action'=>$act,'status'=>$rt));
	}
	public function clear(){
		$model=xg_input('model');
		if($model)$tbname='xg_'.$model;
		if(!$tbname)$tbname=xg_input('tbname');
		if(!$tbname)xg_error('没有表名');
		$rt=xg_model($tbname)->where('status',-1)->delete();
		if($rt===false)xg_error('清空失败');
		xg_success(array('msg'=>'清空成功','action'=>'delete','status'=>$rt));
	}
	public function delete(){
		$model=xg_input('model');
		if($model)$tbname='xg_'.$model;
		if(!$tbname)$tbname=xg_input('tbname');
		$id=xg_input('id');
		if(!$tbname)xg_error('没有表名');
		if(!$id)xg_error('没有ID');
		$rt=xg_model($tbname)->delete($id);
		xg_hooks('status-delete')->run($tbname,$id,$rt);
		if($rt===false)xg_error('删除失败');
		xg_success(array('msg'=>'删除成功','action'=>'delete','status'=>$rt));
	}
}
?>