<?php
namespace apps\home\controller;
class topic extends base{
	public function index(){
		$id=xg_input('get.id/i');
		$info=xg_model('topic')->where('status',1)->find($id);
		return $this->display('topic/'.$id,['info'=>$info,'pagetitle'=>$info['title']]);
	}
}
?>