<?php
namespace sys\xg\traits;
trait user{
	public function checklogin(){
		if($userid=xg_login()){
			if($user=xg_model('member')->info($userid))xg_jsonok(['user'=>$user]);
		}
		xg_jsonerr();
	}
	public function logout(){
		xg_clear_login();
		xg_jsonok();
	}
	public function bind($data,$app=''){
		return xg_model('member')->bind($data,$app);
	}
	public function login(){
		if($sys=xg_input('sys') and $sys!='xg'){
			if(!$this->sys)return;
			return $this->sys->login();
		}
		$mobile=xg_input('mobile');
		$vcode=xg_input('vcode/i');
		if(!$mobile)xg_jsonerr('请填写手机号');
		if(!$vcode)xg_jsonerr('请填写验证码');
		if($vcode!=xg_cache('vcode-mobile-'.$mobile))xg_jsonerr('验证码错误');
		$userid=xg_model('member')->mobile2id($mobile);
		if(!$userid){
			$username=xg_randstr(12);
			$userid=xg_model('member')->register($username,'',$mobile);
		}
		$sessid=xg_set_login($userid);
		xg_jsonok(['session_id'=>$sessid,'msg'=>'登录成功']);
	}
	public function mobile(){
		$mobile=xg_input('mobile');
		$vcode=xg_input('vcode/i');
		if(!$mobile)xg_jsonerr('请填写手机号');
		if(!$vcode)xg_jsonerr('请填写验证码');
		if($vcode!=xg_cache('vcode-mobile-'.$mobile))xg_jsonerr('验证码错误');
		$userid=xg_model('member')->where('mobile',$mobile)->value('userid');
		if(!$userid){
			$username=xg_randstr(12);
			$userid=xg_model('member')->register($username,'',$mobile);
		}
		$sessid=xg_set_login($userid,'',null,1,true,'ecms');
		xg_jsonok(['session_id'=>$sessid,'msg'=>'登录成功']);
	}
}
?>