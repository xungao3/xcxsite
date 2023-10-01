<?php
namespace apps\admin\controller;
class base extends \apps\base{
	use \xg\traits\status;
	public function init(){
		parent::init();
		$this->checklogin();
		xg_admin_auth(XG_CTL);
		xg_admin_auth(XG_CTL.'-'.XG_ACT);
		$this->assign('xg.admin',xg('admin'));
	}
	protected function checklogin(){
		if(!xg_admin()){
			if(XG_APP=='admin')$url=xg_url('index/index');
			xg_error('请先登录管理员账号',$url);
		}
	}
}
?>