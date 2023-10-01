<?php
namespace apps\admin\controller;
class link extends base{
	function init(){
		parent::init();
		$this->assign('selname',xg_input('selname'));
	}
	public function link(){
		$thid=xg_input('get.thid');
		$data=xg_model('app_links')->where('thid',$thid)->find();
		if(XG_POST){
			$post=xg_input('post.');
			if($data){
				xg_model('app_links')->where('thid',$thid)->update(array('data'=>xg_jsonstr($post)));
			}else{
				xg_model('app_links')->add(array('thid'=>$thid,'data'=>xg_jsonstr($post)));
			}
			$this->del_all_app_cache();
			xg_success('保存成功');
		}
		$pages=xg_model('app_page')->where('thid',$thid)->select();
		$addPages=xg_hooks('add-page')->run()->data();
		foreach($addPages as $k=>$v){
			foreach($v as $k2=>$v2){
				$pages[]=$v2;
			}
		}
		$data=xg_jsonarr($data['data']);
		$this->assign('data',$data);
		$this->assign('pages',$pages);
		return $this->display();
	}
	public function category(){
		$thid=xg_input('get.thid');
		$type=xg_input('get.type');
		$pid=xg_input('get.pid/i',null);
		$cid=xg_input('get.data.cid');
		if(is_null($pid)){
			$cates=xg_category();
			foreach($cates as $k=>$v){
				$cates[$k]=$v['title'];
			}
		}else{
			$cates=[];
			$cateids=xg_category($pid,'son');
			foreach($cateids as $id){
				$cates[$id]=xg_category($id,'title');
			}
		}
		return $this->display(['cid'=>$cid,'pid'=>$pid,'cates'=>$cates]);
	}
	public function categories(){
		$thid=xg_input('get.thid');
		$pid=xg_input('get.pid/i',null);
		$cids=xg_input('get.data.cids');
		if(!$cids)$cids=xg_input('get.cids',[],'xg_arr');
		if(is_null($pid)){
			$cates=xg_category();
			foreach($cates as $k=>$v){
				$cates[$k]=$v;
			}
		}else{
			$cates=[];
			$cateids=xg_category($pid,'son');
			foreach($cateids as $id){
				$cates[$id]=xg_category($id);
			}
		}
		return $this->display(['pid'=>$pid,'cids'=>$cids,'cates'=>$cates]);
	}
	public function topics(){
		$thid=xg_input('get.thid');
		$tids=xg_input('get.data.tids');
		if(!$tids)$tids=xg_input('get.tids',[],'xg_arr');
		$data=xg_model('topic')->where(['status'=>1])->order('id desc')->column('title');
		return $this->display(['tids'=>$tids,'data'=>$data]);
	}
	public function page(){
		$thid=xg_input('get.thid');
		$pnames=xg_model('app_page')->where('thid',$thid)->column('title','name');
		$pnames=xg_merge(['index'=>'首页'],$pnames);
		return $this->display(['pnames'=>$pnames]);
	}
	public function contents(){
		$thid=xg_input('get.thid');
		$id=xg_input('get.data.id');
		$tbname=xg_input('get.tbname');
		$cid=xg_input('get.cid/i',0);
		$keywords=xg_input('get.keywords');
		$page=xg_input('get.page/i',1);
		$pagesize=30;
		$cates=xg_cate_select_cont();
		if(!$cid)$cid=array_keys($cates);
		$where=[['status','=',1]];
		if($keywords)$where[]=array('title|description','like',"%$keywords%");
		$count=xg_model('content')->where('cid',$cid)->where($where)->count();
		$data=xg_model('content')->where('cid',$cid)->where($where)->page($page,$pagesize)->fields('cid,id,title,status')->contents();
		$cids=[];
		$conts=[];
		foreach($data as $v){
			$v['model']=xg_category($v['cid'],'model');
			$conts[$v['id']]=$v;
		}
		$pagehtml=xg_pagehtml($count,$pagesize);
		return $this->display(['ids'=>$ids,'cid'=>$cid,'pid'=>$cid,'keywords'=>$keywords,'cates'=>$cates,'conts'=>$conts,'pagehtml'=>$pagehtml,'tbname'=>$tbname]);
	}
	public function content(){
		$thid=xg_input('get.thid');
		$type=xg_input('get.type');
		$ids=xg_input('get.data.ids');
		$tbname=xg_input('get.tbname');
		$cid=xg_input('get.cid/i',0);
		$keywords=xg_input('get.keywords');
		$page=xg_input('get.page/i',1);
		$pagesize=30;
		$cates=xg_cate_select_cont();
		if(!$cid)$cid=array_keys($cates);
		$where=[['status','=',1]];
		if($keywords)$where[]=array('title|description','like',"%$keywords%");
		$count=xg_model('content')->where('cid',$cid)->where($where)->count();
		$data=xg_model('content')->where('cid',$cid)->where($where)->page($page,$pagesize)->fields('cid,id,title,status')->contents();
		$cids=[];
		$conts=[];
		foreach($data as $v){
			$cids[$v['id']]=$v['cid'];
			$conts[$v['id']]=$v;
		}
		$pagehtml=xg_pagehtml($count,$pagesize);
		return $this->display(['id'=>$id,'cids'=>$cids,'type'=>$type,'cid'=>$cid,'keywords'=>$keywords,'cates'=>$cates,'conts'=>$conts,'pagehtml'=>$pagehtml,'tbname'=>$tbname]);
	}
	public function dataurl(){
		$thid=xg_input('get.thid');
		$type=xg_input('get.type');
		$this->display(['thid'=>$thid,'type'=>$type]);
	}
	public function topic(){
		$thid=xg_input('get.thid');
		$type=xg_input('get.type');
		$keywords=xg_input('get.keywords');
		$page=xg_input('get.page/i',1);
		$pagesize=30;
		$where=[['status','=',1]];
		if($keywords)$where[]=array('title','like',"%$keywords%");
		$count=xg_model('topic')->where($where)->count();
		$data=xg_model('topic')->where($where)->page($page,$pagesize)->column('title');
		$pagehtml=xg_pagehtml($count,$pagesize);
		return $this->display(['type'=>$type,'keywords'=>$keywords,'pagehtml'=>$pagehtml,'data'=>$data]);
	}
}
?>