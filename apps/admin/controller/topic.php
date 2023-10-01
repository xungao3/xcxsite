<?php
namespace apps\admin\controller;
class topic extends base{
	use \xg\traits\formtype;
	function init(){
		parent::init();
	}
	public function fields(){
		$this->assign('type',$this->type);
		$this->assign('form',$this->form);
		$list=xg_model('topic_fields')->select();
		$this->display(array('list'=>$list));
	}
	public function field(){
		$id=xg_input('id/i');
		if($id)$info=xg_model('topic_fields')->json('data')->find($id);
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
			$table='topic';
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
				xg_model('topic_fields')->time('updatetime')->json('data')->update($data,$id);
			}else{
				$data['status']=1;
				$id=xg_model('topic_fields')->time('createtime,updatetime')->json('data')->insert($data);
			}
			xg_cache('topic',null);
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
		$info=xg_model('topic_fields')->find($id);
		if(!$info)xg_error('没有此字段');
		if($confirm=xg_confirm('确定删除['.$info['title'].']吗？'))xg_error($confirm);
		xg_drop_column('topic',$info['name']);
		xg_model('topic_fields')->delete($id);
		xg_cache('topic',null);
		xg_jsonok('删除成功');
	}
	public function delete(){
		$id=xg_input('id/i');
		$info=xg_model('topic')->find($id);
		if(!$info)xg_error('没有此专题');
		if($confirm=xg_confirm('确定删除['.$info['title'].']吗？'))xg_error($confirm);
		xg_model('topic')->delete($id);
		xg_cache('topic',null);
		xg_jsonok('删除成功');
	}
	public function formdatas(){
		$id=xg_input('id/i',0);
		if(XG_AJAX){
			$where=xg_where();
			if($id)$where->where('tid',$id);
			$data=xg_model('topic_form')->where($where)->time('time')->without('data')->order('id desc')->select();
			foreach($data as $k=>$v){
				$data[$k]['links']='<a class="xg-a xg-admin-tab-link" xg-tab-title="表单-'.$v['id'].'" href="'.xg_url('formdata',['id'=>$v['id']]).'">表单</a>';
			}
			xg_jsonok(['data'=>$data]);
		}
		$this->display();
	}
	public function formdata(){
		$id=xg_input('id/i',0);
		$data=xg_model('topic_form')->time('time')->json('data')->find($id);
		$data=xg_merge(['提交时间'=>$data['time'],'内容标题'=>$data['title']],$data['data']);
		$this->display(['data'=>$data]);
	}
	public function topics(){
		$list=xg_model('topic')->select();
		$this->display(array('list'=>$list));
	}
	public function topic(){
		$id=xg_input('id/i',0);
		$info=xg_model('topic')->find($id);
		if(XG_POST){
			$data=xg_input('post.');
			if($id){
				xg_model('topic')->update($data,$id);
			}else{
				$data['static']=1;
				xg_model('topic')->insert($data);
			}
			xg_success('保存成功');
		}
		$fields=xg_model('topic_fields')->where('status',1)->json('data')->column('*','name');
		$form=[];
		foreach($fields as $f){
			$form[$f['name']]=['name'=>$f['name'],'title'=>$f['title'],'type'=>$f['form'],'data'=>$f['data'],'remark'=>$f['remark']];
		}
		$this->display(array('info'=>$info,'form'=>$form));
	}
}
?>