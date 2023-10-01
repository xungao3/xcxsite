<?php
namespace apps\home\controller;

class user extends base{
	function init(){
		parent::init();
		if(!xg_in_array(XG_ACT,array('login','register','resetpw','logout'))){
			$this->checklogin();
		}
	}
	public function login(){
		if(XG_POST){
			if(xg_model('times')->check_times(1)){
				xg_error('此IP超过登录次数');
			}
			$username=xg_input('post.username');
			$password=xg_input('post.password');
			if(!$username)xg_error('请填写用户名');
			if(!$password)xg_error('请填写密码');
			$user=xg_model('member')->where('username|mobile',$username)->find();
			if(!$user)xg_error('没有此用户');
			if(!$this->checkpassword($user,$password)){
				xg_model('times')->add_times(1);
				xg_error('密码错误');
			}
			if(!$user['status'])xg_error('您的账号已经被停用');
			xg_set_login($user['userid'],$user['username'],$password,$user['groupid'],!!xg_input('post.remember'));
			$data=array('login_times'=>'`login_times`+1','last_login_time'=>XG_TIME,'last_login_ip'=>xg_ip());
			if(xg_session('wx_openid'))$data['wx_openid']=xg_session('wx_openid');
			xg_model('member')->where('userid',$user['userid'])->update($data);
			xg_success(array('msg'=>'登录成功','goto'=>'/'));
		}else{
			return $this->display(['pagetitle'=>'登录']);
		}
	}
	protected function checkpassword($user,$password){
		if(md5(md5($password).$user['salt'])==$user['password']){
			return true;
		}
		return false;
	}
	public function register(){
		if(XG_POST){
			$username=xg_input('post.username','');
			$password=xg_input('post.password','');
			$repassword=xg_input('post.repassword','');
			$mobile=xg_input('post.mobile','');
			//$email=xg_input('post.email','');
			$vcode=xg_input('post.vcode');
			if(!$username)xg_error('请填写用户名');
			if(xg_ismobile($username))xg_error('用户名不能是手机号');
			if(!xg_ismobile($mobile))xg_error('请填写正确的手机号');
			if(!$password)xg_error('请填写密码');
			if(!$repassword)xg_error('请填写确认密码');
			if($repassword!=$password)xg_error('两次输入的密码不一致');
			if(!$vcode)xg_error('请填写手机验证码');
			if(xg_model('member')->check_name($username))xg_error('此用户名已经被注册');
			if(xg_model('member')->check_mobile($mobile))xg_error('此手机号已经被注册');
			if($mobile){
				$err=xg_vcode()->check($mobile,$vcode,3);
				if($err)xg_error($err);
			}
			$user=xg_model('member')->data(['wx_openid'=>xg_session('wx_openid')])->register($username,$password,$mobile,$email);
			xg_set_login($user['userid'],$username,$password,$user['groupid']);
			xg_vcode()->exp($mobile,$vcode,3);
			xg_success(['msg'=>'注册成功','goto'=>'/']);
		}else{
			return $this->display(['pagetitle'=>'注册账号','navcur'=>'register']);
		}
	}
	public function resetpw(){
		if(XG_POST){
			$username=xg_input('post.username');
			$mobile=xg_input('post.mobile');
			$vcode=xg_input('post.vcode');
			$password=xg_input('post.password');
			$repassword=xg_input('post.repassword');
			if(!$username)xg_error('请填写用户名');
			if(!$mobile)xg_error('请填写手机号');
			$user=xg_model('member')->where('username',$username)->find();
			if($user['mobile']!=$mobile)xg_error('手机号错误');
			if(!$vcode)xg_error('请填写手机验证码');
			if(!$password)xg_error('请填写密码');
			if($password!=$repassword)xg_error('密码和确认密码不一致');
			$err=xg_vcode()->check($mobile,$vcode,1);
			if($err)xg_error($err);
			xg_vcode()->exp($mobile,$vcode,1);
			xg_model('member')->where('username',$username)->update(['password'=>md5(md5($password).$user['salt'])]);
			xg_success('修改成功',xg_url('user/login'));
		}else{
			return $this->display(['pagetitle'=>'找回密码']);
		}
	}
	public function password(){
		if(xg_model('times')->check_times(1)){
			xg_error('此IP超过错误次数');
		}
		$oldpassword=xg_input('post.oldpassword');
		$newpassword=xg_input('post.newpassword');
		$repassword=xg_input('post.repassword');
		if(!$oldpassword)xg_error('请填写旧密码');
		if(!$newpassword)xg_error('请填写新密码');
		if(!$repassword)xg_error('请填写确认密码');
		$user=xg_model('member')->find(xg_login());
		if(!$this->checkpassword($user,$oldpassword)){
			xg_model('times')->add_times(1);
			xg_error('密码错误');
		}
		xg_model('member')->where('userid',xg_login())->update(['password'=>md5(md5($newpassword).$user['salt'])]);
		xg_set_login($user['userid'],$user['username'],$newpassword,$user['groupid'],xg_cookie('user_auth'));
		xg_success(array('msg'=>'修改成功'));
	}
	public function index(){
		return xg_redirect(xg_url('user/profile'));
	}
	public function profile(){
		$userinfo=xg_model('member')->where('userid',xg_login())->find();
		return $this->display('',[
			'userinfo'=>$userinfo,
			'pagetitle'=>'我的资料'
		]);
	}
	public function connect(){
		if(xg_model('times')->check_times(1)){
			xg_error('此IP超过错误次数');
		}
		$password=xg_input('post.password');
		$email=xg_input('post.email','');
		$mobile=xg_input('post.mobile','');
		$vcode=xg_input('post.vcode');
		$user=xg_model('member')->find(xg_login());
		if(!$password)xg_error('请填写密码');
		if(!$mobile)xg_error('请填写手机号');
		if($mobile){
			if(!xg_ismobile($mobile))xg_error('请填写正确的手机号');
			if(xg_model('member')->has_mobile($mobile,xg_login()))xg_error('此手机号已经被注册');
			if($mobile!=$user['mobile']){
				if(!$vcode)xg_error('请填写手机验证码');
				$err=xg_vcode()->check($mobile,$vcode,3);
				if($err)xg_error($err);
			}
		}
		if(!$this->checkpassword($user,$password)){
			xg_model('times')->add_times(1);
			xg_error('密码错误');
		}
		$save=[];
		if($mobile and $mobile!=$user['mobile'])$save['mobile']=$mobile;
		if($save)xg_model('member')->where('userid',xg_login())->update($save);
		xg_vcode()->exp($mobile,$vcode,3);
		xg_success(array('msg'=>'修改成功'));
	}
	public function logout(){
		xg_clear_login();
		xg_success('退出成功','/');
	}
}