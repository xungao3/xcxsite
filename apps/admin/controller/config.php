<?php
namespace apps\admin\controller;
class config extends base{
	protected $type=['image'=>'图片','text'=>'文本框','textarea'=>'多行文本框','bool'=>'布尔值','radio'=>'单选','checkbox'=>'多选','select'=>'选择框','color'=>'颜色值'];
	function index(){
		if(XG_POST){
			$data=xg_input('post.');
			$types=xg_model('config')->where('status',1)->column('type','name');
			foreach($types as $name=>$type){
				if($type=='int' or $type=='bool'){
					$value=intval($data[$name]);
				}elseif($type=='html'){
					$value=xg_input('post.'.$name,'','safehtml,dotrim');
				}elseif($type=='checkbox'){
					$value=xg_str($data[$name]);
				}else{
					$value=$data[$name];
				}
				xg_model('config')->where('name',$name)->save(['value'=>$value]);
			}
			xg_cache('site_config',null);
			xg_success('保存成功');
		}
		$config=xg_model('config')->where('status',1)->order('`order` asc,id asc')->column('*','name');
		$groups=xg_model('config')->where('status',1)->order('`order` asc,id asc')->group('group')->column('group');
		$this->assign('config',$config);
		$this->assign('type',$this->type);
		$this->assign('groups',array_values($groups));
		$this->display();
	}
	function config(){
		$id=xg_input('id/i');
		if(XG_POST){
			$data=xg_input('post.');
			if($id){
				xg_model('config')->where($id)->save($data);
			}else{
				$id=xg_model('config')->add($data);
			}
			xg_cache('site_config',null);
			xg_success('提交成功');
		}
		if($id){
			$info=xg_model('config')->find($id);
			$this->assign('info',$info);
		}
		$group=xg_model('config')->group('group')->column('group');
		$this->assign('group',$group);
		$this->assign('type',$this->type);
		$this->display();
	}
	function manage(){
		$list=xg_model('config')->select();
		$this->assign('list',$list);
		$this->assign('type',$this->type);
		$this->display();
	}
}
?>