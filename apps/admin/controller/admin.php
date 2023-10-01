<?php
namespace apps\admin\controller;
class admin extends base {
	public function admin(){
		$userid=xg_input('get.userid',0,'intval');
		if(XG_POST){
			$data=xg_input('post.');
			if(!$data['username'])xg_error('请填写用户名');
			if($data['password']){
				if($data['password']!=$data['repassword'])xg_error('两次输入的密码不一致');
				$salt=xg_randstr(6);
				$data['salt']=$salt;
				$data['password']=md5(md5($data['password']).$salt);
			}else{
				unset($data['password'],$data['salt']);
			}
			if($userid){
				xg_model('admin')->where(array('userid'=>$userid))->json('auth')->update($data);
				xg_cache('admin-'.$userid,null);
			}else{
				if(xg_model('admin')->where(array('username'=>$data['username']))->value('userid'))xg_error('已经存在此管理员');
				if(!$data['password'])xg_error('请填写密码');
				$data['status']=1;
				xg_model('admin')->json('auth')->add($data);
			}
			xg_success('保存成功');
		}
		if($userid)$info=xg_model('admin')->where('userid',$userid)->json('auth')->find();
		$this->assign('info',$info);
		$authdata=xg_cache('admin-auth');
		$auth=[];
		$group=[];
		foreach($authdata as $v){
			if(!$v['with']){
				if($v['group']){
					$auth[$v['group']]=$auth[$v['group']]?$auth[$v['group']]:[];
					$auth[$v['group']][$v['name']]=$v;
				}else{
					$group[$v['name']]=$v['title'];
				}
			}
		}
		$this->assign('models',xg_models());
		$this->assign('group',$group);
		$this->assign('auth',$auth);
		return $this->display();
	}
	public function userlist(){
		$page=xg_input('get.page/i',1);
		$pagesize=20;
		$keywords=xg_input('get.keywords');
		if($keywords){
			$where[]=array('username|mobile|email','like','%'.$keywords.'%');
		}
		$count=xg_model('admin')->where($where)->count();
		$list=xg_model('admin')->where($where)->page($page,$pagesize)->order('userid desc')->column('*','userid');
		if(is_array($list))$uidlist=array_keys($list);
		$pagehtml=xg_pagehtml($count,$pagesize,$page);
		return $this->display(['list'=>$list,'pagehtml'=>$pagehtml,'keywords'=>$keywords]);
	}
}
?>