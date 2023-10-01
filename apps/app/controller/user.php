<?php
//中文
namespace apps\app\controller;
class user extends base{
	protected $sys=null;
	function init(){
		parent::init();
		$sys=xg_input('sys');
		if($sys)$this->sys=xg_sys($sys);
	}
	public function login(){
		if(!$this->sys)return;
		return $this->sys->login();
	}
	public function checklogin(){
		if(!$this->sys)return;
		return $this->sys->checklogin();
	}
	public function register(){
		if(!$this->sys)return;
		return $this->sys->register();
	}
	public function logout(){
		if(!$this->sys)return;
		return $this->sys->logout();
	}
	// public function form(){
	// 	$data=xg_input('post.');
	// 	$uid=xg_login('app');
	// 	if(!$uid)xg_error('请先登录');
	// 	xg_model('member')->form($data,$uid);
	// 	xg_success('提交成功');
	// }
	public function getvcode(){
		$mobile=xg_input('mobile');
		xg_vcode()->get($mobile);
		xg_jsonerr('百度短信请求接口发生错误');
	}
	public function wxxcxmobile(){
		$appid=xg_input('appid');
		$code=xg_input('code');
		$mobile=\xgwxxcx::init($appid)->mobile($code);
		xg_jsonok(['mobile'=>$mobile]);
	}
	public function wxxcxlogin(){
		$app=xg_input('appname');
		$data = xg_input('post.');
		$appid = $data['appid'];
		$res=\xgwxxcx::init($appid)->login($data);
		$res=$this->sys->bind($res,$app);
		$sessid=xg_set_login($res['userid']);
		xg_jsonok(['session_id'=>$sessid,'msg'=>'登录成功','user'=>$res]);
	}
}
?>