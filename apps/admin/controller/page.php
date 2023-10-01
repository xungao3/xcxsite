<?php
namespace apps\admin\controller;
class page extends base{
	function save(){
		$pid=xg_input('pid/i');
		$info=xg_model('app_page')->find($pid);
		$savename=$info['title'].'-'.date('Ymd').'-'.date('His').'.json';
		$data=xgblock()->make_page_data($pid);
		$data=xg_jsonstr(['xg-saved-page'=>$data]);
		xg_download($data,$savename);
	}
	function load(){
		$thid=xg_input('thid/i');
		$cont=xg_file()->allow('json')->content();
		$data=xg_jsonarr($cont)['xg-saved-page'];
		if(!$data)xg_error('数据错误');
		xg_cache_group('app-sets',null);
		if(xgblock()->decode_page_data($thid,$data))xg_success('导入成功');
		xg_error('导入失败');
	}
	function copy(){
		$thid=xg_input('thid/i');
		$pid=xg_input('pid/i');
		$data=xgblock()->make_page_data($pid);
		xgblock()->decode_page_data($thid,$data);
		xg_cache_group('app-sets',null);
		xg_jsonok('复制成功');
	}
	function delete(){
		if(!$thid=xg_input('get.thid'))xg_error('请设置主题id');
		if(!$pagename=xg_input('get.pagename'))xg_error('请设置页面名称');
		if($pagename=='index')xg_error('首页不能删除');
		if($confirm=xg_confirm('确定删除吗？'))xg_error($confirm);
		xg_model('app_page')->where(array('name'=>$pagename,'thid'=>$thid))->delete();
		xg_model('app_block')->where(array('pagename'=>$pagename,'thid'=>$thid))->delete();
		xg_cache_group('app-sets',null);
		xg_success('删除成功');
	}
	function page(){
		if(!$thid=xg_input('get.thid'))xg_error('请设置主题id');
		$pid=xg_input('pid');
		if($pid)$info=xg_model('app_page')->json('data')->find($pid);
		if(XG_POST){
			$post=xg_input('post.');
			if(!$post['name']){
				xg_error('请填写标识');
			}
			if(!$post['title']){
				xg_error('请填写名称');
			}
			if(preg_match('/[^0-9a-z_]/',$post['name'])){
				xg_error('标识只能填写小写英文字母，数字，下划线');
			}
			if(is_numeric($post['name'])){
				xg_error('标识不能是数字');
			}
			if(substr($post['name'],0,7)=='blocks_'){
				xg_error('标识开始不能为blocks_');
			}
			if(xg_model('app_page')->where([['title','=',$post['title']],['pid','!=',$pid],['thid','=',$thid]])->value('pid')){
				//xg_error('已存在此名称的页面');
			}
			if(xg_model('app_page')->where([['name','=',$post['name']],['pid','!=',$pid],['thid','=',$thid]])->value('pid')){
				xg_error('已存在此标识的页面');
			}
			$post['thid']=$thid;
			if(!$pid){
				$post['status']=1;
				xg_model('app_page')->json('data')->add($post);
			}else{
				xg_model('app_page')->where(array('pid'=>$pid))->json('data')->save($post);
				if($info['name']!=$post['name']){
					xg_model('app_block')->where(array('thid'=>$thid,'pagename'=>$info['name']))->save(array('pagename'=>$post['name']));
				}
			}
			xg_cache_group('app-sets',null);
			xg_success('保存成功');
		}
		$this->assign('info',$info);
		return $this->display();
	}
	function pages(){
		if(!$thid=xg_input('get.thid'))xg_error('请设置主题id');
		$list=xg_model('app_page')->where(array('thid'=>$thid))->select();
		$this->assign('list',$list);
		$this->assign('thid',$thid);
		return $this->display();
	}
	function links(){
		if(!$thid=xg_input('get.thid'))xg_error('请设置主题id');
		$links=xg_model('app_links')->where(array('thid'=>$thid))->json('data')->find();
		if(XG_POST){
			$data=xg_input('post.');
			if($links){
				xg_model('app_links')->where(array('thid'=>$thid))->json('data')->update(['data'=>$data]);
			}else{
				xg_model('app_links')->json('data')->insert(['data'=>$data,'thid'=>$thid]);
			}
			xg_cache_group('app-sets',null);
			xg_success('保存成功');
		}
		$pages=xg_model('app_page')->where(array('thid'=>$thid))->select();
		$this->assign('links',$links['data']);
		$this->assign('pages',$pages);
		$this->assign('thid',$thid);
		return $this->display();
	}
}
?>