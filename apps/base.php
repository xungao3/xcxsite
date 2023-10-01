<?php
namespace apps;
class base extends \xg\controller{
	protected function init(){
		xg_hooks('submit-auto')->run();
		parent::init();
	}
	public function vcode(){
		$vcode=new \xg\libs\vcode();
		$err=$vcode->get();
		if($err)xg_error($err);
		xg_success('验证码发送成功');
	}
	public function verify(){
		$id=xg_input('get.id',1);
		$verify=new \xg\libs\verify();
		$verify->make($id);
	}
	public function imgcode(){
		$id=xg_input('get.id',1);
		return $this->display(XG_PHP.'/view/imgcode',['id'=>$id]);
	}
}
?>