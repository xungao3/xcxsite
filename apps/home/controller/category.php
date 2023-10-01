<?php
namespace apps\home\controller;
class category extends base{
	public function index(){
		$cid=xg_input('get.cid/i');
		$page=xg_input('get.page/i',1);
		$table=xg_input('get.tbname');
		if(!xg_models()[$table])xg_error('表名不正确');
		$pagesize=24;
		$cate=xg_category($cid);
		if(!$cate['status'])xg_error('此分类未开放');
		$count=xg_model('content')->name($table)->where('cid',$cid)->count();
		if($cate['catetpl']){
			$tpl=$cate['catetpl'];
		}else{
			$tpl='category/'.$table;
		}
		return $this->display($tpl,[
			'pagesize'=>$pagesize,
			'count'=>$count,
			'table'=>$table,
			'model'=>$table,
			'page'=>$page,
			'cateinfo'=>$cate,
			'catename'=>$cate['title'],
			'pagetitle'=>$cate['title'],
			'cid'=>$cid,
			'pagehtml'=>$pagehtml
		]);
	}
}
?>