<?php
namespace apps\admin\controller;
class theme extends base{
	function save(){
		$thid=xg_input('thid/i');
		$info=xg_model('app_theme')->find($thid);
		$savename=$info['title'].'-'.date('Ymd').'-'.date('His').'.json';
		$data=xgblock()->make_theme_data($thid);
		$data=xg_jsonstr(['xg-saved-theme'=>$data]);
		xg_download($data,$savename);
	}
	function load(){
		$cont=xg_file()->allow('json')->content();
		$data=xg_jsonarr($cont)['xg-saved-theme'];
		if(!$data)xg_error('数据错误');
		if(xgblock()->decode_theme_data($thid,$data))xg_success('导入成功');
		xg_error('导入失败');
	}
	function delete(){
		if(!$thid=xg_input('get.thid'))xg_error('请设置主题id');
		if($confirm=xg_confirm('确定删除吗？'))xg_error($confirm);
		xg_model('app_theme')->where(array('thid'=>$thid))->delete();
		xg_model('app_page')->where(array('thid'=>$thid))->delete();
		xg_model('app_block')->where(array('thid'=>$thid))->delete();
		xg_model('app_links')->where(array('thid'=>$thid))->delete();
		xg_success('删除成功');
	}
	function apptheme(){
		if(XG_POST){
			$name=xg_input('name');
			$thid=xg_input('thid');
			xg_model('app_list')->where('name','in',$name)->update(['theme_id'=>$thid]);
			xg_success('部署成功');
		}
		$this->assign('applist',xg_model('app_list')->column('title','name'));
		$this->display();
	}
	function theme(){
		$thid=xg_input('request.thid/i',0);
		if(XG_POST){
			$info=xg_input('post.');
			if(xg_model('app_theme')->where([['thid','!=',$thid],['title','=',$info['title']]])->value('thid')){
				xg_error('已存在此名称的主题');
			}
			if(!$thid){
				$info['status']=1;
				$thid=xg_model('app_theme')->add($info);
				xg_model('app_page')->add(['thid'=>$thid,'type'=>'index','title'=>'首页','name'=>'index','status'=>1]);;
			}else{
				xg_model('app_theme')->where(array('thid'=>$thid))->save($info);
			}
			xg_success('保存成功',xg_url('theme/themes'));
		}
		$info=xg_model('app_theme')->find($thid);
		$this->assign('info',$info);
		return $this->display('theme');
	}
	function themes(){
		$list=xg_model('app_theme')->select();
		$this->assign('list',$list);
		return $this->display();
	}
}
?>