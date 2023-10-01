<?php
namespace xgphp\model;

class member extends \xg\model{
	protected $name='member';
	protected $data=[];
	protected $bind=[];
	public function register($username='',$password='',$mobile='',$email=''){
		$salt=xg_randstr(6);
		$user=$this->data;
		$user['salt']=$salt;
		if(!$username)$username=xg_randstr(12);
		$username=$ousername=xg_subtext($username,15,'');
		while($this->check_name($username)){
			$username=xg_subtext($ousername,10,'').'_'.xg_randstr(4);
		}
		$user['username']=$username;
		if($password)$user['password']=md5(md5($password).$salt);
		$user['mobile']=$mobile;
		$user['email']=$email;
		$user['status']=1;
		$user['login_times']=1;
		$user['reg_time']=XG_TIME;
		$user['reg_ip']=xg_ip();
		$user['last_login_time']=XG_TIME;
		$user['last_login_ip']=xg_ip();
		$userid=$this->insert($user);
		$user['userid']=$userid;
		return $user;
	}
	public function form($data,$uid=0){
		unset($data['userid'],$data['username'],$data['groupid'],$data['mobile'],$data['email'],$data['password'],$data['salt'],$data['status'],$data['reason'],$data['userid']);
		return $this->update($data,$uid);
	}
	public function data($data){
		$this->data=xg_extend($this->data,$data);
		return $this;
	}
	public function info($userid){
		$info=$this->find($userid);
		$bind=xg_model('member_binding')->where('userid',$userid)->find();
		return xg_extend($info,$bind);
	}
	public function bind($data,$app=''){
		if($userid=$this->openid2id($data['openid'])){
			return $this->info($userid);
		}
		$user=$this->register($data['nickname']);
		$data=xg_extend($user,$data);
		$data['app']=$app;
		xg_model('member_binding')->insert($data);
		return $data;
	}
	public function openid2id($openid){
		$userid=xg_model('member_binding')->where('openid',$openid)->value('userid');
		return intval($userid);
	}
	public function openid2user($openid){
		$bind=xg_model('member_binding')->where('openid',$openid)->find();
		if($bind){
			$data=xg_model('member')->find($bind['userid']);
			if($data)$data=xg_extend($data,$bind);
		}
		return $data?$data:[];
	}
	public function check_mobile($mobile,$userid=0){
		$where=[];
		$where[]=['mobile','=',$mobile];
		if($userid)$where[]=['userid','!=',$userid];
		return xg_model('member')->where($where)->count();
	}
	public function check_name($username){
		return xg_model('member')->where('username',$username)->count();
	}
	public function mobile2id($mobile){
		$where=[];
		$where[]=['mobile','=',$mobile];
		return xg_model('member')->where($where)->value('userid');
	}
	public function name2id($username){
		$where=[];
		$where[]=['username','=',$username];
		return xg_model('member')->where($where)->value('userid');
	}
}