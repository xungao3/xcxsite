<?php
namespace apps\home\controller;
class content extends base{
	public function index(){
		$cid=xg_input('get.cid/i',0);
		$id=xg_input('get.id/i',0);
		$table=xg_input('get.tbname');
		if(!$table)$table=xg_input('get.model');
		if(!xg_models()[$table])xg_error('表名不正确');
		if($cid)$cate=xg_category($cid);
		if(!$cate)xg_error('没有此分类');
		if(!$cate['status'])xg_error('此分类未开放');
		if(!xg_models()[$table])xg_error('表名不正确');
		$fields=xg_models()[$table]["contf"];
		if($id)$info=xg_model('xg_'.$table)->fields($fields)->find($id);
		if(!$info)xg_error('没有此内容');
		if(!$info['status'])xg_error('此内容未开放');
		xg_model('xg_'.$table)->where('id',$id)->inc('view');
		if($cate['conttpl']){
			$tpl=$cate['conttpl'];
		}else{
			$tpl='content/'.$table;
		}
		return $this->display($tpl,[
			'info'=>$info,
			'cate'=>$cate,
			'cid'=>$info['cid'],
			'pagekeywords'=>$info['keywords'],
			'pagedescription'=>$info['description'],
			'pagetitle'=>$info['title']
		]);
	}
}
?>