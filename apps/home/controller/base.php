<?php
namespace apps\home\controller;
class base extends \apps\base{
	function init(){
		parent::init();
		if(xg_config('site.site-close')){
			xg_error('网站维护中');
		}
	}
	
	protected function checklogin(){
		if(!xg_login()){
			xg_error('请先登录');
		}
	}
}
?>