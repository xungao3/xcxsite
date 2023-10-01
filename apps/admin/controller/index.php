<?php
namespace apps\admin\controller;
class index extends \xg\controller{
	public function main(){
		return $this->display();
	}
	public function admin(){
		return $this->display();
	}
	public function logout(){
		xg_clear_login('admin');
		xg_success('成功退出登录',xg_url('index'));
	}
	public function index(){
		$goto=xg_input('goto',xg_url('main/main'));
		if(xg_admin())return xg_redirect($goto);
		if(xg_input('username')){
			$username=xg_input('username');
			$password=xg_input('password');
			$remember=!!xg_input('remember');
			$verify=xg_input('verify');
			if(xg_model('times')->check_times()){
				xg_error('此IP超过登录次数');
			}
			if($user=xg_model('admin')->where(array('username'=>$username))->find()){
				if(md5(md5($password).$user['salt'])==$user['password']){
					if($user['status']<=0)xg_error('此账号已停用');
					xg_set_login($user['userid'],$user['username'],$password,$user['groupid'],$remember,'admin');
					//adminlog('登录后台');
					if(XG_POST)xg_success('登录成功',$goto);
					return xg_redirect($goto);
				}
			}
			xg_model('times')->add_times();
			xg_error('用户名或密码错误');
		}
		return $this->display();
	}
}
?>