<?php
namespace apps\app\controller;
class index extends base{
	protected $sys=null;
	function init(){
		parent::init();
		$sys=xg_input('sys');
		if($sys)$this->sys=xg_sys($sys);
	}
	function index(){
		return $this->data();
	}
	function sets(){
		$thid=xg_input('get.thid/i');
		$sets=xg_sets($thid);
		xg_jsonok($sets);
	}
	function search(){
		if(!$this->sys)return;
		return $this->sys->search();
	}
	function upload(){
		if(!$this->sys)return;
		return $this->sys->upload();
	}
	function data(){
		if(!$this->sys)return;
		return $this->sys->data();
	}
	function star(){
		if(!$this->sys)return;
		return $this->sys->star();
	}
	function comment(){
		if(!$this->sys)return;
		return $this->sys->comment();
	}
	function cart(){
		if(!$this->sys)return;
		return $this->sys->cart();
	}
	function cart_items(){
		if(!$this->sys)return;
		return $this->sys->cart_items();
	}
	function comments(){
		if(!$this->sys)return;
		return $this->sys->comments();
	}
	function options(){
		if(!$this->sys)return;
		return $this->sys->options();
	}
	function submit(){
		if(!$this->sys)return;
		return $this->sys->submit();
	}
	function nav(){
		$bid=xg_input('get.bid/i');
		$app=xg_input('appname');
		$reqsys=xg_input('sys','xg');
		if(!$bid)return;
		$sys=new \xg\sys();
		$sys->sys=$reqsys;
		$sys->type='nav';
		$sys->name='nav-'.$bid;
		$md5=$sys->md5();
		$file=$sys->file();
		$path=$sys->path();
		//if($sys->cache and file_exists($path))return $sys->load();
		$data=xg_nav_tree(0);
		$fun=function($data)use(&$fun){
			foreach($data as $k=>$v){
				$children=[];
				if($v['children'])$children=$fun($v['children']);
				if($v['type']=='category'){
					$data[$k]=['title'=>$v['title'],'cid'=>$v['infoid'],'children'=>$children];
				}else{
					$data[$k]=['title'=>$v['title'],'cid'=>$v['cateid'],'id'=>$v['infoid'],'children'=>$children];
				}
			}
			return $data;
		};
		$data=$fun($data);
		$run=xg_hooks('cache-content')->run($data)->last();
		if($run)$data=$run;
		if($app!='preview'){
			$sys->cache(['type'=>$sys->type,'sys'=>$sys->sys,'infoid'=>$bid,'md51'=>$md5[0],'md52'=>$md5[1],'file'=>$file]);
			$data=$sys->save($data);
		}
		return $data;
	}
}