<?php
namespace apps\admin\controller;
class user extends base {
	use \xg\traits\formtype;
	public function edit(){
		$userid=xg_input('get.userid',0,'intval');
		if(XG_POST){
			$data=xg_input('post.');
			if($data['password']){
				if($data['password']!=$data['repassword'])xg_error('确认密码错误');
				$salt=xg_randstr(6);
				$data['salt']=$salt;
				$data['password']=md5(md5($data['password']).$salt);
			}else{
				unset($data['password'],$data['salt']);
			}
			xg_model('member')->where(array('userid'=>$userid))->update($data);
			//adminlog('修改用户信息'.'<br>id='.$id);
			xg_success('修改成功');
		}
		$info=xg_model('member')->where('userid',$userid)->find();
		$fields=xg_model('member_fields')->where('status',1)->json('data')->column('*','name');
		$form=[];
		foreach($fields as $f){
			$form[$f['name']]=['name'=>$f['name'],'title'=>$f['title'],'type'=>$f['form'],'data'=>$f['data'],'remark'=>$f['remark']];
		}
		$this->assign('groups',xg_member_groups(0,'name'));
		$this->display(['info'=>$info,'form'=>$form]);
	}
	public function group(){
		$id=xg_input('get.id/i',0);
		if(XG_POST){
			$data=xg_input('post.');
			if($id){
				xg_model('member_group')->where($id)->update($data);
			}else{
				xg_model('member_group')->insert($data);
			}
			xg_success('保存成功');
		}
		if($id)$info=xg_model('member_group')->find($id);
		$this->assign('info',$info);
		return $this->display();
	}
	public function userlist(){
		$page=xg_input('get.page/i',1);
		$pagesize=20;
		$keywords=xg_input('get.keywords');
		if($keywords){
			$where[]=array('username|mobile|email','like','%'.$keywords.'%');
		}
		$count=xg_model('member')->where($where)->count();
		$list=xg_model('member')->where($where)->page($page,$pagesize)->order('userid desc')->column('*','userid');
		if(is_array($list))$uidlist=array_keys($list);
		$pagehtml=xg_pagehtml($count,$pagesize,$page);
		$this->assign('groups',xg_member_groups(0,'name'));
		$listf=xg_model('member_fields')->where('listf',1)->column('title','name');
		return $this->display(['list'=>$list,'listf'=>$listf,'pagehtml'=>$pagehtml,'keywords'=>$keywords]);
	}
	public function checkgroup(){
		$id=xg_input('get.id/i',0);
		$groupid=xg_model('member')->where($id)->value('apply_groupid');
		if($groupid)xg_model('member')->where($id)->update(['apply_groupid'=>0,'groupid'=>$groupid]);
		xg_success('审核成功');
	}
	public function grouplist(){
		$list=xg_model('member_group')->select();
		return $this->display(['list'=>$list]);
	}
	
	
	
	
	public function switchf(){
		$id=xg_input('id');
		$status=xg_input('status');
		$field=xg_input('field');
		xg_model('member_fields')->update([$field=>$status],$id);
		xg_success(['msg'=>'更新成功','status'=>$status]);
	}
	public function fields(){
		$this->assign('type',$this->type);
		$this->assign('form',$this->form);
		$list=xg_model('member_fields')->select();
		$this->display(array('list'=>$list));
	}
	public function field(){
		$id=xg_input('id/i');
		if($id)$info=xg_model('member_fields')->json('data')->find($id);
		if(XG_POST){
			$post=xg_input('post.');
			if(!$post['title'])xg_error('请填写名称');
			if(!$post['name'])xg_error('请填写标识');
			if(!$post['type'])xg_error('请填写数据类型');
			if(!$post['form'] and $confirm=xg_confirm('没有选择表单，确定提交吗？'))xg_error($confirm);
			$data['title']=$title=$post['title'];
			$data['name']=$name=$post['name'];
			$data['type']=$type=$post['type'];
			$data['length']=$length=$post['length'];
			$data['form']=$form=$post['form'];
			$data['remark']=$post['remark'];
			$data['func']=$post['func'];
			if($type=='varchar'){
				if(!$length){
					xg_error('请填写字段长度');
				}
			}
			if($form=='table'){
				$data['data']=$post['table-info'];
			}
			if($form=='html'){
				$data['data']=xg_input('post.html-info','','');
			}
			$table='member';
			if(!$id){
				xg_add_column($table,$name,$type,$length,$title);
			}elseif($info){
				if($info['name']!=$name){
					xg_rename_column($table,$info['name'],$name,$title);
				}
				if($info['length']!=$length or $info['type']!=$type){
					xg_change_column($table,$name,$type,$length,$title);
				}
			}
			if($info){
				xg_model('member_fields')->time('updatetime')->json('data')->update($data,$id);
			}else{
				$data['status']=1;
				$id=xg_model('member_fields')->time('createtime,updatetime')->json('data')->insert($data);
			}
			xg_cache('member',null);
			xg_success(array('msg'=>'提交成功'));
		}else{
			$this->assign('type',$this->type);
			$this->assign('form',$this->form);
			$this->assign('info',$info);
			return $this->display();
		}
	}
	public function delfield(){
		$id=xg_input('id/i');
		$info=xg_model('member_fields')->find($id);
		if(!$info)xg_error('没有此字段');
		if($confirm=xg_confirm('确定删除['.$info['title'].']吗？'))xg_error($confirm);
		xg_drop_column('member',$info['name']);
		xg_model('member_fields')->delete($id);
		xg_cache('member',null);
		xg_jsonok('删除成功');
	}
}
?>