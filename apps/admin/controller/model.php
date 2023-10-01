<?php
namespace apps\admin\controller;
class model extends base{
	public function model(){
		$id=xg_input('id',0,'intval');
		if(XG_POST){
			$post=xg_input('post.');
			$data=array();
			if(!$post['title'])xg_error('请填写标题');
			if(!$post['name'])xg_error('请填写名称');
			if(preg_match('/[^a-zA-Z0-9_\-\.]/',$post['name']))xg_error('名称不能包括中文和一些特殊字符');
			if(!preg_match('/^[a-zA-Z]/',$post['name']))xg_error('名称必须以字母开头');
			if(!$post['alias'])xg_error('请填写别名');
			$data['title']=$title=$post['title'];
			$data['name']=$name=$post['name'];
			$data['alias']=$post['alias'];
			$data['remark']=$post['remark'];
			$data['type']=$type=(int)$post['type'];
			if($type!==1 and $type!==2)xg_error('模型类型错误');
			if($id){
				$info=xg_model('model')->find($id);
				if($info['name']!=$post['name']){
					xg_rename_table('xg_'.$info['name'],'xg_'.$post['name']);
					xg_model('category')->where('model',$info['name'])->update(['model'=>$post['name']]);
					xg_model('comment')->where('model',$info['name'])->update(['model'=>$post['name']]);
					xg_hooks('rename-model')->run($info['name'],$post['name']);
				}
				$data['status']=$post['status'];
				xg_model('model')->time('updatetime')->update($data,$id);
			}else{
				$tbpre=XG_TBPRE;
				if(xg_table_exist("{$tbpre}xg_{$name}"))xg_error('已存在此名称的表');
				$data['status']=1;
				$id=xg_model('model')->time('createtime,updatetime')->insert($data);
				$sql=str_replace(['[--xg-table-name--]','[--xg-table-title--]'],["{$tbpre}xg_{$name}",$title],xg_model_table_state());
				xg_db()->exec($sql);
				$fields=xg_model_sys_fields();
				foreach($fields as $k=>$v){
					$fields[$k]['mid']=$id;
				}
				xg_model('fields')->insert($fields);
				if($type==1){
					xg_model('fields')->where('mid',$id)->update(['adminf'=>1,'status'=>1,'contf'=>1,'catef'=>1]);
				}
			}
			xg_cache('models',null);
			xg_success(array('msg'=>'提交成功'));
		}else{
			if($id)$info=xg_model('model')->find($id);
			$this->assign('info',$info);
			return $this->display();
		}
	}
	public function load(){
		$cont=xg_file()->allow('json')->content();
		$data=xg_jsonarr($cont)['xg-saved-model'];
		if(!$data)xg_error('数据错误');
		xg_load_model($data);
		xg_cache('models',null);
		xg_success('导入成功');
	}
	public function save(){
		$id=xg_input('id/i',0);
		$savename=$info['title'].'-'.date('Ymd').'-'.date('His').'.json';
		$data=xg_jsonstr(['xg-saved-model'=>xg_save_model($id)]);
		xg_download($data,$savename);
	}
	public function delete(){
		$id=xg_input('id',0,'intval');
		$yes=xg_input('xg-yes');
		$info=xg_model('model')->find($id);
		if(!$info)xg_error('没有此模型');
		if($yes){
			xg_drop_table('xg_'.$info['name']);
			xg_model('model')->delete($id);
			xg_model('fields')->where('mid',$id)->delete();
			xg_cache('models',null);
			xg_success('删除成功');
		}else{
			xg_jsonerr(['confirm'=>'确定删除['.$info['title'].']吗？']);
		}
	}
	public function switchf(){
		$id=xg_input('id');
		$status=xg_input('status');
		$field=xg_input('field');
		xg_model('model')->update([$field=>$status],$id);
		xg_cache('models',null);
		xg_success(['msg'=>'更新成功','status'=>$status]);
	}
	public function models(){
		$list=xg_model('model')->select();
		$this->display(array('list'=>$list));
	}
}
?>