<?php
namespace apps\admin\controller;
class fields extends base{
	use \xg\traits\formtype;
	function init(){
		parent::init();
		$this->assign('type',$this->type);
		$this->assign('form',$this->form);
	}
	public function field2(){
		$name=xg_input('name');
		$id=xg_input('id/i',0);
		if(XG_POST){
			$post=xg_input('post.');
			xg_model('fields')->where($id)->update($post);
			xg_cache('models',null);
			xg_success('提交成功');
		}
		$info=xg_model('fields')->find($id);
		$this->assign('info',$info);
		$this->assign('name',$name);
		return $this->display();
	}
	public function fields(){
		$mid=xg_input('mid',0,'intval');
		if(XG_POST){
			$post=xg_input('post.');
			xg_model('fields')->update($post,$post['id']);
			xg_success('保存成功');
		}
		$model=xg_model('model')->find($mid);
		$list=xg_model('fields')->where('mid',$mid)->order('sys desc')->select();
		$this->display(array('list'=>$list,'model'=>$model,'mid'=>$mid));
	}
	public function field(){
		$id=xg_input('id',0,'intval');
		$mid=xg_input('mid',0,'intval');
		$model=xg_model('model')->find($mid);
		if(!$model)xg_error('没有此模型');
		if($id)$info=xg_model('fields')->json('data')->find($id);
		$table='xg_'.$model['name'];
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
			$data['mid']=$mid;
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
				xg_model('fields')->time('updatetime')->json('data')->update($data,$id);
			}else{
				$data['status']=1;
				$id=xg_model('fields')->time('createtime,updatetime')->json('data')->insert($data);
			}
			xg_cache('models',null);
			xg_success(array('msg'=>'提交成功'));
		}else{
			$this->assign('info',$info);
			return $this->display();
		}
	}
	public function status(){
		$name=xg_input('get.name');
		if($name){
			$mid=xg_input('get.mid/i',0);
			$form=xg_model('model')->where($mid)->value('form');
			$form=xg_jsonarr($form);
			foreach($form as $k=>$v){
				if($v['name']==$name){
					$form[$k]['status']=$form[$k]['status']?0:1;
					$status=$form[$k]['status'];
				}
			}
			xg_model('model')->where($mid)->save(['form'=>xg_jsonstr($form)]);
			xg_cache('models',null);
			xg_success(['msg'=>'更新成功','status'=>$status]);
		}
		xg_cache('models',null);
		parent::status();
	}
	public function switchf(){
		$id=xg_input('id');
		$status=xg_input('status');
		$field=xg_input('field');
		xg_model('fields')->update([$field=>$status],$id);
		xg_cache('models',null);
		xg_success(['msg'=>'更新成功','status'=>$status]);
	}
	public function delete(){
		$id=xg_input('id',0,'intval');
		$mid=xg_input('mid',0,'intval');
		$info=xg_model('fields')->find($id);
		$model=xg_model('model')->find($info['mid']);
		if(!$model)xg_error('没有此模型');
		if(!$info)xg_error('没有此字段');
		if($confirm=xg_confirm('确定删除['.$info['title'].']吗？'))xg_error($confirm);
		xg_drop_column('xg_'.$model['name'],$info['name']);
		xg_model('fields')->delete($id);
		xg_cache('models',null);
		xg_jsonok('删除成功');
	}
}
?>