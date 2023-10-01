<?php
namespace apps\home\controller;
class index extends base{
	public function index(){
		if(!file_exists(XG_DATA.'/installed')){
			xg_redirect(xg_url('install/index/index'));
		}
		$this->assign('pagetitle',xg_config('site.site-title'));
		$this->assign('pagedescription',xg_config('site.site-description'));
		$this->assign('pagekeywords',xg_config('site.site-keywords'));
		return $this->display();
	}
}
?>